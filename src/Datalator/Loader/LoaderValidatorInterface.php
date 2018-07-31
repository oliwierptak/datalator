<?php

declare(strict_types = 1);

namespace Datalator\Loader;

use Datalator\Popo\LoaderConfigurator;

interface LoaderValidatorInterface
{
    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function validate(LoaderConfigurator $configurator): void;
}
