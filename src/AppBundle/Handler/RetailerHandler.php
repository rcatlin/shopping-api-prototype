<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Retailer;
use AppBundle\Repository\RetailerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception\PersistenceException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;

/**
 * @DI\Service("handler.retailer")
 */
class RetailerHandler
{
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
     * @DI\InjectParams({
     *     "objectManager"=@DI\Inject("doctrine.orm.default_entity_manager"),
     *     "repository"=@DI\Inject("repository.retailer"),
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     *
     * @param ObjectManager $objectManager
     * @param RetailerRepository $repository
     * @param Serializer $serializer
     */
    public function __construct(
        ObjectManager $objectManager,
        RetailerRepository $repository,
        Serializer $serializer
    ) {
        $this->objectManager = $objectManager;
        $this->repository = $repository;
        $this->serializer = $serializer;
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

        try {
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $retailer;
    }
}
