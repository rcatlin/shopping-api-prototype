<?php

namespace Tests\Fixtures;

use AppBundle\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixture implements FixtureInterface
{
    private $children;
    private $name;
    private $parent;

    public function __construct($name, Category $parent = null, ArrayCollection $children = null)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->children = ($children === null) ? new ArrayCollection() : $children;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return Category
     */
    public function load(ObjectManager $manager)
    {
        $category = new Category();

        $category->setChildren($this->children);
        $category->setName($this->name);
        $category->setParent($this->parent);

        $manager->persist($category);
        $manager->flush();

        return $category;
    }
}
