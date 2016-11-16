<?php

namespace AppBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Retailer
 *
 * @ORM\Table(name="retailer")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RetailerRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Retailer
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
     * @Serializer\Accessor(getter="getId")
     * @Serializer\ReadOnly(true)
     * @Serializer\SerializedName("id")
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Name should not be blank.")
     * @Assert\Type(
     *     type="string",
     *     message="Name must be a string."
     * )
     *
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=255,
     *     unique=true
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Accessor(
     *     getter="getName",
     *     setter="setName"
     * )
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Product",
     *     mappedBy="retailer",
     *     cascade={"persist"}
     * )
     */
    private $products;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Url should not be blank.")
     *
     * @ORM\Column(
     *     name="url",
     *     type="string",
     *     length=255
     * )
     *
     * @Serializer\Expose()
     * @Serializer\Accessor(
     *     getter="getUrl",
     *     setter="setUrl"
     * )
     * @Serializer\SerializedName("url")
     * @Serializer\Type("string")
     */
    private $url;

    public function __toString()
    {
        return $this->getName();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Retailer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function addProduct(Product $product)
    {
        $product->setRetailer($this);

        $this->products->add($product);
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function removeProduct(Product $product)
    {
        $product->setRetailer(null);

        $this->products->removeElement($product);
    }

    /**
     * @param ArrayCollection $products
     */
    public function setProducts(ArrayCollection $products)
    {
        $products->forAll(function ($product) {
            /** @var Product $product */
            $product->setRetailer($this);
        });

        $this->products = $products;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Retailer
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

