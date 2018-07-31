<?php

declare(strict_types = 1);

namespace Tests\Datalator\Schema\Module;

use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ModuleConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class ModuleLoaderTest extends TestCase
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

    public function testLoadShouldNotLoadWithoutModules(): void
    {
        $moduleLoader = $this->factory->createModuleLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $modules = $moduleLoader->load($configurator);

        $this->assertEmpty($modules);
    }

    public function testLoadShouldIgnoreNonExistingModules(): void
    {
        $moduleLoader = $this->factory->createModuleLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
                'bar',
                'nonExistingModule',
                'foo',
            ]);

        $modules = $moduleLoader->load($configurator);

        $this->assertArrayHasKey('foo', $modules);
        $this->assertArrayHasKey('bar', $modules);
        $this->assertArrayNotHasKey('nonExistingModule', $modules);

        $isModuleLoaded = \array_key_exists('nonExistingModule', $modules);
        $this->assertFalse($isModuleLoaded);

        $isModuleLoaded = \array_key_exists('foo', $modules);
        $this->assertTrue($isModuleLoaded);
    }

    public function testLoadShouldPreserveOrder(): void
    {
        $moduleLoader = $this->factory->createModuleLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
                'bar',
            ]);

        $modules = $moduleLoader->load($configurator);

        $this->assertArrayHasKey('foo', $modules);
        $this->assertArrayHasKey('bar', $modules);

        $firstModule = \current($modules);
        $this->assertInstanceOf(ModuleConfigurator::class, $firstModule);
        $this->assertEquals('foo', $firstModule->getName());
    }
}
