<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\Fixtures\ProductFixture;
use Tests\Functional\WebTestCase;

class ProductCRUDUpdateControllerTest extends WebTestCase
{
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
        $this->assertSame($newPrice, $content['result']['price']);
    }
}
