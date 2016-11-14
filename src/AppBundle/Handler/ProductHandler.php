<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use Ramsey\Uuid\Uuid;

/**
 * @DI\Service("handler.product")
 */
class ProductHandler
{
    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @DI\InjectParams({
     *     "repository"=@DI\Inject("repository.product")
     * })
     *
     * @param ProductRepository $repository
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
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
}
