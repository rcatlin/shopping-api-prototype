<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Handler\ObjectHandler;
use FOS\RestBundle\Controller\Annotations\Route;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/categories")
 */
class CategoryCRUDController extends ObjectCRUDController
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
     * @Route("/{uuid}", name="api_edit_category")
     * @Method({"POST"})
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
