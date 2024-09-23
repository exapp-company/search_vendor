<?php

namespace App\Console\Commands;

use App\Jobs\SitemapGeneratorJob;
use Spatie\Sitemap\SitemapGenerator;
use Illuminate\Console\Command;
use App\Models\Page;
use App\Models\Product;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация сайтмапа';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        config(['app.url' => "https://poiskzip.ru/"]);
        SitemapGenerator::create("https://poiskzip.ru/")
            ->setUrl("https://poiskzip.ru")
            ->getSitemap()
            ->add(Page::where('is_published', 1)->get())
            ->writeToFile(config('app.sitemap_path'));
    }
}
