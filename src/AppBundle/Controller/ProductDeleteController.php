<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/products")
 */
class ProductDeleteController extends FOSRestController
{
    use RendersJson;

    /**
     * @DI\Inject("handler.product")
     *
     * @var ProductHandler
     */
    private $handler;

    /**
     * @Method({"DELETE"})
     * @Route(path="/{uuid}", name="api_product_delete")
     *
     * @ParamConverter(
     *     "product",
     *     class="AppBundle:Product",
     *     options={
     *         "mapping": {"uuid": "id"}
     *     }
     * )
     *
     * @param Product $product
     *
     * @return Response
     */
    public function delete(Product $product)
    {
        try {
            $this->handler->delete($product);
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
}
