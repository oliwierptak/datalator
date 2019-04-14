<?php

declare(strict_types = 1);

namespace Datalator\Reader;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;
use Doctrine\DBAL\Connection;

class DatabaseReader implements ReaderInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function read(ReaderConfigurator $configurator): ReaderValue
    {
        $value = (new ReaderValue());

        $sql = \sprintf(
            "SELECT %s FROM %s WHERE %s = '%s' LIMIT 1",
            $configurator->getQueryColumn(),
            $configurator->getSource(),
            $configurator->getIdentityColumn(),
            $configurator->getIdentityValue()
        );

        try {
            $data = $this->databaseConnection->fetchAssoc($sql);
            if (!$data) {
                return $value;
            }

            if ($configurator->getQueryColumn() === '*') {
                $value->setDatabaseValue($data);
            } else {
                $value->setDatabaseValue($data[$configurator->getQueryColumn()]);
            }
        } catch (\Throwable $throwable) {
            return $value;
        }

        return $value;
    }
}
