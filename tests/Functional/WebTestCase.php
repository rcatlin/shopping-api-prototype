<?php

namespace Tests\Functional;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;

/**
 * WebTestCase is the base class for functional tests.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class WebTestCase extends SymfonyWebTestCase
{
    /**
     * @var bool
     */
    private static $rollbackOnTearDown = false;

    protected function tearDown()
    {
        if (self::$rollbackOnTearDown) {
            if (null === ($entityManager = self::getEntityManager())) {
                throw new \Exception('EntityManager inaccessible when rollback on teardown isrequired.');
            }

            $entityManager->rollback();
            $entityManager->getConnection()->close();
            self::$rollbackOnTearDown = false;
        }

        parent::tearDown();
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        static::bootKernel($options);

        // Begin Transaction and turn off auto-commit
        $entityManager = static::getEntityManager();
        $entityManager->beginTransaction();
        $entityManager->getConnection()->setAutoCommit(false);
        self::$rollbackOnTearDown = true;

        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    protected function getContainer()
    {
        return static::$kernel->getContainer();
    }

    /**
     * @return EntityManager|null
     */
    private static function getEntityManager()
    {
        $container = static::$kernel->getContainer();

        if ($container === null) {
            return null;
        }

        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        return $entityManager;
    }
}
