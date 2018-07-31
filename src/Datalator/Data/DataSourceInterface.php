<?php

declare(strict_types = 1);

namespace Datalator\Data;

interface DataSourceInterface
{
    public function getData(): array;

    public function isLoaded(): bool;

    public function getName(): string;

    public function getColumns(): array;
}
