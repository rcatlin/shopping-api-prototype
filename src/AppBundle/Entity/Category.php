<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid_binary")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     * @Serializer\ReadOnly(true)
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotNull(message="Name cannot be empty.")
     * @Assert\NotBlank(message="Name cannot be blank.")
     * @Assert\Type(
     *     type="string",
     *     message="Name must be a valid string."
     * )
     *
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=255
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"default"})
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Category",
     *     inversedBy="children"
     * )
     *
     * @Serializer\Groups({"parent"})
     * @Serializer\MaxDepth(1)
     */
    private $parent;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Category",
     *     mappedBy="parent"
     * )
     *
     * @Serializer\Groups({"children"})
     * @Serializer\MaxDepth(1)
     */
    private $children;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     *
     * @return $this
     */
    public function setChildren(ArrayCollection $children)
    {
        $children->forAll(function ($key, $child) {
            /** @var Category $child */
            $child->setParent($child);
        });

        $this->children = $children;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Category $parent
     *
     * @return Category
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }
}

