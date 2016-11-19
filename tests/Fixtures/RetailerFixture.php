<?php

namespace Tests\Fixtures;

use AppBundle\Entity\Retailer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RetailerFixture implements FixtureInterface
{
    private $name;
    private $url;
    private $products;

    public function __construct($name, $url, ArrayCollection $products = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->products = ($products !== null) ? $products : new ArrayCollection();
    }

    public function load(ObjectManager $manager)
    {
        $retailer = new Retailer();

        $retailer->setName($this->name);
        $retailer->setUrl($this->url);
        $retailer->setProducts($this->products);

        $manager->persist($retailer);
        $manager->flush();

        return $retailer;
    }
}
