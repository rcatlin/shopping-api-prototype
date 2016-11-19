<?php

namespace Tests\Functional\AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\ProductFixture;
use Tests\Fixtures\RetailerFixture;
use Tests\Functional\WebTestCase;

class RetailerCRUDGetControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testGet()
    {
        $client = self::createClient();

        $name = 'Foo';
        $url = 'http://www.foo.com/';

        $product = (new RetailerFixture($name, $url))->load(self::getEntityManager());

        $client->request('GET', '/api/retailers/' . $product->getId()->toString());

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']);

        $this->assertNotNull($content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
        $this->assertSame($url, $content['result']['url']);
    }

    public function testGetWithProductsIncludesContainsProducts()
    {
        $client = self::createClient();

        $retailer = (new RetailerFixture(
            'Foo',
            5555,
            new ArrayCollection([
                (new ProductFixture('Bar', 88888))->load(self::getEntityManager()),
            ])
        ))->load(self::getEntityManager());

        $client->request('GET', '/api/retailers/' . $retailer->getId()->toString() . '?includes=products');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'url', 'products'], $content['result']);
        $this->assertCount(1, $content['result']['products']);
    }
}
