<?php

namespace AppBundle\Controller\Api;

use AppBundle\RendersJson;
use AppBundle\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/products")
 */
class ProductGetController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("repository.product", required=true)
     *
     * @var ProductRepository
     */
    private $repository;

    /**
     * @DI\Inject("jms_serializer", required=true)
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Retrieves a product by UUID
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a Product by UUID",
     *     statusCodes={
     *         200="Product with given UUID found",
     *         400="Invalid UUID provided",
     *         404="Product was not found given the UUID"
     *     }
     * )
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
                'result' => $this->serializer->toArray($product),
            ]
        );
    }
}
