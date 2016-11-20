<?php

namespace AppBundle\Tests\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Functional\WebTestCase;

class CategoryCRUDCreateControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testCreate()
    {
        $name = 'Foo';

        $client = static::createClient();

        $client->request('POST', '/api/categories', [], [], [], json_encode([
            'name' => $name,
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content['result']);

        $result = $content['result'];
        $this->assertArrayHasKey('id', $result);

        $this->assertNotNull($result['id']);
        $this->assertSame($name, $result['name']);
    }
}
