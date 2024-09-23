<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Services\Geo\YandexGeoService;
use Tests\TestCase;

class YandexGeoServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_get_coors()
    {
        $yaService = new YandexGeoService();
        $yaService->getCoors("Россия, Благовещенск");
    }
}
