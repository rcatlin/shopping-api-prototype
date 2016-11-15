<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityNotFoundException;
use Exception\InvalidFormException;
use Exception\PersistenceException;
use InvalidArgumentException;
use JMS\DiExtraBundle\Annotation as DI;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactory;

/**
 * @DI\Service("handler.product")
 */
class ProductHandler
{
    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @DI\InjectParams({
     *     "formFactory"=@DI\Inject("form.factory"),
     *     "objectManager"=@DI\Inject("doctrine.orm.default_entity_manager"),
     *     "repository"=@DI\Inject("repository.product")
     * })
     *
     * @param FormFactory $formFactory
     * @param ObjectManager $objectManager
     * @param ProductRepository $repository
     */
    public function __construct(FormFactory $formFactory, ObjectManager $objectManager, ProductRepository $repository)
    {
        $this->formFactory = $formFactory;
        $this->objectManager = $objectManager;
        $this->repository = $repository;
    }

    /**
     * @param string $uuid
     *
     * @return Product
     *
     * @throws InvalidArgumentException
     * @throws EntityNotFoundException
     */
    public function get($uuid)
    {
        $binaryUuid = $binaryUuid = Uuid::fromString($uuid);

        $product = $this->repository->find($binaryUuid);

        if ($product === null) {
            throw new EntityNotFoundException('Product not found with UUID: ' . $uuid);
        }

        return $product;
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getList($offset, $limit)
    {
        return $this->repository->findBy([], null, (int) $limit, (int) $offset);
    }

    /**
     * @param array $parameters
     *
     * @return Product
     *
     * @throws InvalidFormException
     * @throws PersistenceException
     */
    public function post(array $parameters)
    {
        $form = $this->processForm($parameters, 'POST');

        $product = $form->getData();

        try {
            $this->objectManager->persist($product);
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $product;
    }

    /**
     * @param $uuid
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     * @throws InvalidFormException
     * @throws PersistenceException
     */
    public function put($uuid, array $parameters)
    {
        $form = $this->processForm($parameters, 'PUT', $this->get($uuid));

        $product = $form->getData();

        try {
            $this->objectManager->flush();
        } catch (\Exception $exception) {
            throw new PersistenceException();
        }

        return $product;
    }

    /**
     * @param array $parameters
     * @param string $method
     * @param Product|null $product
     *
     * @return \Symfony\Component\Form\FormInterface
     *
     * @throws InvalidFormException
     */
    private function processForm(array $parameters, $method, $product = null)
    {
        $form = $this
            ->formFactory
            ->create('AppBundle\Form\ProductType', $product)
            ->submit($parameters, ($method === 'PUT'));

        if ($form->isValid()) {
            return $form;
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        throw new InvalidFormException($errors);
    }
}
