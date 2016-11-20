<?php

namespace AppBundle\Handler;

use AppBundle\ValidatesEntity;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Exception\ObjectUpdateMismatchException;
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
 * @DI\Service("handler.object")
 */
class ObjectHandler
{
    use ValidatesEntity;

    /**
     * @var string
     */
    private $entityFcqn;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var EntityRepository
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
     * @param string $entityFqcn
     * @param ObjectManager $objectManager
     * @param EntityRepository $repository
     * @param Serializer $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        $entityFqcn,
        ObjectManager $objectManager,
        EntityRepository $repository,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityFcqn = $entityFqcn;
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @param object $object
     *
     * @throws PersistenceException
     */
    public function delete($object)
    {
        try {
            $this->objectManager->remove($object);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }
    }

    /**
     * @param string $uuid
     *
     * @return object
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     */
    public function get($uuid)
    {
        $binaryUuid = $binaryUuid = Uuid::fromString($uuid);

        $object = $this->repository->find($binaryUuid);

        if ($object === null) {
            throw new EntityNotFoundException($this->entityFcqn . ' not found with UUID: ' . $uuid);
        }

        return $object;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return array
     */
    public function getList($offset, $limit)
    {
        return $this->repository->findBy([], null, (int) $limit, (int) $offset);
    }

    /**
     * @param object $object
     * @param string $data
     *
     * @return object
     *
     * @throws ObjectUpdateMismatchException
     * @throws PersistenceException
     * @throws ValidationException
     */
    public function patch($object, $data)
    {
        $originalId = $object->getId()->toString();

        $object = $this->serializer->deserialize(
            $data,
            $this->entityFcqn,
            'json',
            (new DeserializationContext())->setAttribute('target', $object)
        );

        $finalId = $object->getId()->toString();

        if ($originalId !== $finalId) {
            throw new ObjectUpdateMismatchException($this->entityFcqn, $originalId);
        }

        $this->validateEntity($this->validator, $object);

        try {
            $object = $this->objectManager->merge($object);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $object;
    }

    /**
     * @param string $data
     *
     * @return object
     *
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     * @throws ValidationException
     */
    public function post($data)
    {
        $object = $this->serializer->deserialize($data, $this->entityFcqn, 'json');

        $this->validateEntity($this->validator, $object);

        try {
            $this->objectManager->persist($object);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $object;
    }

    /**
     * @param object $object
     * @param string $data
     *
     * @return object
     *
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws ObjectNotConstructedException
     * @throws PersistenceException
     */
    public function put($object, $data)
    {
        $object = $this->serializer->deserialize(
            $data,
            $this->entityFcqn,
            'json',
            (new DeserializationContext())->setAttribute('target', $object)
        );

        $this->validateEntity($this->validator, $object);

        try {
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $object;
    }
}
