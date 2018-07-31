<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\DatalatorFacade;
use Datalator\Popo\LoaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatalatorFacadeTest extends TestCase
{
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

    protected function setUp(): void
    {
        $this->schemaDir = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataDir = \TESTS_FIXTURE_DIR . 'database/data/';

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
    }
}
