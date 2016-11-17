<?php

namespace AppBundle\Tests\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Functional\WebTestCase;

class RetailerCRUDCreateControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testCreate()
    {
        $name = 'Foo';
        $url = 'http://www.foo.com/';

        $client = static::createClient();

        $client->request('POST', '/api/retailers', [], [], [], json_encode([
            'name' => $name,
            'url' => $url,
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']);

        $this->assertNotNull($content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
        $this->assertSame($url, $content['result']['url']);
    }
}
