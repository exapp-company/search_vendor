<?php

namespace App\Jobs;

use App\Models\Page;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Sitemap\SitemapGenerator;

class SitemapGeneratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        config(['app.url' => "https://poiskzip.ru/"]);
        SitemapGenerator::create("https://poiskzip.ru/")
            ->maxTagsPerSitemap(5000)
            ->setUrl("https://poiskzip.ru")
            ->getSitemap()
            ->add(Page::where('is_published', 1)->get())
            ->add(Product::all())

            ->writeToFile(config('app.sitemap_path'));
    }
}
