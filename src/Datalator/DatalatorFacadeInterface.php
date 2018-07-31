<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Popo\LoaderConfigurator;

interface DatalatorFacadeInterface
{
    public function setFactory(DatalatorFactoryInterface $factory): void;

    public function create(LoaderConfigurator $configurator): void;

    public function drop(LoaderConfigurator $configurator): void;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Datalator\Popo\LoaderConfigurator[] $importConfiguratorCollection
     *
     * @return void
     */
    public function import(LoaderConfigurator $configurator, array $importConfiguratorCollection): void;

    public function populate(LoaderConfigurator $configurator): void;
}
