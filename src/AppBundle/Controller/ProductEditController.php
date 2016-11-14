<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\RendersJson;
use AppBundle\Repository\ProductRepository;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/products")
 */
class ProductEditController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("repository.product")
     *
     * @var ProductRepository
     */
    private $repository;

    /**
     * @DI\Inject("jms_serializer")
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Edits an Existing Product
     *
     * @ApiDoc(
     *     resource=true,
     *     description="Updates an Existing Product",
     *     input="AppBundle\Form\ProductType",
     *     statusCodes={
     *         201="Product was successfully modified and changes were persisted",
     *         400="Invalid Product Data",
     *         500="Server encountered an error saving the Product changes"
     *     }
     * )
     *
     * @Method({"PUT"})
     * @Route(path="/{uuid}", name="api_product_edit")
     *
     * @param Request $request
     * @param string $uuid
     *
     * @return Response
     */
    public function edit(Request $request, $uuid)
    {
        try {
            $binaryUuid = Uuid::fromString($uuid);
        } catch (InvalidArgumentException $exception) {
            return $this->renderJson(
                400,
                [
                    'message' => $exception->getMessage(),
                ]
            );
        }

        $product = $this->repository->find($binaryUuid);

        $form = $this
            ->createForm('AppBundle\Form\ProductType', $product)
            ->submit(
                json_decode($request->getContent(), true)
            );

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->renderJson(400, [
                'errors' => $errors,
            ]);
        }

        /** @var Product $product */
        $product = $form->getData();
        $manager = $this->getDoctrine()->getManager();

        try {
            $manager->flush();
        } catch (\Exception $exception) {
            return $this->renderJson(
                500,
                [
                    'errors' => [
                        $exception->getMessage(),
                    ]
                ]
            );
        }

        return $this->renderJson(
            201,
            [
                'result' => $this->serializer->toArray($product),
            ]
        );
    }
}
