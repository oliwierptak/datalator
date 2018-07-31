<?php

declare(strict_types = 1);

namespace Datalator\Loader;

use Datalator\Popo\LoaderConfigurator;

class LoaderValidator implements LoaderValidatorInterface
{
    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function validate(LoaderConfigurator $configurator): void
    {
        $paths = [
            'schema' => $configurator->requireSchemaPath(),
            'data' => $configurator->requireDataPath(),
        ];

        $errors = [];
        foreach ($paths as $name => $path) {
            $directory = new \SplFileInfo(
                $path
            );

            if ($directory->isDir()) {
                continue;
            }

            $errors[$name] = \sprintf(
                "[%s] directory does not exist under: %s",
                $name,
                $path
            );
        }

        if (\count($errors) > 0) {
            $errorString = \implode(".\n", $errors);

            throw new \InvalidArgumentException($errorString);
        }
    }
}
