<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\Fixtures\RetailerFixture;
use Tests\Functional\WebTestCase;

class RetailerCRUDDeleteControllerTest extends WebTestCase
{
    public function testDelete()
    {
        $client = self::createClient();

        $retailer = (new RetailerFixture('Foo', 'http://www.foo.com/'))->load(self::getEntityManager());

        $client->request('DELETE', '/api/retailers/' . $retailer->getId()->toString());

        $response = $client->getResponse();

        $this->assertSame(204, $response->getStatusCode());

    }
}
