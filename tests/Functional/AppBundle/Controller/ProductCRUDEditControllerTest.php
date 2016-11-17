<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\Fixtures\ProductFixture;
use Tests\Functional\WebTestCase;

class ProductCRUDEditControllerTest extends WebTestCase
{
    public function testEdit()
    {
        $client = self::createClient();

        $container = $this->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        $product = (new ProductFixture('Foo', 5555))->load($entityManager);

        $productId = $product->getId()->toString();

        $productUrl = '/api/products/' . $productId;

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => 'Bar',
            'price' => 6666
        ]));
        $response = $client->getResponse();
;
        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('id', $content['result']);
        $this->assertArrayHasKey('name', $content['result']);
        $this->assertArrayHasKey('price', $content['result']);

        $this->assertSame($productId, $content['result']['id']);
        $this->assertSame('Bar', $content['result']['name']);
        $this->assertSame(6666, $content['result']['price']);
    }
}
