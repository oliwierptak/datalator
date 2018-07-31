<?php

declare(strict_types = 1);

namespace Tests\Datalator\Builder;

use Datalator\DatalatorFactory;
use Datalator\Popo\LoaderConfigurator;
use PHPUnit\Framework\TestCase;

class DatabaseBuilderTest extends TestCase
{
    /**
     * @var string
     */
    protected $schemaDir;

    /**
     * @var string
     */
    protected $dataDir;

    protected function setUp(): void
    {
        $this->schemaDir = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataDir = \TESTS_FIXTURE_DIR . 'database/data/';
    }

    public function testCreate(): void
    {
        $factory = new DatalatorFactory();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $factory->createDatabaseBuilder($configurator);

        $databaseBuilder->create();
    }

    public function testDrop(): void
    {
        $factory = new DatalatorFactory();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $factory->createDatabaseBuilder($configurator);
        $databaseBuilder->create();

        $databaseBuilder->drop();
    }

    public function testPopulate(): void
    {
        $factory = new DatalatorFactory();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
                'foo',
            ]);

        $databaseBuilder = $factory->createDatabaseBuilder($configurator);

        $databaseBuilder->populate();
    }

    public function testPopulateFeatureOne(): void
    {
        $factory = new DatalatorFactory();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'buzz',
            ]);

        $databaseBuilder = $factory->createDatabaseBuilder($configurator);

        $databaseBuilder->populate();
    }

    public function testImport(): void
    {
        $factory = new DatalatorFactory();

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


        $databaseBuilder = $factory->createDatabaseBuilder($featureOneConfigurator);

        $databaseBuilder->import([
            $defaultBarConfigurator,
            $defaultFooConfigurator,
        ]);
    }
}
