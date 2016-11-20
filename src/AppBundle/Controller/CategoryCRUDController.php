<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\GetsRequestSerializationGroups;
use AppBundle\Handler\ObjectHandler;
use AppBundle\RendersJson;
use Exception\PersistenceException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/categories")
 */
class CategoryCRUDController extends FOSRestController
{
    use GetsRequestSerializationGroups;
    use RendersJson;

    const LIMIT = 10;
    const OFFSET = 0;

    /**
     * @var ObjectHandler
     *
     * @DI\Inject("handler.category")
     */
    private $handler;

    /**
     * @var Serializer
     *
     * @DI\Inject("jms_serializer")
     */
    private $serializer;

    /**
     * @Route("", name="api_create_category")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            $category = $this->handler->post($request->getContent());
        }catch (ObjectNotConstructedException $exception) {
            return $this->renderJson(400, [
                'errors' => $exception->getMessage(),
                'data' => $exception->getData(),
                'path' => $exception->getPath(),
            ]);
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        } catch (ValidationException $exception) {
            return $this->renderJson(400, ['errors' => $exception->getErrors()]);
        }

        return $this->renderJson(201, [
            'result' => $this->serializer->toArray(
                $category,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    public function getCategory(Category $category)
    {
        return $this->renderJson(200, [
            'result' => $this->serializer->toArray(
                $category,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @Route("", name="api_list_categories")
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getList(Request $request)
    {
        $list = $this->handler->getList(
            $request->query->get('offset', self::OFFSET),
            $request->query->get('limit', self::LIMIT)
        );

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray(
                $list,
                $this->getSerializationContextFromRequest($request)
            )
        ]);
    }
}
