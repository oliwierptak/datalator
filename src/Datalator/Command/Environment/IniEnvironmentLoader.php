<?php

declare(strict_types = 1);

namespace Datalator\Command\Environment;

use Datalator\Popo\IniEnvironmentConfigurator;

class IniEnvironmentLoader implements IniEnvironmentLoaderInterface
{
    public function getEnvironmentData(IniEnvironmentConfigurator $configurator): array
    {
        $configurator = $this->ensureConfiguratorPaths($configurator);

        $config = $this->loadIni($configurator->requirePath(), $configurator->requireIniFilename());
        $result = \array_merge($config, $configurator->toArray());

        return $result;
    }

    protected function ensureConfiguratorPaths(IniEnvironmentConfigurator $configurator): IniEnvironmentConfigurator
    {
        $path = $this->ensurePath($configurator->getPath());
        $schemaPath = $path . \rtrim($configurator->requireSchemaPath(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        $dataPath = $path . \rtrim($configurator->requireDataPath(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        $configurator->setPath($path);
        $configurator->setSchemaPath($schemaPath);
        $configurator->setDataPath($dataPath);

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

    protected function loadIni(string $path, string $iniFilename): array
    {
        $config = [];
        $configFile = $path . $iniFilename;

        if (\is_file($configFile)) {
            $config = \parse_ini_file($configFile, false) ?? [];
        }

        return $config;
    }
}
