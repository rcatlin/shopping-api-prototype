<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Product
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
     *     message="Name must be a valid string."
     * )
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Serializer\Expose()
     * @Serializer\Accessor(getter="getName", setter="setName")
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * Price in Minor Units (US pennies)
     *
     * @var integer
     *
     * @Assert\Type(
     *     type="integer",
     *     message="Price must be an integer."
     * )
     * @Assert\Range(
     *     min=1,
     *     minMessage="Price must be greater than or equal to a US penny."
     * )
     *
     * @ORM\Column(name="price", type="bigint", nullable=true)
     *
     * @Serializer\Expose()
     * @Serializer\Accessor(getter="getPrice", setter="setPrice")
     * @Serializer\SerializedName("price")
     * @Serializer\Type("integer")
     */
    private $price;

    /**
     * @var Retailer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Retailer")
     *
     * @Serializer\Expose()
     * @Serializer\SerializedName("retailer")
     * @Serializer\Accessor(
     *     getter="getRetailer",
     *     setter="setRetailer"
     * )
     */
    private $retailer;

    public function __toString()
    {
        $retailer = $this->getRetailer();

        if ($retailer === null) {
            return $this->getName();
        }

        return $retailer->getName() . ' - ' . $this->getName();
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
     * @return Product
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

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return Retailer
     */
    public function getRetailer()
    {
        return $this->retailer;
    }

    /**
     * @param Retailer $retailer
     */
    public function setRetailer(Retailer $retailer = null)
    {
        if ($retailer !== null) {
            $this->retailer = $retailer;

            return;
        }

        if ($this->retailer === null) {
            return;
        }

        $retailer->removeProduct($this);
    }
}

