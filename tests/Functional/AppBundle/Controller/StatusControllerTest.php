<?php

namespace Functional\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusControllerTest extends WebTestCase
{
    public function testGetStatus()
    {
        $client = self::createClient();

        $client->request('GET', '/api/status');

        $response = $client->getResponse();

        $this->assertSame(200, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('status', $content['result']);
        $this->assertSame('OK', $content['result']['status']);
    }
}
