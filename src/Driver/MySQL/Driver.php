<?php

namespace Kaso\Model\Driver\MySQL;

use Exception;
use Kaso\Model\Driver\MySQL\Connection\Connection;
use Kaso\Model\Driver\MySQL\Statement\Statement;
use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\Connection\IConnection;
use Kaso\Model\Driver\IDriver;
use Kaso\Model\Driver\MySQL\Query\Query;
use Kaso\Model\Hydrator\IHydrator;
use Kaso\Model\Query\IQuery;
use Kaso\Model\Statement\IStatement;

class Driver implements IDriver
{
    public function createConnection(ConnectionConfiguration $configuration): IConnection
    {
        return new Connection($configuration);
    }

    public function createQuery(?IHydrator $hydrator = null): IQuery
    {
        return new Query($hydrator);
    }

    public function createStatement(IConnection $connection, string $queryString): IStatement
    {
        if (!($connection instanceof Connection)) {
            throw new Exception("Connection is not from the same driver");
        }

        return new Statement($connection, $queryString);
    }
}
