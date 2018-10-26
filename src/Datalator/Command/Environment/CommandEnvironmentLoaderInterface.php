<?php

declare(strict_types = 1);

namespace Datalator\Command\Environment;

use Datalator\Popo\CommandEnvironmentConfigurator;

interface CommandEnvironmentLoaderInterface
{
    /**
     * Specification:
     * - Loads config file in INI format
     * - Returns parsed INI file as array
     *
     * @param string $path
     *
     * @return array
     */
    public function getEnvironmentConfig(string $path): array;

    /**
     * Specification:
     * - Ensures paths has proper trailing slashes
     * - Loads config data
     * - Builds arguments collection
     * - Merges config data with arguments
     * - Returns merged result as array
     *
     * @param \Datalator\Popo\CommandEnvironmentConfigurator $configurator
     *
     * @return array
     */
    public function getEnvironmentData(CommandEnvironmentConfigurator $configurator): array;
}
