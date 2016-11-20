<?php

namespace Functional\AppBundle\Controller;

use Tests\Fixtures\RetailerFixture;
use Tests\Functional\WebTestCase;

class RetailerCRUDUpdateControllerTest extends WebTestCase
{
    public function testPartiallyUpdateRetailer()
    {
        $client = self::createClient();

        $newName = 'FOO';

        $retailer = (new RetailerFixture('Foo', 'http://www.foo.com/'))->load(self::getEntityManager());

        $client->request('PATCH', '/api/retailers/' . $retailer->getId()->toString(), [], [], [], json_encode([
            'name' => $newName,
        ]));

        $response = $client->getResponse();

//        $this->assertSame(202, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        var_dump($content);
        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('name', $content['result']);
        $this->assertSame($newName, $content['result']['name']);
    }
}
