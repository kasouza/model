<?php

namespace Kaso\Model\ConnectionPool;

use Exception;
use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\Connection\IConnection;
use Kaso\Model\Driver\IDriver;

class SimpleConnectionPool implements IConnectionPool
{
    protected ?IDriver $driver = null;

    public function __construct(
        protected ConnectionConfiguration $connectionConfiguration
    ) {}

    public function onAttachToDb(IDriver $driver) {
        if (!empty($this->driver)) {
            throw new Exception("Connection pool has already been attached to a DB");
        }

        $this->driver = $driver;
    }

    public function getConnection(): IConnection
    {
        return $this->driver->createConnection($this->connectionConfiguration);
    }
}
