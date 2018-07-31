<?php

declare(strict_types = 1);

namespace Datalator\Helper;

use Datalator\DatalatorFacade;
use Datalator\Popo\LoaderConfigurator;

class TestPopulator implements TestPopulatorInterface
{
    /**
     * @var \Datalator\DatalatorFacadeInterface
     */
    protected $databasePopulator;

    /**
     * @var \Datalator\Popo\LoaderConfigurator
     */
    protected $configurator;

    protected function buildPopulator(): self
    {
        if ($this->databasePopulator === null) {
            $this->databasePopulator = new DatalatorFacade();
        }

        return $this;
    }

    protected function buildConfigurator(): self
    {
        if ($this->configurator === null) {
            $this->configurator = (new LoaderConfigurator());
        }

        return $this;
    }

    public function create(): TestPopulatorInterface
    {
        $this
            ->buildConfigurator()
            ->buildPopulator();

        $this->databasePopulator->create(
            $this->configurator
        );

        return $this;
    }

    public function drop(): TestPopulatorInterface
    {
        $this
            ->buildConfigurator()
            ->buildPopulator();

        $this->databasePopulator->drop(
            $this->configurator
        );

        return $this;
    }

    public function populate(): TestPopulatorInterface
    {
        $this
            ->buildConfigurator()
            ->buildPopulator();

        $this->databasePopulator->populate(
            $this->configurator
        );

        return $this;
    }

    public function extendWith(LoaderConfigurator $configurator): TestPopulatorInterface
    {
        $this
            ->buildConfigurator()
            ->buildPopulator();

        $this->databasePopulator->import(
            $this->configurator,
            [$configurator]
        );

        return $this;
    }

    public function importFrom(array $importConfiguratorCollection): TestPopulatorInterface
    {
        $this
            ->buildConfigurator()
            ->buildPopulator();

        $this->databasePopulator->import(
            $this->configurator,
            $importConfiguratorCollection
        );

        return $this;
    }

    public function useSchemaName(string $schemaName): TestPopulatorInterface
    {
        $this->buildConfigurator();
        $this->configurator->setSchema($schemaName);

        return $this;
    }

    public function useSchemaPath(string $schemaPath): TestPopulatorInterface
    {
        $this->buildConfigurator();
        $this->configurator->setSchemaPath($schemaPath);

        return $this;
    }

    public function useDataName(string $dataName): TestPopulatorInterface
    {
        $this->buildConfigurator();
        $this->configurator->setData($dataName);

        return $this;
    }

    public function useDataPath(string $dataPath): TestPopulatorInterface
    {
        $this->buildConfigurator();
        $this->configurator->setDataPath($dataPath);

        return $this;
    }

    public function useModules(array $modules): TestPopulatorInterface
    {
        $this->buildConfigurator();
        $this->configurator->setModules($modules);

        return $this;
    }

    public function dumpConfiguratorInstance(): LoaderConfigurator
    {
        $this->buildConfigurator();

        return clone $this->configurator;
    }
}
