<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\ShopStatus;
use App\Jobs\ImportProductJob;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\MoonShine\Pages\Shop\ShopIndexPage;
use App\MoonShine\Pages\Shop\ShopFormPage;
use App\MoonShine\Pages\Shop\ShopDetailPage;
use App\Services\StatusService;
use MoonShine\Attributes\SearchUsingFullText;
use MoonShine\Components\Badge;
use MoonShine\Decorations\Block;
use MoonShine\Enums\ToastType;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Http\Responses\MoonShineJsonResponse;
use MoonShine\MoonShineRequest;
use MoonShine\Resources\ModelResource;
use MoonShine\Pages\Page;

use function Clue\StreamFilter\fun;

/**
 * @extends ModelResource<Shop>
 */
class ShopResource extends ModelResource
{
    protected string $model = Shop::class;

    protected string $title = 'Магазины';

    protected array $with = ['supplier', 'city'];

    /**
     * @return list<Page>
     */

    public function fields(): array
    {
        return [
            ID::make("ID", 'id')->sortable(),
            Badge::make("df", "purple"),
            Text::make("Название", "name"),
            Text::make("Статус", "status", function ($shop) {
                return ShopStatus::readable($shop->status);
            })->badge(function ($shop) {
                return match ($shop) {
                    'active' => 'green',
                    'inactive' => 'gray',
                    'pending' => 'purple',
                    'rejected' => 'red',
                    default => null,
                };
            }),
            Text::make("Импорт", "import_progress", fn($shop) => __("shop.import." . $shop->import_progress))
                ->badge(fn($value) => match ($value) {
                    'active' => 'blue',
                    'failed' => 'red',
                    'success' => 'green',
                    'canceled' => 'yellow',
                    default => 'gray'
                }),

            HasMany::make("Филиалы", "offices", resource: new OfficeResource())->hideOnIndex()->creatable(true)
            // Block::make(

            // )
        ];
    }
    public function pages(): array
    {
        return [
            ShopIndexPage::make($this->title()),
            ShopFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            //ShopDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    /**
     * @param Shop $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }

    public function changeStatus(MoonShineRequest $request)
    {
        $shop = Shop::find($request->shop);
        $stService = new StatusService();
        $stService->changeStatus($shop, ShopStatus::from($request->value));
        return $shop->id;
    }
    public function importProducts(MoonShineRequest $request)
    {
        $shop = $this->getItem();
        ImportProductJob::dispatch($shop);
        return MoonShineJsonResponse::make()->toast("Магазин поставлен в очередь на импорт", ToastType::SUCCESS);
    }
    public function reindexProducts(MoonShineRequest $request)
    {
        $shop = $this->getItem();
        $shop->products()->searchable();
        return MoonShineJsonResponse::make()->toast("Товары магазины поставлены на переидексацию", ToastType::SUCCESS);
    }
    #[SearchUsingFullText(['name'])]
    public function search(): array
    {
        return ['name'];
    }
}
