<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\Fixtures\CategoryFixture;
use Tests\Functional\WebTestCase;

class CategoryCRUDUpdateControllerTest extends WebTestCase
{
    public function testPartiallyUpdate()
    {
        $client = self::createClient();

        $category = (new CategoryFixture('Foo'))->load(self::getEntityManager());

        $newName = 'Bar';

        $client->request('PATCH', '/api/categories/' . $category->getId()->toString(), [], [], [], json_encode([
            'name' => $newName
        ]));

        $response = $client->getResponse();

        $this->assertSame(202, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('name', $content['result']);
        $this->assertSame($category->getId()->toString(), $content['result']['id']);
        $this->assertSame($newName, $content['result']['name']);
    }
}
