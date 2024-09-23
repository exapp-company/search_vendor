<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Suggest;
use App\MoonShine\Pages\Suggest\SuggestIndexPage;
use App\MoonShine\Pages\Suggest\SuggestFormPage;
use App\MoonShine\Pages\Suggest\SuggestDetailPage;

use MoonShine\Fields\ID;
use MoonShine\Fields\Number;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Text;
use MoonShine\Resources\ModelResource;
use MoonShine\Pages\Page;

/**
 * @extends ModelResource<Suggest>
 */
class SuggestResource extends ModelResource
{
    protected string $model = Suggest::class;

    protected string $title = 'Запросы';

    /**
     * @return list<Page>
     */
    public function pages(): array
    {
        return [
            SuggestIndexPage::make($this->title()),
            SuggestFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            SuggestDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    /**
     * @param Suggest $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
    public function fields(): array
    {
        return [
            ID::make("ID", 'id')->badge("purple")->sortable()->useOnImport()->showOnExport(),
            Text::make("Запрос", "query")->readonly()->useOnImport()->showOnExport(),
            Slug::make("URL", "slug")->useOnImport()->showOnExport(),
            Number::make("Кол-во", "count")->sortable()->readonly()->useOnImport()->showOnExport(),
            Number::make("Кол-во результатов", "result_count")->sortable()->readonly()->useOnImport()->showOnExport(),
        ];
    }
}
