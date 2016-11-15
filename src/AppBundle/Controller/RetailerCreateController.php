<?php

namespace AppBundle\Controller;

use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Exception\InvalidFormException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/retailers")
 */
class RetailerCreateController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("handler.retailer")
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
     * Creates as New Retailer
     *
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
}
