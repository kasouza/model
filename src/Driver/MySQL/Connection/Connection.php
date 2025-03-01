<?php

namespace Kaso\Model\Driver\MySQL\Connection;

use Exception;
use PDO;

use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\Connection\IConnection;

class Connection implements IConnection
{
    protected $handle;

    public function __construct(ConnectionConfiguration $configuration)
    {
        $dbName = $configuration->getDbName();
        if (empty($dbName)) {
            throw new Exception("Empty dbName");
        }

        $host = $configuration->getHost();
        if (empty($host)) {
            throw new Exception("Empty host");
        }

        $port = $configuration->getPort();
        if (empty($port)) {
            $port = "3306";
        }

        $username = $configuration->getUsername();
        $password = $configuration->getUsername();

        $dsn = "mysql:host={$host};dbname={$dbName}";
        $this->handle = new PDO($dsn, $username, $password);
    }

    public function getHandle() {
        return $this->handle;
    }

    public function close(): void {}
}
