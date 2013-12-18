<?php

namespace Saxulum\Tests\DoctrineMongoDb\Silex\Provider;

use Doctrine\MongoDB\Connection;
use Saxulum\DoctrineMongoDb\Silex\Provider\DoctrineMongoDbProvider;
use Silex\Application;

class DoctrineMongoDbProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleConnection()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('mongo is not available');
        }

        $app = new Application();
        $app->register(new DoctrineMongoDbProvider());

        /** @var Connection $mongodb */
        $mongodb = $app['mongodb'];

        $this->assertSame($app['mongodbs']['default'], $mongodb);
        $this->assertInstanceOf('Doctrine\MongoDB\Connection', $mongodb);

        $this->assertSame($app['mongodbs.config']['default'], $app['mongodb.config']);
        $this->assertInstanceOf('Doctrine\MongoDB\Configuration', $app['mongodb.config']);

        $this->assertSame($app['mongodbs.event_manager']['default'], $app['mongodb.event_manager']);
        $this->assertInstanceOf('Doctrine\Common\EventManager', $app['mongodb.event_manager']);

        $database = $mongodb->selectDatabase('saxulum-doctrine-mongodb-provider');
        $collection = $database->selectCollection('sample');

        $document = array('key' => 'value');
        $collection->insert($document);

        $this->assertArrayHasKey('_id', $document);

        $database->dropCollection('sample');
    }
}
