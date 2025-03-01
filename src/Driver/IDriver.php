<?php

namespace Kaso\Model\Driver;

use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\Connection\IConnection;
use Kaso\Model\Hydrator\IHydrator;
use Kaso\Model\Query\IQuery;
use Kaso\Model\Statement\IStatement;

interface IDriver
{
    public function createConnection(ConnectionConfiguration $configuration): IConnection;
    public function createQuery(?IHydrator $hydrator = null): IQuery;
    public function createStatement(IConnection $connection, IQuery $query): IStatement;
}
