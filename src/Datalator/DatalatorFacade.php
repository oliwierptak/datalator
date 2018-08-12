<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;

class DatalatorFacade implements DatalatorFacadeInterface
{
    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    public function setFactory(DatalatorFactoryInterface $factory): void
    {
        $this->factory = $factory;
    }

    protected function getFactory(): DatalatorFactoryInterface
    {
        if ($this->factory === null) {
            $this->factory = new DatalatorFactory();
        }

        return $this->factory;
    }

    public function create(LoaderConfigurator $configurator): void
    {
        $this->getFactory()
            ->createDatabaseBuilder($configurator)
            ->create();
    }

    public function drop(LoaderConfigurator $configurator): void
    {
        $this->getFactory()
            ->createDatabaseBuilder($configurator)
            ->drop();
    }

    public function import(LoaderConfigurator $configurator, array $importConfiguratorCollection): void
    {
        $this->getFactory()
            ->createDatabaseBuilder($configurator)
            ->import($importConfiguratorCollection);
    }

    public function populate(LoaderConfigurator $configurator): void
    {
        $this->getFactory()
            ->createDatabaseBuilder($configurator)
            ->populate();
    }

    public function readFromSchema(LoaderConfigurator $configurator, ReaderConfigurator $readerConfigurator): ReaderValue
    {
        return $this->getFactory()
            ->createDatabaseReader($configurator)
            ->read($readerConfigurator);
    }

    public function readFromData(LoaderConfigurator $configurator, ReaderConfigurator $readerConfigurator): ReaderValue
    {
        return $this->getFactory()
            ->createCsvReader($configurator)
            ->read($readerConfigurator);
    }
}
