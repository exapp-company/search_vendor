<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Office;

use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Components\When;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Heading;
use MoonShine\Fields\Email;
use MoonShine\Fields\Enum;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Fields\Number;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Template;
use MoonShine\Fields\Text;
use MoonShine\Fields\Url;
use MoonShine\Traits\Resource\ResourceWithParent;
use Throwable;

class OfficeFormPage extends FormPage
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
        return [
            Grid::make([
                Column::make([
                    Block::make("Информация", [
                        Hidden::make(null, 'shop_id'),
                        Flex::make([
                            Text::make("Название", "name")->required()->placeholder("На пушкина"),
                            Phone::make("Телефон", "phone")->placeholder("+7 (999) 999-99-99")->mask('+7 (999) 999-99-99'),

                        ]),
                        Flex::make([
                            Email::make("Email", "email")->placeholder("info@poiskzip.ru"),
                            Text::make("Адрес", "address")->placeholder("ул Пушкина дом Колотушкина")
                        ]),
                        Enum::make("Статус", 'status', function ($shop) {
                            return ShopStatus::readable($shop->status);
                        })->attach(ShopStatus::class)->onChangeMethod(
                            "changeStatus",

                            function () {
                                return ['office' => $this->getResource()->getItemID()];
                            },
                            resource: $this->getResource()
                        )->default($this->getResource()->getItem()?->status ?? "pending"),
                    ])
                ])->columnSpan(6),
                Column::make([
                    Block::make("Местоположение", [
                        BelongsTo::make("Город", "city", fn($city) => $city->name)->creatable(),
                        Flex::make([
                            Number::make("Долгота", "lon")->step(0.000001),
                            Number::make("Широта", "lat")->step(0.000001)
                        ])
                    ]),

                ])->columnSpan(6),
                Column::make([
                    Block::make("Настройки фида", [
                        Switcher::make("Использовать фид магазина", "use_parent_feed")->default(true),
                        Flex::make(
                            '',
                            [
                                Url::make("Ссылка на фид", "feed_url")->placeholder("URL фида"),
                                Select::make("Формат фида", "feed_type")->options(
                                    [
                                        "xml" => "XML",
                                        "json" => "JSON"
                                    ],
                                )->default("xml")
                            ]
                        ),


                        Divider::make(),
                        Switcher::make("Использовать маппинг магазина", "use_parent_mapping")->default(true),
                        Heading::make("Настройки маппинга")->h(3),
                        ...$mapping_fields,

                    ])
                ])->columnSpan(12)

            ])
            // Block::make("Основное"б х
            // )
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
}
