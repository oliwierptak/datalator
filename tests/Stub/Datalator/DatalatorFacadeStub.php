<?php

declare(strict_types = 1);

namespace Tests\DatalatorStub\Datalator;

use Datalator\DatalatorFacade;
use Datalator\DatalatorFactoryInterface;

class DatalatorFacadeStub extends DatalatorFacade
{
    protected function getFactory(): DatalatorFactoryInterface
    {
        if ($this->factory === null) {
            $this->factory = new DatalatorFactoryStub();
        }

        return $this->factory;
    }
}
