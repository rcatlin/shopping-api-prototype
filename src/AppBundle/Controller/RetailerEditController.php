<?php

namespace AppBundle\Controller;

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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/retailers")
 */
class RetailerEditController extends FOSRestController
{
    use RendersJson;

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
     * Edits an Existing Retailer
     *
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
}
