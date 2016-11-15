<?php

namespace AppBundle\Entity;

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
     * @var string
     *
     * @Assert\NotBlank(message="Url should not be blank.")
     * @Assert\Type(
     *     type="url",
     *     message="Url must be a valid url."
     * )
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
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    private $url;


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

