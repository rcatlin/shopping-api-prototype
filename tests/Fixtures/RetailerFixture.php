<?php

namespace Tests\Fixtures;

use AppBundle\Entity\Retailer;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RetailerFixture implements FixtureInterface
{
    private $name;
    private $url;

    public function __construct($name, $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public function load(ObjectManager $manager)
    {
        $retailer = new Retailer();

        $retailer->setName($this->name);
        $retailer->setUrl($this->url);

        $manager->persist($retailer);
        $manager->flush();

        return $retailer;
    }
}
