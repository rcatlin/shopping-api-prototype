<?php

namespace AppBundle\Controller;

use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use FOS\RestBundle\Controller\Annotations\Route;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/products")
 */
class ProductListController
{
    use RendersJson;

    const LIMIT = 10;
    const OFFSET = 0;

    /**
     * @DI\Inject("handler.product")
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
     * @ApiDoc(
     *     description="Get a List of Products",
     *     filters={
     *          {"name"="limit", "dataType"="integer"},
     *          {"name"="offset", "dataType"="integer"}
     *     },
     *     statusCodes={
     *          200="A List of Products were retrieved.",
     *          204="No products were found and no content was returned."
     *     }
     * )
     *
     * @Method({"GET"})
     * @Route(path="", name="api_product_list")
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
}
