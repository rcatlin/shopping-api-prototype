<?php

namespace AppBundle\Controller\Api;

use AppBundle\RendersJson;
use AppBundle\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/products")
 */
class ProductController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("repository.product", required=true)
     *
     * @var ProductRepository
     */
    private $repository;

    /**
     * Retrieves a product by UUID
     *
     * @Route("/{uuid}", name="api_get_product")
     * @Method({"GET"})
     *
     * @param string $uuid
     *
     * @return Response
     */
    public function getByUuid($uuid)
    {
        try {
            $binaryUuid = Uuid::fromString($uuid);
        } catch (InvalidArgumentException $exception) {
            return $this->renderJson(
                400,
                [
                    'message' => $exception->getMessage(),
                ]
            );
        }

        $product = $this->repository->find($binaryUuid);

        if ($product === null) {
            return $this->renderJson(
                404,
                ['message' => 'Product Not Found']
            );
        }

        return $this->renderJson(
            200,
            [
                'result' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                ]
            ]
        );
    }
}
