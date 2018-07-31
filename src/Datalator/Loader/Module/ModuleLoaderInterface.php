<?php

declare(strict_types = 1);

namespace Datalator\Loader\Module;

use Datalator\Popo\LoaderConfigurator;

interface ModuleLoaderInterface
{
    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \RuntimeException
     *
     * @return \Datalator\Popo\ModuleConfigurator[]
     */
    public function load(LoaderConfigurator $configurator): array;
}
