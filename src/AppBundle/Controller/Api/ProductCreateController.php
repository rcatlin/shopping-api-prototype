<?php

namespace AppBundle\Controller\Api;

use AppBundle\HandlesPost;
use AppBundle\RenderFormErrors;
use AppBundle\RendersJson;
use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * @Route(path="/api/products")
 */
class ProductCreateController extends FOSRestController
{
    use HandlesPost;
    use RenderFormErrors;
    use RendersJson;

    /**
     * @DI\Inject("doctrine")
     *
     * @var Registry
     */
    private $doctrine;

    /**
     * @DI\Inject("jms_serializer")
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * Creates as New Product
     *
     * @Route(path="", name="api_product_create")
     * @Method({"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $form = $this->handlePost(
            $this->createForm('AppBundle\Form\ProductType'),
            $request
        );

        if (null !== ($formErrorResponse = $this->renderFormErrors($form))) {
            return $formErrorResponse;
        }

        $product = $form->getData();

        try {
            $manager = $this
                ->doctrine
                ->getManager();

            $manager->persist($product);
            $manager->flush();
        } catch (\Exception $exception) {
            return $this->renderJson(
                500,
                [
                    'errors' => [
                        $exception->getMessage(),
                    ],
                ]
            );
        }

        return $this->renderJson(
            200,
            [
                'result' => $this->serializer->toArray($product),
            ]
        );
    }
}
