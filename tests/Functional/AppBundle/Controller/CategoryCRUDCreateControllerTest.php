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

    public function testCreateWithParent()
    {
        $name = 'Foo';
        $parentName = 'Bar';

        $client = static::createClient();

        $client->request('POST', '/api/categories?includes=parent', [], [], [], json_encode([
            'name' => $name,
            'parent' => [
                'name' => $parentName,
            ],
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content['result']);

        $result = $content['result'];
        $this->assertArrayHasKeys(['id', 'parent'], $result);

        $this->assertNotNull($result['id']);
        $this->assertNotNull($result['parent']['id']);

        $this->assertSame($name, $result['name']);
        $this->assertSame($parentName, $result['parent']['name']);
    }

    public function testCreateWithChild()
    {
        $name = 'Foo';
        $childName = 'Bar';

        $client = static::createClient();

        $client->request('POST', '/api/categories?includes=children', [], [], [], json_encode([
            'name' => $name,
            'children' => [
                [
                    'name' => $childName,
                ]
            ],
        ]));

        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $content['result']);

        $result = $content['result'];
        $this->assertArrayHasKeys(['id', 'name', 'children'], $result);

        $this->assertNotNull($result['id']);
        $this->assertSame($name, $result['name']);
        $this->assertCount(1, $result['children']);

        $child = $result['children'][0];

        $this->assertNotNull($child['id']);
        $this->assertSame($childName, $child['name']);
    }
}
