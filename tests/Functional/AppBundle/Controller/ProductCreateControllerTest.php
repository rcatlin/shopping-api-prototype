<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductCreateControllerTest extends WebTestCase
{
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
}
