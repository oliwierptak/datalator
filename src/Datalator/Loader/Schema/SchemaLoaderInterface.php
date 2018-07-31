<?php

declare(strict_types = 1);

namespace Datalator\Loader\Schema;

use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;

interface SchemaLoaderInterface
{
    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \UnexpectedValueException
     *
     * @return \Datalator\Popo\SchemaConfigurator
     */
    public function load(LoaderConfigurator $configurator): SchemaConfigurator;
}
