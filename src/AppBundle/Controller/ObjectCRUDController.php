<?php

namespace AppBundle\Controller;

use AppBundle\GetsRequestSerializationGroups;
use AppBundle\Handler\ObjectHandler;
use AppBundle\RendersJson;
use Assert\Assertion;
use Doctrine\ORM\EntityNotFoundException;
use Exception\ObjectUpdateMismatchException;
use Exception\PersistenceException;
use Exception\Serializer\Construction\ObjectNotConstructedException;
use Exception\ValidationException;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObjectCRUDController extends FOSRestController
{
    use GetsRequestSerializationGroups;
    use RendersJson;

    const OFFSET = 0;
    const LIMIT = 10;

    /**
     * @var string
     */
    protected $entityFqcn;

    /**
     * @var ObjectHandler
     */
    protected $handler;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param string $entityFqcn
     * @param ObjectHandler $handler
     * @param Serializer $serializer
     */
    public function __construct($entityFqcn, ObjectHandler $handler, Serializer $serializer)
    {
        $this->entityFqcn = $entityFqcn;
        $this->handler = $handler;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createObject(Request $request)
    {
        try {
            $object = $this->handler->post($request->getContent());
        } catch (ObjectNotConstructedException $exception) {
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
                $object,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @param object $object
     *
     * @return Response
     */
    public function deleteObject($object)
    {
        Assertion::isInstanceOf($object, $this->entityFqcn);

        try {
            $this->handler->delete($object);
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
     * @param Request $request
     * @param object $object
     *
     * @return Response
     */
    public function editObject(Request $request, $object)
    {
        Assertion::isInstanceOf($object, $this->entityFqcn);

        try {
            $object = $this->handler->put($object, $request->getContent());
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
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        } catch (ValidationException $exception) {
            return $this->renderJson(400, ['errors' => $exception->getErrors()]);
        }

        return $this->renderJson(201, [
            'result' => $this->serializer->toArray(
                $object,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function getObjectList(Request $request)
    {
        $products = $this->handler->getList(
            (int) $request->query->get('offset', self::OFFSET),
            (int) $request->query->get('limit', self::LIMIT)
        );

        if (empty($products)) {
            return $this->renderJson(204);
        }

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray(
                $products,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return Response
     */
    public function getObject(Request $request, $object)
    {
        Assertion::isInstanceOf($object, $this->entityFqcn);

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray(
                $object,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @param Request $request
     * @param object $object
     *
     * @return Response
     */
    public function updateObject(Request $request, $object)
    {
        Assertion::isInstanceOf($object, $this->entityFqcn);

        try {
            $object = $this->handler->patch($object, $request->getContent());
        } catch (ObjectUpdateMismatchException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        } catch (ValidationException $exception) {
            return $this->renderJson(400, ['errors' => $exception->getErrors()]);
        }

        return $this->renderJson(202, [
            'result' => $this->serializer->toArray(
                $object,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }
}
