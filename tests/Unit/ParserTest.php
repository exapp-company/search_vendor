<?php

namespace Tests\Unit;

use App\Models\Shop;
use App\Services\Documents\JSONParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public static function urlSubdomainProvider(): array
    {
        return [
            "Обычный урл" => [
                [
                    "url" => "https://shop.ru/some-path/"
                ],
                "https://shop.ru/some-path/"
            ],
            "Урл с поддоменном" => [
                [
                    "url" => "https://test.shop.ru/some-path/"
                ],
                "https://shop.ru/some-path/"
            ],
            "Url c параметром" => [
                [
                    "url" => "https://shop.ru/some-path/?query=1&san=2"
                ],
                "https://shop.ru/some-path/?query=1&san=2"
            ],
            "Url c поддоменом и параметром" => [
                [
                    "url" => "https://test.shop.ru/some-path/?query=1&san=2"
                ],
                "https://shop.ru/some-path/?query=1&san=2"
            ],
        ];
    }

    #[DataProvider('urlSubdomainProvider')]
    public function test_urlSubdomain(array $product, $expected): void
    {
        $shop = new Shop([
            'has_subdomain' => true
        ]);
        $parser = new JSONParser($shop);

        $key = $parser->getKey($product);

        $this->assertSame($expected, $parser->getKey($product));
        print $key;
    }
}
