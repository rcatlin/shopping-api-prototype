<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Retailer;
use AppBundle\GetsRequestSerializationGroups;
use AppBundle\Handler\RetailerHandler;
use AppBundle\RendersJson;
use AppBundle\ValidatesEntity;
use Doctrine\ORM\EntityNotFoundException;
use Exception\InvalidFormException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(path="/api/retailers")
 */
class RetailerCRUDController extends FOSRestController
{
    use GetsRequestSerializationGroups;
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
            $retailer = $this->handler->post($request->getContent());
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
            'result' => $this->serializer->toArray(
                $retailer,
                $this->getSerializationContextFromRequest($request)
            ),
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
        try {
            $retailer = $this->handler->put($retailer, $request->getContent());
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        }

        return $this->renderJson(201, [
            'result' => $this->serializer->toArray($retailer,
                $this->getSerializationContextFromRequest($request)
            ),
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
        $retailers = $this->handler->getList(
            (int) $request->query->get('offset', self::OFFSET),
            (int) $request->query->get('limit', self::LIMIT)
        );

        if (empty($retailers)) {
            return $this->renderJson(204);
        }

        return $this->renderJson(200, [
            'result' => $this->serializer->toArray(
                $retailers,
                $this->getSerializationContextFromRequest($request)
            ),
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
        return $this->renderJson(200, [
            'result' => $this->serializer->toArray($retailer,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }

    /**
     * @Route("/{uuid}", name="api_partially_update_retailer")
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
    public function updateRetailer(Request $request, Retailer $retailer)
    {
        try {
            $retailer = $this->handler->patch($retailer, $request->getContent());
        } catch (PersistenceException $exception) {
            return $this->renderJson(500, [
                'errors' => $exception->getMessage(),
            ]);
        }

        return $this->renderJson(202, [
            'result' => $this->serializer->toArray(
                $retailer,
                $this->getSerializationContextFromRequest($request)
            ),
        ]);
    }
}
