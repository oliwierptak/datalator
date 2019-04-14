<?php

namespace Datalator\Populator;

interface DatabaseCreatorInterface
{
    public function createDatabase(): void;

    public function dropDatabase(): void;

    public function databaseExists(): bool;
}
