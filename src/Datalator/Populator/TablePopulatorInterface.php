<?php

declare(strict_types = 1);

namespace Datalator\Populator;

interface TablePopulatorInterface
{
    public function populateTable(): void;
}
