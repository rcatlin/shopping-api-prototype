<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Retailer;
use AppBundle\Handler\RetailerHandler;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/retailers")
 */
class RetailerCRUDController extends FOSRestController
{
    use RendersJson;

    const LIMIT = 10;
    const OFFSET = 0;

    /**
     * @DI\Inject("handler.retailer")
     *
     * @var RetailerHandler
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
        try {
            $retailer = $this->handler->post(json_decode($request->getContent(), true));
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
            'result' => $this->serializer->toArray($retailer),
        ]);
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
        try {
            $this->handler->delete($retailer);
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
     * @param Request $request
     * @param string $uuid
     *
     * @return Response
     */
    public function edit(Request $request, $uuid)
    {
        try {
            $retailer = $this->handler->put($uuid, json_decode($request->getContent(), true));
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
            'result' => $this->serializer->toArray($retailer),
        ]);
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
     * @param string $uuid
     *
     * @return Response
     */
    public function getRetailer($uuid)
    {
        try {
            $retailer = $this->handler->get($uuid);
        } catch (InvalidArgumentException $exception) {
            return $this->renderJson(400, [
                'errors' => [$exception->getMessage()],
            ]);
        } catch (EntityNotFoundException $exception) {
            return $this->renderJson(404, [
                'errors' => [$exception->getMessage()],
            ]);
        }

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray($retailer),
        ]);
    }
}
