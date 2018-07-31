<?php

declare(strict_types = 1);

namespace Datalator\Populator;

interface TablePopulatorInterface
{
    public function createTable(): void;

    public function dropTable(): void;

    public function populateTable(): void;
}
