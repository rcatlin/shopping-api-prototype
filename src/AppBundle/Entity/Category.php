<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Category implements IdentifiableInterface
{
    /**
     * @var Uuid
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
     * @Assert\NotNull()
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
     * @Assert\Valid()
     *
     * @ORM\ManyToOne(
     *     targetEntity="AppBundle\Entity\Category",
     *     inversedBy="children",
     *     cascade={"PERSIST"}
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"parent"})
     * @Serializer\MaxDepth(1)
     */
    private $parent;

    /**
     * @var Uuid
     *
     * @ORM\Column(
     *     name="parent_id",
     *     nullable=true,
     *     type="uuid_binary"
     * )
     */
    private $parentId;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Category",
     *     mappedBy="parent",
     *     cascade={"PERSIST"}
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"children"})
     * @Serializer\MaxDepth(2)
     * @Serializer\Accessor(
     *     getter="getChildren",
     *     setter="setChildren"
     * )
     */
    private $children;

    /**
     * @var ArrayCollection
     *
     * @Assert\Valid()
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Product",
     *     mappedBy="category"
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"products"})
     * @Serializer\MaxDepth(2)
     * @Serializer\Accessor(
     *     setter="setProducts"
     * )
     */
    private $products;

    /**
     * Get Id
     *
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|null
     */
    public function getChildren()
    {
        if ($this->children === null || $this->children->isEmpty()) {
            return null;
        }

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
     * @param Category|null $parent
     *
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setProducts(ArrayCollection $products = null)
    {
        if ($products !== null) {
            foreach ($products as $product) {
                $product->setCategory($this);
            }
        }

        $this->products = $products;

        return $this;
    }

    public function addProduct(Product $product)
    {
        if ($this->products === null) {
            $this->products = new ArrayCollection([$product]);

            return $this;
        }

        if (!$this->products->contains($product)) {
            $this->products->add($product);

            return $this;
        }

        return $this;
    }

    public function removeProduct(Product $product)
    {
        if ($this->products !== null && $this->products->contains($product)) {
            $this->products->contains($product);
        }

        return $this;
    }
}

