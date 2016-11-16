<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/products")
 */
class ProductGetController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("handler.product")
     *
     * @var ProductHandler
     */
    private $handler;

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
     * @ParamConverter(
     *     "product",
     *     class="AppBundle:Product",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Product $product
     *
     * @return Response
     */
    public function getProduct(Product $product)
    {
        return $this->renderJson(200, [
            'result' => $this->serializer->toArray($product),
        ]);
    }
}
