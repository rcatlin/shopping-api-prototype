<?php

namespace Tests\Functional\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Fixtures\ProductFixture;

class ProductDeleteControllerTest extends WebTestCase
{
    public function testDelete()
    {
        $client = self::createClient();

        $container = $this->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        $product = (new ProductFixture('Foo', 5555))->load($entityManager);
        $productUrl = '/api/products/' . $product->getId()->toString();

        $client->request('DELETE', $productUrl);
        $response = $client->getResponse();
        $this->assertSame(204, $response->getStatusCode());
    }
}
