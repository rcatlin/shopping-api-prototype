<?php

namespace Tests\Functional\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Fixtures\ProductFixture;

class ProductEditControllerTest extends WebTestCase
{
    public function testEdit()
    {
        $client = self::createClient();

        $container = $this->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        $product = (new ProductFixture('Foo', 5555))->load($entityManager);
        $productUrl = '/api/products/' . $product->getId()->toString();

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => 'Bar',
            'price' => 6666
        ]));
        $response = $client->getResponse();
        $this->assertSame(201, $response->getStatusCode());
    }
}
