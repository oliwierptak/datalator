<?php

declare(strict_types = 1);

namespace Datalator\Helper;

use Datalator\DatalatorFactory;
use Datalator\DatalatorFactoryInterface;
use Datalator\Popo\LoaderConfigurator;

trait TraitHelper
{
    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var \Datalator\Popo\LoaderConfigurator
     */
    protected $configurator;

    /**
     * @var bool
     */
    protected $isDone = false;

    protected function rebuild(): void
    {
        $this->isDone = false;
    }

    protected function done(): void
    {
        $this->isDone = true;
    }

    protected function configure(): self
    {
        if ($this->configurator === null) {
            $this->configurator = (new LoaderConfigurator());
        }

        return $this;
    }

    protected function getFactory(): DatalatorFactoryInterface
    {
        if ($this->factory === null) {
            $this->factory = new DatalatorFactory();
        }

        return $this->factory;
    }

    public function setFactory(DatalatorFactoryInterface $factory): void
    {
        $this->factory = $factory;
    }

    public function setConfigurator(LoaderConfigurator $configurator): void
    {
        $this->configurator = $configurator;
    }

    public function useSchemaName(string $schemaName): self
    {
        $this->configure();
        $this->configurator->setSchema($schemaName);

        $this->rebuild();

        return $this;
    }

    public function useSchemaPath(string $schemaPath): self
    {
        $this->configure();
        $this->configurator->setSchemaPath($schemaPath);

        $this->rebuild();

        return $this;
    }

    public function useDataName(string $dataName): self
    {
        $this->configure();
        $this->configurator->setData($dataName);

        $this->rebuild();

        return $this;
    }

    public function useDataPath(string $dataPath): self
    {
        $this->configure();
        $this->configurator->setDataPath($dataPath);

        $this->rebuild();

        return $this;
    }

    public function useModules(array $modules): self
    {
        $this->configure();
        $this->configurator->setModules($modules);

        $this->rebuild();

        return $this;
    }

    public function dumpConfiguratorInstance(): LoaderConfigurator
    {
        $this->configure();

        return clone $this->configurator;
    }
}
