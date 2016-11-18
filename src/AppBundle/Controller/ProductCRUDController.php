<?php

namespace AppBundle\Controller;
use AppBundle\Entity\Product;
use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use Exception\InvalidFormException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
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
class ProductCRUDController
{
    use RendersJson;

    const LIMIT = 10;
    const OFFSET = 0;

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
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a New Product",
     *     statusCodes={
     *         201="Product was successfully created and persisted",
     *         400="Bad Request Data",
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
        } catch (ObjectNotConstructedException $exception) {
            return $this->renderJson(400, [
                'errors' => $exception->getMessage(),
                'data' => $exception->getData(),
                'path' => $exception->getPath(),
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

    /**
     * @ApiDoc(
     *     description="Deletes an Existing Product",
     *     statusCodes={
     *         204="Product was successfully deleted",
     *         404="Product with given UUID not found",
     *         500="Server encountered an error deleting the Product"
     *     }
     * )
     *
     * @Method({"DELETE"})
     * @Route(path="/{uuid}", name="api_product_delete")
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
    public function delete(Product $product)
    {
        try {
            $this->handler->delete($product);
        } catch (EntityNotFoundException $exception) {
            return $this->renderJson(404, [
                'errors' => [$exception->getMessage()],
            ]);
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => [$exception->getMessage()],
            ]);
        }

        return $this->renderJson(204);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Product",
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
     *
     * @return Response
     */
    public function edit(Request $request, Product $product)
    {
        try {
            $product = $this->handler->put($product, $request->getContent());
        } catch (ObjectNotConstructedException $exception) {
            return $this->renderJson(400, [
                'errors' => $exception->getMessage(),
                'data' => $exception->getData(),
                'path' => $exception->getPath(),
            ]);
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

    /**
     * @ApiDoc(
     *     description="Get a List of Products",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     statusCodes={
     *          200="A List of Products were retrieved.",
     *          204="No products were found and no content was returned."
     *     }
     * )
     *
     * @Method({"GET"})
     * @Route(path="", name="api_product_list")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getList(Request $request)
    {
        $list = $this->handler->getList(
            (int) $request->query->get('offset', self::OFFSET),
            (int) $request->query->get('limit', self::LIMIT)
        );

        if (empty($list)) {
            return $this->renderJson(204);
        }

        return $this->renderJson(200, [
            'result' => array_map([$this->serializer, 'toArray'], $list),
        ]);
    }

    /**
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
