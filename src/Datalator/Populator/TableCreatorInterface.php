<?php

declare(strict_types = 1);

namespace Datalator\Populator;

interface TableCreatorInterface
{
    public function createTable(): void;

    public function dropTable(): void;

    public function tableExists(): bool;
}
