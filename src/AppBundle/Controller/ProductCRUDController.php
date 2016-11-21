<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Handler\ObjectHandler;
use FOS\RestBundle\Controller\Annotations\Route;
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
class ProductCRUDController extends AbstractObjectCRUDController
{
    /**
     * @DI\InjectParams({
     *     "handler"=@DI\Inject("handler.product"),
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     * @param ObjectHandler $handler
     * @param Serializer $serializer
     */
    public function __construct(ObjectHandler $handler, Serializer $serializer)
    {
        parent::__construct('AppBundle\Entity\Product', $handler, $serializer);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a New Product",
     *     section="Product",
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
        return parent::createObject($request);
    }

    /**
     * @ApiDoc(
     *     description="Deletes an Existing Product",
     *     section="Product",
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
        return parent::deleteObject($product);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Product",
     *     section="Product",
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
        return parent::editObject($request, $product);
    }

    /**
     * @ApiDoc(
     *     description="Get a List of Products",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     section="Product",
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
        return parent::getObjectList($request);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a Product by UUID",
     *     section="Product",
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
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     */
    public function getProduct(Request $request, Product $product)
    {
        return parent::getObject($request, $product);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Partially Update a Product by UUID",
     *     section="Product",
     *     statusCodes={
     *         200="Product was successfully updated and changes were persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error updating Product or persisting changes"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_partial_update_product")
     * @Method({"PATCH"})
     *
     * @ParamConverter(
     *     "product",
     *     class="AppBundle\Entity\Product",
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
    public function partialUpdateProduct(Request $request, Product $product)
    {
        return parent::updateObject($request, $product);
    }
}
