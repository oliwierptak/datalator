<?php

declare(strict_types = 1);

namespace Datalator\Loader\Schema;

use Datalator\Finder\FileLoaderInterface;
use Datalator\Loader\Module\ModuleLoaderInterface;
use Datalator\Popo\DatabaseConfigurator;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;
use Symfony\Component\Finder\SplFileInfo;

class SchemaLoader implements SchemaLoaderInterface
{
    /**
     * @var \Datalator\Finder\FileLoaderInterface
     */
    protected $schemaFileLoader;

    /**
     * @var \Datalator\Loader\Module\ModuleLoaderInterface
     */
    protected $moduleLoader;

    public function __construct(
        FileLoaderInterface $schemaFileLoader,
        ModuleLoaderInterface $moduleLoader
    ) {
        $this->schemaFileLoader = $schemaFileLoader;
        $this->moduleLoader = $moduleLoader;
    }

    public function load(LoaderConfigurator $configurator): SchemaConfigurator
    {
        $databaseFile = $this->getDatabaseConfigurationFile($configurator);
        $databaseConfigurator = $this->buildDatabaseConfigurator($databaseFile);
        $configurator = $this->updateDefaultModules($configurator, $databaseConfigurator);

        $schemaConfigurator = $this->buildSchemaConfigurator($configurator, $databaseConfigurator, $databaseFile);

        return $schemaConfigurator;
    }

    protected function updateDefaultModules(LoaderConfigurator $configurator, DatabaseConfigurator $databaseConfigurator): \Datalator\Popo\LoaderConfigurator
    {
        if (!\count($configurator->requireModules())) {
            $modules = $databaseConfigurator->requireModules();
            $this->validateLoadedModules($configurator, $modules);

            $configurator->setModules($modules);
        }

        return $configurator;
    }

    protected function buildSchemaConfigurator(LoaderConfigurator $configurator, DatabaseConfigurator $databaseConfigurator, SplFileInfo $databaseFile): SchemaConfigurator
    {
        $databaseName = $databaseConfigurator->requireConnection()->requireDbname();
        $schemaName = $this->getSchemaName($databaseFile);
        $sqlCreate = $this->getSql($databaseFile, $databaseName, 'create.sql');
        $sqlDrop = $this->getSql($databaseFile, $databaseName, 'drop.sql');

        $schemaConfigurator = (new SchemaConfigurator())
            ->setSchemaName($schemaName)
            ->setDatabaseConfigurator($databaseConfigurator)
            ->setSqlCreate($sqlCreate)
            ->setSqlDrop($sqlDrop);

        $loadedModules = $this->moduleLoader->load($configurator);
        $this->validateLoadedModules($configurator, $loadedModules);

        $schemaConfigurator->setLoadedModules($loadedModules);

        return $schemaConfigurator;
    }

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \UnexpectedValueException
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    protected function getDatabaseConfigurationFile(LoaderConfigurator $configurator): SplFileInfo
    {
        $pattern = \sprintf(
            '@^%s/_db/(.*)$@',
            $configurator->requireSchema()
        );

        $schemaFileCollection = $this->schemaFileLoader->load(
            $configurator->requireDataPath(),
            $pattern,
            'database.ini'
        );

        if (\count($schemaFileCollection) !== 1) {
            throw new \UnexpectedValueException(
                \sprintf(
                    'Pattern: "%s" matches too many or not enough files. Only one database.ini file per schema is allowed in: "%s" directory',
                    $pattern,
                    $configurator->requireDataPath()
                )
            );
        }

        $databaseFile = \current($schemaFileCollection);

        return $databaseFile;
    }

    protected function getSqlFileData(SplFileInfo $sqlFile): string
    {
        return \file_get_contents($sqlFile->getPathname());
    }

    protected function getSchemaName(SplFileInfo $schemaFile): string
    {
        $nameTokens = \explode(\DIRECTORY_SEPARATOR, $schemaFile->getRelativePath());

        //pop twice
        \array_pop($nameTokens);
        $name =  \array_pop($nameTokens);

        return $name;
    }

    protected function getSql(SplFileInfo $schemaFile, string $databaseName, string $sqlFilename): string
    {
        $sqlFile = new SplFileInfo(
            $schemaFile->getPath() . \DIRECTORY_SEPARATOR . $sqlFilename,
            $schemaFile->getRelativePath(),
            $schemaFile->getRelativePathname()
        );

        $sql = $this->getSqlFileData($sqlFile);
        $sql = \sprintf($sql, $databaseName);

        return $sql;
    }

    /**
     * @param \Symfony\Component\Finder\SplFileInfo $databaseFile
     *
     * @throws \RuntimeException
     *
     * @return \Datalator\Popo\DatabaseConfigurator
     */
    protected function buildDatabaseConfigurator(SplFileInfo $databaseFile): DatabaseConfigurator
    {
        try {
            $data = \parse_ini_file($databaseFile->getPathname(), true);
            $config = [
                'connection' => [],
                'modules' => [],
            ];

            if (isset($data['connection'])) {
                $config['connection'] = $data['connection'];
            }

            if (isset($data['modules']['load'])) {
                $config['modules'] = $data['modules']['load'];
            }

            $configurator = (new DatabaseConfigurator())
                ->fromArray($config);

            return $configurator;
        } catch (\Throwable $t) {
            $error = \sprintf(
                'Could not load configuration file from "%s". Reason: %s',
                $databaseFile->getPathname(),
                $t->getMessage()
            );

            throw new \RuntimeException($error, $t->getCode(), $t);
        }
    }

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param array $modules
     *
     * @throws \LogicException
     *
     * @return void
     */
    protected function validateLoadedModules(LoaderConfigurator $configurator, array $modules): void
    {
        if (!\count($modules)) {
            throw new \LogicException(
                \sprintf(
                    'No modules enabled for: "%s". Check [modules] section in database.ini under %s',
                    $configurator->getSchema(),
                    $configurator->getDataPath()
                )
            );
        }
    }
}
