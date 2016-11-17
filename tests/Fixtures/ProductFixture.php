<?php

namespace Tests\Fixtures;

use AppBundle\Entity\Product;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixture implements FixtureInterface
{
    private $name;
    private $price;

    public function __construct($name, $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function load(ObjectManager $manager)
    {
        $product = new Product();

        $product->setName($this->name);
        $product->setPrice($this->price);

        $manager->persist($product);
        $manager->flush();

        return $product;
    }
}
