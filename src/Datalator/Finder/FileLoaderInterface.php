<?php

declare(strict_types = 1);

namespace Datalator\Finder;

interface FileLoaderInterface
{
    /**
     * @param string $schemaDirectory
     * @param null|string $schemaPath
     * @param null|string $schemaFilename
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function load(
        string $schemaDirectory,
        ?string $schemaPath = '@^src/(.*)/schema/(.*)$@',
        ?string $schemaFilename = '*.schema.json'
    ): array;
}
