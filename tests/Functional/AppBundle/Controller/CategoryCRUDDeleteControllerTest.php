<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\Fixtures\CategoryFixture;
use Tests\Functional\WebTestCase;

class CategoryCRUDDeleteControllerTest extends WebTestCase
{
    public function testDelete()
    {
        $client = self::createClient();

        $category = (new CategoryFixture('Foo'))->load(self::getEntityManager());
        $categoryUrl = '/api/categories/' . $category->getId()->toString();

        $client->request('DELETE', $categoryUrl);
        $response = $client->getResponse();
        $this->assertSame(204, $response->getStatusCode());
    }
}
