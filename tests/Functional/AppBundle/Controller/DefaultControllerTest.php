<?php

namespace Tests\AppBundle\Controller;

use Tests\Functional\AppBundle\Controller\AbstractControllerTestCase;

class DefaultControllerTest extends AbstractControllerTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
