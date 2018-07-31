<?php

declare(strict_types = 1);

namespace Tests\Datalator\Helper;

use Datalator\Helper\TestPopulator;
use PHPUnit\Framework\TestCase;

class DatalatorHelperTest extends TestCase
{
    /**
     * @var string
     */
    protected $schemaPath;

    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var \Datalator\Helper\TestPopulator
     */
    protected $testPopulator;

    protected function setUp(): void
    {
        $this->schemaPath = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataPath = \TESTS_FIXTURE_DIR . 'database/data/';

        $this->testPopulator = (new TestPopulator())
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath);
    }

    public function testCreate(): void
    {
        $this->testPopulator->create();
    }

    public function testDrop(): void
    {
        $this->testPopulator->drop();
    }

    public function testPopulate(): void
    {
        $this->testPopulator->populate();
    }

    public function testUseCreate(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->create();
    }

    public function testUseDrop(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->drop();
    }

    public function testUsePopulate(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->populate();
    }

    public function testUseImportFrom(): void
    {
        $defaultConfiguratorBar = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfiguratorBar->setModules(['bar']);

        $defaultConfiguratorFoo = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfiguratorFoo->setModules(['foo']);

        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->importFrom(
                [$defaultConfiguratorBar, $defaultConfiguratorFoo]
            );
    }

    public function testUseExtend(): void
    {
        $defaultConfigurator = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfigurator->setModules(['bar']);

        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->extendWith(
                $defaultConfigurator
            );
    }
}
