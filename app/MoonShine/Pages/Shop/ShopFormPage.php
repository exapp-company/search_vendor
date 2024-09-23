<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Shop;

use App\Enums\ShopStatus;
use App\Models\Log;
use App\MoonShine\Pages\Office\OfficeFormPage;
use App\MoonShine\Pages\Office\OfficeIndexPage;
use App\MoonShine\Resources\OfficeResource;
use App\MoonShine\Resources\UserResource;
use Dotenv\Parser\Value;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL as FacadesURL;
use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\ActionGroup;
use MoonShine\Components\FlexibleRender;
use MoonShine\Components\FormBuilder;
use MoonShine\Components\Modal;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\When;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Fragment;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Enums\Layer;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Json;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Select;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Fields\Url;
use MoonShine\Metrics\ValueMetric;
use MoonShine\MoonShineRequest;
use MoonShine\Pages\PageComponents;
use Throwable;

class ShopFormPage extends FormPage
{
    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        $feed_fields = [
            "name" => "Название товара",
            "url" => "URL товара",
            "price" => "Цена товара",
            "picture" => "Изображение товара",
            "in_stock" => "Наличие товара",
            "sku" => "Артикул товара",
            "amount" => "Количество товара",
            "wholesale_price" => "Оптовая цена"
        ];
        $mapping_fields = [];

        foreach ($feed_fields as $key => $value) {
            $mapping_fields[] = Flex::make(
                [
                    // Column::make([])->columnSpan(6),
                    Text::make($value, "feed_mapping.$key.name", fn($shop) => Arr::get($shop->feed_mapping, "$key.name"))
                        ->default($key)
                        ->onApply(function (Model $item, $value, Field $field) {
                            $mapping = $item->feed_mapping;
                            $field = Str::replaceStart("feed_mapping.", "", $field->column());
                            Arr::set($mapping, $field, $value);
                            $item->feed_mapping = $mapping;
                            $item->save();
                        })->unescape(),
                    Select::make(null, "feed_mapping.$key.type")
                        ->default("field")
                        ->options([
                            "field" => "Тег",
                            "attribute" => "Атрибут"
                        ])
                        ->onApply(function (Model $item, $value, Field $field) {
                            $mapping = $item->feed_mapping;
                            $field = Str::replaceStart("feed_mapping.", "", $field->column());
                            Arr::set($mapping, $field, $value);
                            $item->feed_mapping = $mapping;
                            $item->save();
                        })
                ]
            )->itemsAlign("end");
        }
        // $officesComponent = (new OfficeFormPage())->fields();
        // $officesComponent[] = Hidden::make(null, "shop_id")->default($this->getResource()->getItemID());
        //dd($this->getResource()->getItem()->getKey());
        return [
            Grid::make(
                [
                    Column::make([
                        Block::make(
                            [
                                Heading::make("Основная информация")->h(2),
                                Flex::make([
                                    Text::make(null, "name")->placeholder("Название"),
                                    Url::make(null, "website")->placeholder("Сайт")
                                ]),
                                Flex::make([
                                    BelongsTo::make("Город", "city", fn($value) => $value->name)->searchable()->creatable(),
                                    BelongsTo::make("Владелец", "supplier", fn($value) => $value->email, new UserResource())->nullable()->searchable(),

                                ])->itemsAlign("end"),
                                Textarea::make(null, "description")->default("Описание")
                            ]
                        )

                    ])->columnSpan(6),
                    Column::make([

                        Block::make(
                            [
                                Heading::make("Статистика и управление")->h(2),
                                Enum::make("Статус", 'status', function ($shop) {
                                    return ShopStatus::readable($shop->status);
                                })->attach(ShopStatus::class)->onChangeMethod(
                                    "changeStatus",
                                    function () {
                                        return ['shop' => $this->getResource()->getItemID()];
                                    }
                                )->default($this->getResource()->getItem()?->status ?? "pending"),
                                Switcher::make("Рекомендовано", "recommended"),
                                When::make(
                                    fn() =>  $this->getResource()->getItemID(),
                                    fn() => [
                                        Flex::make([
                                            ValueMetric::make("Филиалы")
                                                ->value(fn() => $this->getResource()->getItem()?->offices()->count()),
                                            ValueMetric::make("Товары")
                                                ->value(fn() => $this->getResource()->getItem()?->products()->count())
                                        ])->justifyAlign('start')
                                            ->itemsAlign('start'),
                                        Divider::make(),
                                        ActionGroup::make(
                                            [
                                                ActionButton::make("Импорт")
                                                    ->method(
                                                        "importProducts",
                                                        params: ['resourceItem' => $this->getResource()->getItemID()]
                                                    )
                                                    ->async("POST"),
                                                ActionButton::make("Обновить индекс")
                                                    ->method(
                                                        "reindexProducts",
                                                        params: ['resourceItem' => $this->getResource()->getItemID()]
                                                    )
                                                    ->async("POST")
                                            ]
                                        ),

                                    ],
                                    fn() => [
                                        FlexibleRender::make("Статистика и управление будет доступно после создания магазина")
                                    ]
                                )

                            ]
                        )
                    ])->columnSpan(6),
                    Column::make([
                        Block::make("Настройки фида", [
                            Flex::make(
                                '',
                                [
                                    Url::make("Ссылка на фид", "feed_url")->placeholder("URL фида"),
                                    Select::make("Формат фида", "feed_type")->options(
                                        [
                                            "xml" => "XML",
                                            "json" => "JSON"
                                        ],
                                    )->default("json")
                                ]
                            ),
                            Flex::make('', [
                                Switcher::make("Используются поддомены", "has_subdomain")->default(false),
                                Text::make("Корневой элемент", "feed_item")->default("offer")
                            ]),
                            Divider::make(),
                            Heading::make("Настройки маппинга")->h(3),
                            ...$mapping_fields,

                        ])
                    ])->columnSpan(12)
                ]
            ),
            HasMany::make('', "offices", resource: new OfficeResource())
                ->creatable(
                    true,
                    ActionButton::make("Добавить филиал")
                ) //->beforeApply(fn(Model &$model) => $model->shop_id = $this->getResource()->getItemID())
            // Tabs::make(
            //     [
            //         Tab::make("Магазин", [])->active(),
            //         Tab::make("Филиалы", [
            //             HasMany::make("Филиалы", "offices", resource: new OfficeResource())
            //                 ->creatable(
            //                     true,
            //                     ActionButton::make("Добавить филиал")
            //                 )
            //         ])
            //     ]
            // ),


        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<MoonShineComponent>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
    public function title(): string
    {
        return $this->getResource()?->getItem()?->name ?? "Новый магазин";
    }
    public function components(): array
    {
        //$this->getResource()->getItemID()  - id текущей записи PostResource
        //если нет идентификтора, значит нам нужно стандартное поведение при добавлении записи
        if (! $this->getResource()->getItemID()) {
            return parent::components();
        }

        $bottomComponents = $this->getLayerComponents(Layer::BOTTOM);

        $officesComponent = collect($bottomComponents)->filter(fn($component) => $component->getName() === 'offices')->first();

        //сортируем по табам
        $tabLayer = [
            Tabs::make([
                Tab::make('Редактирование', $this->mainLayer()),

                Tab::make('Филиалы', [$officesComponent]),
            ])
        ];

        return [
            ...$this->getLayerComponents(Layer::TOP),
            ...$tabLayer,
        ];
    }
}
