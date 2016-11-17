<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\ProductFixture;
use Tests\Functional\WebTestCase;

class ProductCRUDGetControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testGet()
    {
        $client = self::createClient();

        $name = 'Foo';
        $price = 9999;

        $product = (new ProductFixture($name, $price))->load(self::getEntityManager());

        $client->request('GET', '/api/products/' . $product->getId()->toString());

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'price'], $content['result']);

        $this->assertNotNull($content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
        $this->assertSame($price, $content['result']['price']);
    }
}
