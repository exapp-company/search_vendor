<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Services\Documents\JSONParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ParserTest extends TestCase
{
    public static function validateOfferDataProvider(): array
    {
        return [
            "Правильный товар" => [
                [
                    "url" => "http://test.ru",
                    "name" => "some name",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                    "in_stock" => true,
                    "sku" => 123,
                    "amount" => 9,
                    "wholesale_price" => 100
                ],
                true
            ],
            "Правильный null товар" => [
                [
                    "url" => "http://test.ru",
                    "name" => "some name",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                    "in_stock" => true,
                    "sku" => null,
                    "amount" => null,
                    "wholesale_price" => null
                ],
                true
            ],
            "Правильный упрощенный товар" => [
                [
                    "url" => "http://test.ru",
                    "name" => "some name",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                    "in_stock" => true,
                ],
                true
            ],
            "товар без названия" => [
                [
                    "url" => "http://test.ru",
                    "name" => "",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                    "in_stock" => true,

                ],
                false
            ],
            "товар без урла" => [
                [
                    "url" => "",
                    "name" => "dgfd",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                    "in_stock" => true,

                ],
                false
            ],
            "товар без цены" => [
                [
                    "url" => "http://test.ru/image",
                    "name" => "dgfd",
                    "picture" => "http://test.ru/image",
                    "price" => null,
                    "in_stock" => true,

                ],
                false
            ],
            "товар с нулевой ценой" => [
                [
                    "url" => "http://test.ru/image",
                    "name" => "dgfd",
                    "picture" => "http://test.ru/image",
                    "price" => 0,
                    "in_stock" => true,

                ],
                false
            ],
            "товар с огромный ценой ценой" => [
                [
                    "url" => "http://test.ru/image",
                    "name" => "dgfd",
                    "picture" => "http://test.ru/image",
                    "price" => 9999999999999999,
                    "in_stock" => true,

                ],
                false
            ],
            "товар без наличия" => [
                [
                    "url" => "http://test.ru/image",
                    "name" => "dgfd",
                    "picture" => "http://test.ru/image",
                    "price" => 1000,
                ],
                false
            ],
            "null товар" => [
                null,
                false
            ],
            "товар из xml" => [
                [
                    'sku' => NULL,
                    'url' => 'https://store.notebook1.ru/catalog/products/7208218/',
                    'name' => 'Рамка экрана ноутбука Asus X550',
                    'price' => '600.00',
                    'amount' => NULL,
                    'picture' => 'https://store.notebook1.ru/upload/iblock/cfe/0vtt7cur64xbi87uup7sz8jh9hsswq1o.jpg',
                    'in_stock' => 'true',
                    'wholesale_price' => NULL,
                ],
                true
            ]
        ];
    }
    #[DataProvider('validateOfferDataProvider')]
    public function test_validateOffer(?array $node, bool $expected): void
    {
        $shop = new Shop();
        $parser = new JSONParser($shop);
        $this->assertSame($expected, $parser->validateOffer($node));
    }
}
