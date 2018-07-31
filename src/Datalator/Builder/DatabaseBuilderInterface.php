<?php

declare(strict_types = 1);

namespace Datalator\Builder;

interface DatabaseBuilderInterface
{
    public function create(): void;

    public function drop(): void;

    public function populate(): void;

    /**
     * @param \Datalator\Popo\LoaderConfigurator[] $importConfiguratorCollection
     *
     * @return void
     */
    public function import(array $importConfiguratorCollection): void;
}
