<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Office;
use App\MoonShine\Pages\Office\OfficeIndexPage;
use App\MoonShine\Pages\Office\OfficeFormPage;
use App\MoonShine\Pages\Office\OfficeDetailPage;
use App\Services\StatusService;
use MoonShine\Fields\Enum;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\Fields\Text;
use MoonShine\MoonShineRequest;
use MoonShine\Resources\ModelResource;
use MoonShine\Pages\Page;
use MoonShine\Traits\Resource\ResourceWithParent;

/**
 * @extends ModelResource<Office>
 */
class OfficeResource extends ModelResource
{
    use ResourceWithParent;
    protected string $model = Office::class;

    protected string $title = 'Филиалы';

    public function fields(): array
    {
        return [
            ID::make("ID", "id")->badge("purple"),
            Text::make("Название", "name"),
            BelongsTo::make("Город", "city", fn($city) => $city->name),
            Enum::make("Статус", 'status', function ($shop) {
                return ShopStatus::readable($shop->status);
            })->attach(ShopStatus::class)->onChangeMethod(
                "changeStatus",
                function () {
                    return ['office' => $this->getItemID()];
                }
            )->default($this->getItem()?->status ?? "pending")->badge(),
        ];
    }

    /**
     * @return list<Page>
     */
    public function pages(): array
    {
        return [
            OfficeIndexPage::make($this->title()),
            OfficeFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),

        ];
    }

    /**
     * @param Office $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
    public function search(): array
    {
        return ['title'];
    }
    public function changeStatus(MoonShineRequest $request)
    {
        $shop = Office::find($request->office);
        $stService = new StatusService();
        $stService->changeStatus($shop, ShopStatus::from($request->value));
        return $shop->id;
    }
    protected function getParentResourceClassName(): string
    {
        return ShopResource::class;
    }

    protected function getParentRelationName(): string
    {
        return 'shop';
    }
}
