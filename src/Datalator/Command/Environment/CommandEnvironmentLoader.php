<?php

declare(strict_types = 1);

namespace Datalator\Command\Environment;

use Datalator\Popo\CommandEnvironmentConfigurator;

class CommandEnvironmentLoader implements CommandEnvironmentLoaderInterface
{
    /**
     * @var string
     */
    protected $iniFilename;

    public function __construct(?string $iniFilename = null)
    {
        $this->iniFilename = $iniFilename ?? '.datalator';
    }

    public function getEnvironmentConfig(string $path): array
    {
        $config = [];
        $configFile = $this->ensurePath($path) . $this->iniFilename;

        if (\is_file($configFile)) {
            $config = \parse_ini_file($configFile, false) ?? [];
        }

        return $config;
    }

    public function getEnvironmentData(CommandEnvironmentConfigurator $configurator): array
    {
        $configurator = $this->ensureConfiguratorPath($configurator);

        $config = $this->getEnvironmentConfig($configurator->getPath());
        $arguments = $this->buildArgumentCollection($configurator);

        $result = \array_merge($config, $arguments);

        return $result;
    }

    protected function ensureConfiguratorPath(CommandEnvironmentConfigurator $configurator): CommandEnvironmentConfigurator
    {
        $path = $this->ensurePath($configurator->getPath());

        $configurator->setPath($path);

        return $configurator;
    }

    protected function ensurePath(?string $path): string
    {
        if (!$path) {
            $path = \getcwd();
        }
        $path = \rtrim($path, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        return $path;
    }

    protected function buildArgumentCollection(CommandEnvironmentConfigurator $configurator): array
    {
        $schemaPath = $configurator->getPath() . \rtrim($configurator->getSchemaPath(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        $dataPath = $configurator->getPath() . \rtrim($configurator->getDataPath(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        $arguments = [
            'schema' => $configurator->getSchemaName(),
            'data' => $configurator->getDataName(),
            'schemaPath' => $schemaPath,
            'dataPath' => $dataPath,
            'modules' => $configurator->getModules(),
        ];

        return $arguments;
    }
}
