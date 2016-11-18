<?php

namespace Tests\Functional\AppBundle\Controller;

use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Tests\AssertsArrayHasKeys;
use Tests\Fixtures\ProductFixture;
use Tests\Fixtures\RetailerFixture;
use Tests\Functional\WebTestCase;

class ProductCRUDEditControllerTest extends WebTestCase
{
    use AssertsArrayHasKeys;

    public function testEdit()
    {
        $client = self::createClient();

        $product = (new ProductFixture('Foo', 5555))->load($this->getEntityManager());

        $productId = $product->getId()->toString();

        $productUrl = '/api/products/' . $productId;

        $newName = 'Bar';
        $newPrice = 6666;

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => $newName,
            'price' => $newPrice
        ]));
        $response = $client->getResponse();
;
        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKey('id', $content['result']);
        $this->assertArrayHasKey('name', $content['result']);
        $this->assertArrayHasKey('price', $content['result']);

        $this->assertSame($productId, $content['result']['id']);
        $this->assertSame($newName, $content['result']['name']);
        $this->assertSame($newPrice, $content['result']['price']);
    }

    public function testEditWithNewRetailer()
    {
        $client = self::createClient();

        $name = 'Foo';
        $price = 7890;
        $retailerName = 'Bar';
        $retailerUrl = 'http://www.bar.com/';

        $product = (new ProductFixture($name, $price))->load($this->getEntityManager());

        $productId = $product->getId()->toString();
        $productUrl = '/api/products/' . $productId;

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => $name,
            'price' => $price,
            'retailer' => [
                'name' => $retailerName,
                'url' => $retailerUrl,
            ]
        ]));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'price', 'retailer'], $content['result']);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']['retailer']);

        $this->assertSame($productId, $content['result']['id']);
        $this->assertSame($name, $content['result']['name']);
        $this->assertSame($price, $content['result']['price']);
        $this->assertNotNull($content['result']['retailer']['id']);
        $this->assertSame($retailerName, $content['result']['retailer']['name']);
        $this->assertSame($retailerUrl, $content['result']['retailer']['url']);
    }

    public function testEditWithExistingRetailer()
    {
        $client = self::createClient();

        $productName = 'Foo';
        $productPrice = 9876;
        $retailerName = 'Bar';
        $retailerUrl = 'http://www.bar.com/';

        $product = (new ProductFixture($productName, $productPrice))->load($this->getEntityManager());
        $retailer = (new RetailerFixture($retailerName, $retailerUrl))->load($this->getEntityManager());

        $productId = $product->getId()->toString();
        $productUrl = '/api/products/' . $productId;
        $retailerId = $retailer->getId()->toString();

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => $productName,
            'price' => $productPrice,
            'retailer' => [
                'id' => $retailerId,
                'name' => $retailerName,
                'url' => $retailerUrl,
            ]
        ]));
        $response = $client->getResponse();

        $this->assertSame(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('result', $content);
        $this->assertArrayHasKeys(['id', 'name', 'price', 'retailer'], $content['result']);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['result']['retailer']);

        $this->assertSame($productId, $content['result']['id']);
        $this->assertSame($productName, $content['result']['name']);
        $this->assertSame($productPrice, $content['result']['price']);
        $this->assertSame($retailerId, $content['result']['retailer']['id']);
        $this->assertSame($retailerName, $content['result']['retailer']['name']);
        $this->assertSame($retailerUrl, $content['result']['retailer']['url']);
    }

    public function testEditFailsWithBadRetailerId()
    {
        $client = self::createClient();

        $productName = 'Foo';
        $productPrice = 9876;
        $retailerName = 'Bar';
        $retailerUrl = 'http://www.bar.com/';

        $product = (new ProductFixture($productName, $productPrice))->load($this->getEntityManager());

        $productId = $product->getId()->toString();
        $productUrl = '/api/products/' . $productId;
        $badRetailerId = Uuid::getFactory()->uuid4();

        $client->request('PUT', $productUrl, [], [], [], json_encode([
            'name' => $productName,
            'price' => $productPrice,
            'retailer' => [
                'id' => $badRetailerId,
                'name' => $retailerName,
                'url' => $retailerUrl,
            ]
        ]));

        $response = $client->getResponse();

        $this->assertSame(400, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKeys(['errors', 'data'], $content);
        $this->assertArrayHasKeys(['id', 'name', 'url'], $content['data']);

        $this->assertSame($badRetailerId->toString(), $content['data']['id']);
        $this->assertSame($retailerName, $content['data']['name']);
        $this->assertSame($retailerUrl, $content['data']['url']);
    }
}
