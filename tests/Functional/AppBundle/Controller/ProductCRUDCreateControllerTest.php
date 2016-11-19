<?php

namespace AppBundle\Tests\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Functional\WebTestCase;

class ProductCRUDCreateControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testCreate()
    {
        $name = 'Foo';
        $price = 5000;

        $client = static::createClient();

        $client->request('POST', '/api/products', [], [], [], json_encode([
            'name' => $name,
            'price' => $price,
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content['result']);

        $result = $content['result'];
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

        $this->assertNotNull($result['id']);
        $this->assertSame($name, $result['name']);
        $this->assertSame($price, $result['price']);
    }

    public function testCreateWithNewRetailerAndRetailerIncludes()
    {
        $name = 'Foo';
        $price = 5000;
        $retailerName = 'Bar';
        $retailerUrl = 'http://www.bar.com/';

        $client = static::createClient();

        $client->request('POST', '/api/products?includes=retailer', [], [], [], json_encode([
            'name' => $name,
            'price' => $price,
            'retailer' => [
                'name' => $retailerName,
                'url' => $retailerUrl,
            ],
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'retailer'], $content['result']);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']['retailer']);

        $this->assertNotNull($content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
        $this->assertSame($price, $content['result']['price']);
        $this->assertNotNull($content['result']['retailer']['id']);
        $this->assertNotNull($retailerName, $content['result']['retailer']['name']);
        $this->assertNotNull($retailerUrl, $content['result']['retailer']['url']);
    }
}
