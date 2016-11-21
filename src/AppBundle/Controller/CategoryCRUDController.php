<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
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
 * @Route("/api/categories")
 */
class CategoryCRUDController extends AbstractObjectCRUDController
{
    /**
     * @DI\InjectParams({
     *     "handler"=@DI\Inject("handler.category"),
     *     "serializer"=@DI\Inject("jms_serializer")
     * })
     *
     * @param ObjectHandler $handler
     * @param Serializer $serializer
     */
    public function __construct(ObjectHandler $handler, Serializer $serializer)
    {
        parent::__construct('AppBundle\Entity\Category', $handler, $serializer);
    }

    /**
     * @ApiDoc(
     *     description="Create a New Category",
     *     section="Category",
     *     statusCodes={
     *         201="Category was successfully created and persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error persisting the Category"
     *     }
     * )
     *
     * @Route("", name="api_create_category")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createCategory(Request $request)
    {
       return parent::createObject($request);
    }

    /**
     * @ApiDoc(
     *     description="Deletes an Existing Category",
     *     section="Category",
     *     statusCodes={
     *         204="Category was successfully deleted",
     *         404="Category with given UUID not found",
     *         500="Server encountered an error deleting the Category"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_delete_category")
     * @Method({"DELETE"})
     *
     * @ParamConverter(
     *     "category",
     *     class="AppBundle\Entity\Category",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Category $category
     *
     * @return Response
     */
    public function deleteCategory(Category $category)
    {
        return parent::deleteObject($category);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Category",
     *     section="Category",
     *     statusCodes={
     *         201="Category was successfully modified and changes were persisted",
     *         400="Invalid Category Data or Bad UUID",
     *         404="Category with given UUID not found.",
     *         500="Server encountered an error saving the Category changes"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_edit_category")
     * @Method({"PUT"})
     *
     * @ParamConverter(
     *     "category",
     *     class="AppBundle:Category",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function editCategory(Request $request, Category $category)
    {
        return parent::editObject($request, $category);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Retrieve a Category by UUID",
     *     section="Category",
     *     statusCodes={
     *         200="Category with given UUID found",
     *         400="Invalid UUID provided",
     *         404="Category was not found given the UUID"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_get_category")
     * @Method({"GET"})
     *
     * @ParamConverter(
     *     "category",
     *     class="AppBundle\Entity\Category",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function getCategory(Request $request, Category $category)
    {
        return parent::getObject($request, $category);
    }

    /**
     * @ApiDoc(
     *     description="Get a List of Categories",
     *     section="Category",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     statusCodes={
     *          200="A List of Categories were retrieved.",
     *          204="No Categories were found and no content was returned."
     *     }
     * )
     *
     * @Route("", name="api_list_categories")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getCategoryList(Request $request)
    {
        return parent::getObjectList($request);
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Partially Update a Category by UUID",
     *     section="Category",
     *     statusCodes={
     *         200="Category was successfully updated and changes were persisted",
     *         400="Bad Request Data",
     *         500="Server encountered an error updating Category or persisting changes"
     *     }
     * )
     *
     * @Route("/{uuid}", name="api_partial_update_category")
     * @Method({"PATCH"})
     *
     * @ParamConverter(
     *     "category",
     *     class="AppBundle\Entity\Category",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function partialUpdateCategory(Request $request, Category $category)
    {
        return parent::updateObject($request, $category);
    }
}
