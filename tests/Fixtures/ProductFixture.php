<?php

namespace Tests\Fixtures;

use AppBundle\Entity\Product;
use AppBundle\Entity\Retailer;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixture implements FixtureInterface
{
    private $name;
    private $price;
    private $retailer;

    public function __construct($name, $price, Retailer $retailer = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->retailer = $retailer;
    }

    public function load(ObjectManager $manager)
    {
        $product = new Product();

        $product->setName($this->name);
        $product->setPrice($this->price);
        $product->setRetailer($this->retailer);

        $manager->persist($product);
        $manager->flush();

        return $product;
    }
}
