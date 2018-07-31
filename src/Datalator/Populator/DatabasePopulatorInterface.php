<?php

declare(strict_types = 1);

namespace Datalator\Populator;

interface DatabasePopulatorInterface
{
    public function createDatabase(): void;

    public function dropDatabase(): void;

    /**
     * @param \Datalator\Popo\ModuleConfigurator[] $moduleCollection
     * @param array $data
     *
     * @return void
     */
    public function populateDatabase(array $moduleCollection, array $data): void;
}
