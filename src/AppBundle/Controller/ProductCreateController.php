<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use Exception\InvalidFormException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/products")
 */
class ProductCreateController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("handler.product")
     *
     * @var ProductHandler
     */
    private $handler;

    /**
     * @DI\Inject("jms_serializer")
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Creates as New Product
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a New Product",
     *     input="AppBundle\Form\ProductType",
     *     statusCodes={
     *         201="Product was successfully created and persisted",
     *         400="Invalid Product Data",
     *         500="Server encountered an error persisting the Product"
     *     }
     * )
     *
     * @Method({"POST"})
     * @Route(path="", name="api_product_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            $product = $this->handler->post($request->getContent());
        } catch (InvalidFormException $exception) {
            return $this->renderJson(400, [
                'errors' => $exception->getErrors(),
            ]);
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        }

        return $this->renderJson(201, [
            'result' => $this->serializer->toArray($product),
        ]);
    }
}
