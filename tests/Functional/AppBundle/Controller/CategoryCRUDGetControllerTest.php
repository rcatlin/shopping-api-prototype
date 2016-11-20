<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\CategoryFixture;
use Tests\Functional\WebTestCase;

class CategoryCRUDGetControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testGet()
    {
        $client = self::createClient();

        $name = 'Foo';

        $category = (new CategoryFixture($name))->load(self::getEntityManager());

        $client->request('GET', '/api/categories/' . $category->getId()->toString());

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name'], $content['result']);

        $this->assertNotNull($content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
    }
}
