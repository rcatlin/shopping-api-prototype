<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\ProductFixture;
use Tests\Functional\WebTestCase;

class ProductCRUDUpdateControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testPartiallyUpdate()
    {
        $client = self::createClient();

        $product = (new ProductFixture('Foo', 3333))->load(self::getEntityManager());

        $newPrice = 99999;

        $client->request('PATCH', '/api/products/' . $product->getId()->toString(), [], [], [], json_encode([
            'price' => $newPrice,
        ]));

        $response = $client->getResponse();

        $this->assertSame(202, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('price', $content['result']);
        $this->assertSame($product->getId()->toString(), $content['result']['id']);
        $this->assertSame($newPrice, $content['result']['price']);
    }

    public function testPartialUpdateWithNewCategory()
    {
        $client = self::createClient();

        $product = (new ProductFixture('Foo', 5555))->load($this->getEntityManager());

        $productId = $product->getId()->toString();

        $categoryName = 'Qux';

        $client->request('PATCH', '/api/products/' . $productId . '?includes=category', [], [], [], json_encode([
            'category' => [
                'name' => $categoryName,
            ],
        ]));
        $response = $client->getResponse();

        $this->assertSame(202, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'category'], $content['result']);


        $this->assertSame($productId, $content['result']['id']);
        $this->assertNotNull($content['result']['category']['id']);
        $this->assertSame($categoryName, $content['result']['category']['name']);
    }
}
