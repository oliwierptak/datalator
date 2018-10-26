<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\DatalatorFacade;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatalatorFacadeTest extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';

    /**
     * @var string
     */
    protected $schemaDir;

    /**
     * @var string
     */
    protected $dataDir;

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var ReaderConfigurator
     */
    protected $readerConfiguratorFooOne;

    /**
     * @var ReaderConfigurator
     */
    protected $readerConfiguratorBuzz;

    protected function setUp(): void
    {
        $this->schemaDir = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataDir = \TESTS_FIXTURE_DIR . 'database/data/';

        $this->readerConfiguratorFooOne = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue(1)
            ->setQueryColumn('foo_one_key');

        $this->readerConfiguratorBuzz = (new ReaderConfigurator())
            ->setSource('buzz')
            ->setIdentityValue(1)
            ->setQueryColumn('value');

        $this->factory = new DatalatorFactoryStub();
    }

    public function testCreate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->create($configurator);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);
        $this->assertTrue(
            $databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testDrop(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->create($configurator);

        $facade->drop($configurator);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);
        $this->assertFalse(
            $databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testPopulate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateFeatureOne(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testImport(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $featureOneConfigurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $defaultFooConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
            ]);

        $defaultBarConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
            ]);

        $facade->import($featureOneConfigurator, [
            $defaultBarConfigurator,
            $defaultFooConfigurator,
        ]);

        $valueFooOne = $this->factory->createDatabaseReader($featureOneConfigurator)
            ->read($this->readerConfiguratorFooOne);

        $valueBuzz = $this->factory->createDatabaseReader($featureOneConfigurator)
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('foo-one', $valueFooOne->getDatabaseValue());
        $this->assertEquals('Buzz', $valueBuzz->getDatabaseValue());
    }

    public function testReadFromDatabase(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $value = $facade->readFromDatabase($configurator, $this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testReadDataWithoutIdentity(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue('foo-one')
            ->setIdentityColumn('foo_one_key')
            ->setQueryColumn('foo_one_value');

        $value = $facade->readFromData($configurator, $readerConfigurator);

        $this->assertEquals('Foo One', $value->getDataValue());
    }

    public function testReadDataShouldReturnNullIfIdentityValueIsNotDefinedInDataSource(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo-one')
            ->setIdentityValue('INVALID')
            ->setQueryColumn('foo_one_key');

        $value = $facade->readFromData($configurator, $readerConfigurator);

        $this->assertNull($value->getDataValue());
    }
}
