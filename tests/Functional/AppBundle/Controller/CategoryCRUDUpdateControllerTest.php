<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\CategoryFixture;
use Tests\Functional\WebTestCase;

class CategoryCRUDUpdateControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

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

    public function testPartiallyUpdateWithParent()
    {
        $client = self::createClient();

        $child = (new CategoryFixture('Foo'))->load(self::getEntityManager());
        $parent = (new CategoryFixture('Bar'))->load(self::getEntityManager());

        $childId = $child->getId()->toString();
        $parentId = $parent->getId()->toString();

        $client->request('PATCH', '/api/categories/' . $childId . '?includes=parent', [], [], [], json_encode([
            'parent' => [
                'id' => $parent->getId()->toString(),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertSame(202, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['name', 'parent'], $content['result']);
        $this->assertArrayHasKey('id', $content['result']['parent']);

        $this->assertSame($childId, $content['result']['id']);
        $this->assertSame($parentId, $content['result']['parent']['id']);
    }
}
