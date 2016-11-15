<?php

namespace AppBundle\Controller;

use AppBundle\Handler\ProductHandler;
use AppBundle\RendersJson;
use Doctrine\ORM\EntityNotFoundException;
use Exception\PersistenceException;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
     * @param string $uuid
     *
     * @return Response
     */
    public function delete($uuid)
    {
        try {
            $this->handler->delete($uuid);
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
