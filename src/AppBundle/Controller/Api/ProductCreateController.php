<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Product;
use AppBundle\RendersJson;
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
    use RendersJson;

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
        $form = $this
            ->createForm('AppBundle\Form\ProductType')
            ->submit(
                json_decode($request->getContent(), true)
            );

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return $this->renderJson(400, $errors);
        }

        /** @var Product $product */
        $product = $form->getData();
        $manager = $this->getDoctrine()->getManager();

        try {
            $manager->persist($product);
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
            200,
            [
                'result' => $this->serializer->toArray($product),
            ]
        );
    }
}
