<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\CategoryFixture;
use Tests\Functional\WebTestCase;

class CategoryCRUDEditControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testEdit()
    {
        $client = self::createClient();

        $category = (new CategoryFixture('Foo'))->load(self::getEntityManager());

        $categoryId = $category->getId()->toString();

        $categoryUrl = '/api/categories/' . $categoryId;

        $newName = 'Bar';

        $client->request('PUT', $categoryUrl, [], [], [], json_encode([
            'name' => $newName,
        ]));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name'], $content['result']);

        $this->assertSame($categoryId, $content['result']['id']);
        $this->assertSame($newName, $content['result']['name']);
    }
}
