<?php

namespace Tests\Functional\AppBundle\Controller;

use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\RetailerFixture;
use Tests\Functional\WebTestCase;

class RetailerCRUDEditControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testEdit()
    {
        $client = self::createClient();

        $retailer = (new RetailerFixture('Foo', 'http://www.foo.com/'))->load(self::getEntityManager());

        $retailerId = $retailer->getId()->toString();

        $this->assertNotNull($retailerId);

        $retailerUrl = '/api/retailers/' . $retailerId;
        $newName = 'Bar';
        $newUrl = 'http://www.bar.com/';

        $client->request('PUT', $retailerUrl, [], [], [], json_encode([
            'name' => $newName,
            'url' => $newUrl
        ]));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']);

        $this->assertSame($retailerId, $content['result']['id']);
        $this->assertSame($newName, $content['result']['name']);
        $this->assertSame($newUrl, $content['result']['url']);
    }
}
