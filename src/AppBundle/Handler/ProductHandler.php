<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\ValidatesEntity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception\InvalidFormException;
use Exception\PersistenceException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use Exception\ValidationException;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @DI\Service("handler.product")
 */
class ProductHandler
{
    use ValidatesEntity;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @DI\InjectParams({
     *     "objectManager"=@DI\Inject("doctrine.orm.default_entity_manager"),
     *     "repository"=@DI\Inject("repository.product"),
     *     "serializer"=@DI\Inject("jms_serializer"),
     *     "validator"=@DI\Inject("validator")
     * })
     *
     * @param ObjectManager $objectManager
     * @param ProductRepository $repository
     * @param Serializer $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ObjectManager $objectManager,
        ProductRepository $repository,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function delete(Product $product)
    {
        try {
            $this->objectManager->remove($product);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }
    }

    /**
     * @param string $uuid
     *
     * @return Product
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     */
    public function get($uuid)
    {
        $binaryUuid = $binaryUuid = Uuid::fromString($uuid);

        $product = $this->repository->find($binaryUuid);

        if ($product === null) {
            throw new EntityNotFoundException('Product not found with UUID: ' . $uuid);
        }

        return $product;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getList($offset, $limit)
    {
        return $this->repository->findBy([], null, (int) $limit, (int) $offset);
    }

    /**
     * @param Product $product
     * @param string $data
     *
     * @return Product
     *
     * @throws PersistenceException
     * @throws ValidationException
     */
    public function patch(Product $product, $data)
    {
        /** @var Product $product */
        $product = $this->serializer->deserialize(
            $data,
            'AppBundle\Entity\Product',
            'json',
            (new DeserializationContext())->setAttribute('target', $product)
        );

        $this->validateEntity($this->validator, $product);

        try {
            $product = $this->objectManager->merge($product);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $product;
    }

    /**
     * @param string $data
     *
     * @return Product|null
     *
     * @throws InvalidFormException
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     * @throws ValidationException
     */
    public function post($data)
    {
        /** @var Product $product */
        $product = $this->serializer->deserialize($data, 'AppBundle\Entity\Product', 'json');

        $this->validateEntity($this->validator, $product);

        try {
            $this->objectManager->persist($product);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $product;
    }

    /**
     * @param Product $product
     * @param string $data
     *
     * @return Product
     *
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws InvalidFormException
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     */
    public function put(Product $product, $data)
    {
        $product = $this->serializer->deserialize(
            $data,
            'AppBundle\Entity\Product',
            'json',
            (new DeserializationContext())->setAttribute('target', $product)
        );

        $this->validateEntity($this->validator, $product);

        try {
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $product;
    }
}
