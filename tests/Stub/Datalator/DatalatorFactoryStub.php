<?php

declare(strict_types = 1);

namespace Tests\DatalatorStub\Datalator;

use Datalator\DatalatorFactory;
use Datalator\Logger\LoggerFactoryInterface;
use Tests\DatalatorStub\Datalator\Logger\LoggerFactoryStub;

class DatalatorFactoryStub extends DatalatorFactory
{
    protected function createLoggerFactory(): LoggerFactoryInterface
    {
        return new LoggerFactoryStub();
    }
}
