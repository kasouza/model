<?php

namespace Kaso\Model\ConnectionPool;

use Kaso\Model\Connection\IConnection;
use Kaso\Model\Driver\IDriver;

interface IConnectionPool {
    public function getConnection(): IConnection;
    public function onAttachToDb(IDriver $driver);
}
