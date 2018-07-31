<?php

declare(strict_types = 1);

namespace Datalator\Loader\Module;

use Datalator\Finder\FileLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ModuleConfigurator;
use Datalator\Popo\ModuleTable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\SplFileInfo;

class ModuleLoader implements ModuleLoaderInterface
{
    /**
     * @var \Datalator\Finder\FileLoaderInterface
     */
    protected $schemaFileLoader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        FileLoaderInterface $schemaFileLoader,
        LoggerInterface $logger
    ) {
        $this->schemaFileLoader = $schemaFileLoader;
        $this->logger = $logger;
    }

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \RuntimeException
     *
     * @return \Datalator\Popo\ModuleConfigurator[]
     */
    public function load(LoaderConfigurator $configurator): array
    {
        $pattern = \sprintf(
            "@^%s/(.*)/(.*)$@",
            $configurator->requireSchema()
        );

        $moduleFileCollection = $this->schemaFileLoader->load(
            $configurator->requireDataPath(),
            $pattern,
            'module.ini'
        );

        if (!\count($moduleFileCollection)) {
            $message = \sprintf(
                'Modules not loaded. No "module.ini" found under "%s"',
                $configurator->requireDataPath()
            );
            throw new \RuntimeException($message);
        }

        $modules = [];
        foreach ($moduleFileCollection as $moduleFile) {
            $moduleName = $this->getModuleName($moduleFile);
            $moduleTables = $this->getModuleTables($moduleFile);
            $tables = [];

            foreach ($moduleTables as $tableName) {
                $tableSql = $this->getTableSchemaSql($tableName, $moduleName, $configurator);
                $moduleTable = (new ModuleTable())
                    ->setName($tableName)
                    ->setSql($tableSql);

                $tables[] = $moduleTable;
            }

            $modules[$moduleName] = (new ModuleConfigurator())
                ->setName($moduleName)
                ->setTables($tables);
        }

        $modulesSorted = \array_merge(\array_flip($configurator->requireModules()), $modules);
        $modulesSorted = \array_filter(
            $modulesSorted,
            function ($value) {
                return $value instanceof ModuleConfigurator;
            },
            \ARRAY_FILTER_USE_BOTH
        );

        $result = [];
        foreach ($configurator->requireModules() as $moduleName) {
            foreach ($modulesSorted as $name => $moduleConfigurator) {
                if (!$this->canProcess($moduleName, $moduleConfigurator)) {
                    continue;
                }

                $result[$moduleName] = $moduleConfigurator;
            }
        }

        return $result;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $moduleFile
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    protected function getModuleTables(SplFileInfo $moduleFile): array
    {
        static $cache = [];

        if (isset($cache[$moduleFile->getRelativePathname()])) {
            return $cache[$moduleFile->getRelativePathname()];
        }

        try {
            $data = \parse_ini_file($moduleFile->getPathname(), true);
            $config = [
                'tables' => [
                    'tables' => [],
                ],
            ];

            $config = \array_merge_recursive($config, $data);
            $config = $config['tables']['tables'];

            $cache[$moduleFile->getRelativePathname()] = $config;

            return $config;
        } catch (\Throwable $t) {
            $error = \sprintf(
                'Could not load module configuration file from "%s". Reason: %s',
                $moduleFile->getPathname(),
                $t->getMessage()
            );

            throw new \RuntimeException($error, $t->getCode(), $t);
        }
    }

    protected function getModuleName(SplFileInfo $moduleFile): string
    {
        $nameTokens = \explode(\DIRECTORY_SEPARATOR, $moduleFile->getRelativePath());
        $name = \array_pop($nameTokens);

        return $name;
    }

    protected function canProcess(string $moduleName, ModuleConfigurator $configurator): bool
    {
        return \strcasecmp($moduleName, $configurator->getName()) === 0;
    }

    protected function getTableSchemaSql(
        string $tableName,
        string $moduleName,
        LoaderConfigurator $configurator
    ): string {
        $filename = $configurator->requireSchemaPath() .
            \DIRECTORY_SEPARATOR .
            $configurator->requireSchema() .
            \DIRECTORY_SEPARATOR .
            $moduleName .
            \DIRECTORY_SEPARATOR .
            $tableName . '.sql';

        $relativeName = $configurator->requireSchema() . \DIRECTORY_SEPARATOR . $moduleName;

        $relativePathname = $configurator->requireSchema() .
            \DIRECTORY_SEPARATOR .
            $moduleName .
            \DIRECTORY_SEPARATOR .
            $tableName .
            '.sql';

        $tableSchemaFile = new SplFileInfo(
            $filename,
            $relativeName,
            $relativePathname
        );

        $sql = $this->getSqlFileData($tableSchemaFile, $moduleName);

        return $sql;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $sqlFile
     * @param string $moduleName
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function getSqlFileData(SplFileInfo $sqlFile, string $moduleName): string
    {
        if (!$sqlFile->isReadable()) {
            throw new \RuntimeException(\sprintf(
                'Module "%s" is requesting non existing SQL file "%s"',
                $moduleName,
                $sqlFile->getPathname()
            ));
        }

        return \file_get_contents($sqlFile->getPathname());
    }
}
