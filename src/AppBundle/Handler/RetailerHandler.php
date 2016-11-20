<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Retailer;
use AppBundle\Repository\RetailerRepository;
use AppBundle\ValidatesEntity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception\PersistenceException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @DI\Service("handler.retailer")
 */
class RetailerHandler
{
    use ValidatesEntity;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var RetailerRepository
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
     *     "repository"=@DI\Inject("repository.retailer"),
     *     "serializer"=@DI\Inject("jms_serializer"),
     *     "validator"=@DI\Inject("validator")
     * })
     *
     * @param ObjectManager $objectManager
     * @param RetailerRepository $repository
     * @param Serializer $serializer
     */
    public function __construct(
        ObjectManager $objectManager,
        RetailerRepository $repository,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public function delete(Retailer $retailer)
    {
        try {
            $this->objectManager->remove($retailer);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }
    }

    /**
     * @param string $uuid
     *
     * @return Retailer
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     */
    public function get($uuid)
    {
        $binaryUuid = $binaryUuid = Uuid::fromString($uuid);

        $retailer = $this->repository->find($binaryUuid);

        if ($retailer === null) {
            throw new EntityNotFoundException('Retailer not found with UUID: ' . $uuid);
        }

        return $retailer;
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
     * @param Retailer $retailer
     * @param string $data
     *
     * @return Retailer
     *
     * @throws PersistenceException
     */
    public function patch(Retailer $retailer, $data)
    {
        /** @var Retailer $retailer */
        $retailer = $this->serializer->deserialize(
            $data,
            'AppBundle\Entity\Retailer',
            'json',
            (new DeserializationContext())->setAttribute('target', $retailer)
        );

        $this->validateEntity($this->validator, $retailer);

        try {
            $retailer = $this->objectManager->merge($retailer);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $retailer;
    }

    /**
     * @param string $data
     *
     * @return Retailer
     *
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     */
    public function post($data)
    {
        /** @var Retailer $retailer */
        $retailer = $this->serializer->deserialize($data, 'AppBundle\Entity\Retailer', 'json');

        $this->validateEntity($this->validator, $retailer);

        try {
            $this->objectManager->persist($retailer);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $retailer;
    }

    /**
     * @param Retailer $retailer
     * @param string $data
     *
     * @return Retailer
     *
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     */
    public function put(Retailer $retailer, $data)
    {
        $retailer = $this->serializer->deserialize(
            $data,
            'AppBundle\Entity\Retailer',
            'json',
            (new DeserializationContext())->setAttribute('target', $retailer)
        );

        $this->validateEntity($this->validator, $retailer);

        try {
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $retailer;
    }
}
