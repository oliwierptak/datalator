<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Popo\LoaderConfigurator;

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

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Datalator\Popo\LoaderConfigurator[] $importConfiguratorCollection
     *
     * @return void
     */
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
}
