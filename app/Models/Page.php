<?php

namespace App\Models;

use App\Traits\HasCity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Page extends Model implements Sitemapable
{
    use HasFactory, HasCity;
    public $fillable = ["title", "pagetitle", "description", "introtext", "query", "slug", "is_published", "city_id"];

    public function toSitemapTag(): Url|string|array
    {
        return Url::create('https://poiskzip.ru/search/' . $this->slug)
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0);
    }
}
