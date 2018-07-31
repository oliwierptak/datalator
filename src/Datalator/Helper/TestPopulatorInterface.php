<?php

declare(strict_types = 1);

namespace Datalator\Helper;

use Datalator\Popo\LoaderConfigurator;

interface TestPopulatorInterface
{
    public function create(): TestPopulatorInterface;

    public function drop(): TestPopulatorInterface;

    public function populate(): TestPopulatorInterface;

    public function extendWith(LoaderConfigurator $configurator): TestPopulatorInterface;

    /**
     * @param \Datalator\Popo\LoaderConfigurator[] $importConfiguratorCollection
     *
     * @return \Datalator\Helper\TestPopulatorInterface
     */
    public function importFrom(array $importConfiguratorCollection): TestPopulatorInterface;

    public function useSchemaName(string $schemaName): TestPopulatorInterface;

    public function useSchemaPath(string $schemaPath): TestPopulatorInterface;

    public function useDataName(string $dataName): TestPopulatorInterface;

    public function useDataPath(string $dataPath): TestPopulatorInterface;

    public function useModules(array $modules): TestPopulatorInterface;

    public function dumpConfiguratorInstance(): LoaderConfigurator;
}
