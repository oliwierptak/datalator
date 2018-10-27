<?php

declare(strict_types = 1);

namespace Datalator\Command\Environment;

use Datalator\Popo\IniEnvironmentConfigurator;

interface IniEnvironmentLoaderInterface
{
    /**
     * Specification:
     * - Ensures paths has proper trailing slashes
     * - Loads config data
     * - Builds arguments collection
     * - Merges config data with arguments
     * - Returns merged result as array
     *
     * @param \Datalator\Popo\IniEnvironmentConfigurator $configurator
     *
     * @return array
     */
    public function getEnvironmentData(IniEnvironmentConfigurator $configurator): array;
}
