<?php

declare(strict_types = 1);

namespace Tests\Datalator\Builder;

use Datalator\Helper\TestDatabaseHelper;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatabaseBuilderTest extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';

    const SCHEMA_PATH = \TESTS_FIXTURE_DIR . 'database/schema/';
    const DATA_PATH = \TESTS_FIXTURE_DIR . 'database/data/';

    /**
     * @var string
     */
    protected $schemaDir;

    /**
     * @var string
     */
    protected $dataDir;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorFooOne;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorBuzz;

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var TestDatabaseHelper
     */
    protected static $databaseHelper;

    public static function setUpBeforeClass(): void
    {
        static::cleanState();
    }

    protected static function cleanState(): void
    {
        self::setupDatabaseHelper();

        static::$databaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR);
    }

    protected static function setupDatabaseHelper(): void
    {
        if (static::$databaseHelper === null) {
            $configurator = (new LoaderConfigurator())
                ->setSchema('default')
                ->setData('default')
                ->setSchemaPath(static::SCHEMA_PATH)
                ->setDataPath(static::DATA_PATH);

            static::$databaseHelper = new TestDatabaseHelper();
            static::$databaseHelper->setFactory(new DatalatorFactoryStub());
            static::$databaseHelper->setConfigurator($configurator);
        }
    }

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

        static::cleanState();
    }

    public function testCreate(): void
    {
        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);

        $databaseBuilder->create();

        $this->assertTrue($databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR));
    }

    public function testDrop(): void
    {
        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);
        $databaseBuilder->create();

        $databaseBuilder->drop();

        $this->assertFalse($databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR));
    }

    public function testPopulate(): void
    {
        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);

        $databaseBuilder->populate();

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateFeatureOne(): void
    {
        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'buzz',
            ]);

        $databaseBuilder = $this->factory->createDatabaseBuilder($configurator);

        $databaseBuilder->populate();

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testImport(): void
    {
        $featureOneConfigurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'buzz',
            ]);

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

        $databaseBuilder = $this->factory->createDatabaseBuilder($featureOneConfigurator);

        $databaseBuilder->import([
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
}
