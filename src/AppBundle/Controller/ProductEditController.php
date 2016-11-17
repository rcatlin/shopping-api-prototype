<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use AppBundle\Repository\ProductRepository;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/products")
 */
class ProductEditController extends FOSRestController
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
     * Edits an Existing Product
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Product",
     *     input="AppBundle\Form\ProductType",
     *     statusCodes={
     *         201="Product was successfully modified and changes were persisted",
     *         400="Invalid Product Data or Bad UUID",
     *         404="Product with given UUID not found.",
     *         500="Server encountered an error saving the Product changes"
     *     }
     * )
     *
     * @Method({"PUT"})
     * @Route(path="/{uuid}", name="api_product_edit")
     *
     * @ParamConverter(
     *     "product",
     *     class="AppBundle:Product",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product)
    {
        try {
            $product = $this->handler->put($product, json_decode($request->getContent(), true));
        } catch (EntityNotFoundException $exception) {
            return $this->renderJson(404, [
                'errors' => [$exception->getMessage()],
            ]);
        } catch (InvalidArgumentException $exception) {
            return $this->renderJson(400, [
                'errors' => [$exception->getMessage()],
            ]);
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
