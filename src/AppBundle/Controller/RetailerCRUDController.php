<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Retailer;
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
 * @Route(path="/api/retailers")
 */
class RetailerCRUDController extends AbstractObjectCRUDController
{
    /**
     * @DI\InjectParams({
     *     "handler"=@DI\Inject("handler.retailer"),
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     *
     * @param ObjectHandler $handler
     * @param Serializer $serializer
     */
    public function __construct(ObjectHandler $handler, Serializer $serializer)
    {
        parent::__construct('AppBundle\Entity\Retailer', $handler, $serializer);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a New Retailer",
     *     input="AppBundle\Form\RetailerType",
     *     statusCodes={
     *         201="Retailer was successfully created and persisted",
     *         400="Invalid Retailer Data",
     *         500="Server encountered an error persisting the Retailer"
     *     }
     * )
     *
     * @Method({"POST"})
     * @Route(path="", name="api_retailer_create")
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
     *     description="Deletes an Existing Retailer",
     *     statusCodes={
     *         204="Retailer successfully deleted",
     *         404="Retailer not found",
     *         500="Server encountered an error deleting Retailer"
     *     }
     * )
     *
     * @Method({"DELETE"})
     * @Route(path="/{uuid}", name="api_retailer_delete")
     *
     * @ParamConverter(
     *     "retailer",
     *     class="AppBundle\Entity\Retailer",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Retailer $retailer
     *
     * @return Response
     */
    public function delete(Retailer $retailer)
    {
        return parent::deleteObject($retailer);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Retailer",
     *     input="AppBundle\Form\RetailerType",
     *     statusCodes={
     *         201="Retailer was successfully modified and changes were persisted",
     *         400="Invalid Retailer Data or Bad UUID",
     *         404="Retailer with given UUID not found.",
     *         500="Server encountered an error saving the Retailer changes"
     *     }
     * )
     *
     * @Method({"PUT"})
     * @Route(path="/{uuid}", name="api_retailer_edit")
     *
     * @ParamConverter(
     *     "retailer",
     *     class="AppBundle:Retailer",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Retailer $retailer
     *
     * @return Response
     */
    public function edit(Request $request, Retailer $retailer)
    {
        return parent::editObject($request, $retailer);
    }

    /**
     * @ApiDoc(
     *     description="Get a List of Retailers",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     statusCodes={
     *          200="A List of Retailers were retrieved.",
     *          204="No Retailers were found and no content was returned."
     *     }
     * )
     *
     * @Method({"GET"})
     * @Route(path="", name="api_retailer_list")
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
     *     description="Retrieve a Retailer by UUID",
     *     statusCodes={
     *         200="Retailer with given UUID found",
     *         400="Invalid UUID provided",
     *         404="Retailer was not found given the UUID"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_get_retailer")
     * @Method({"GET"})
     *
     * @ParamConverter(
     *     "retailer",
     *     class="AppBundle\Entity\Retailer",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Retailer $retailer
     *
     * @return Response
     */
    public function getRetailer(Request $request, Retailer $retailer)
    {
        return parent::getObject($request, $retailer);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Partially Update a Retailer by UUID",
     *     statusCodes={
     *         200="Retailer was successfully updated and changes were persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error updating Retailer or persisting changes"
     *     }
     * )
     * 
     * @Route("/{uuid}", name="api_partial_update_retailer")
     * @Method({"PATCH"})
     *
     * @ParamConverter(
     *     "retailer",
     *     class="AppBundle\Entity\Retailer",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Retailer $retailer
     *
     * @return Response
     */
    public function partialUpdateRetailer(Request $request, Retailer $retailer)
    {
        return parent::updateObject($request, $retailer);
    }
}
