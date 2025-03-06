<?php

namespace Kaso\Model\DB;

use Kaso\Model\Connection\IConnection;
use Kaso\Model\ConnectionPool\IConnectionPool;
use Kaso\Model\Driver\IDriver;
use Kaso\Model\Hydrator\Hydrator;
use Kaso\Model\Hydrator\IHydrator;
use Kaso\Model\Query\BaseQuery;
use Kaso\Model\Query\IQuery;
use Kaso\Model\Query\RawQuery;
use Kaso\Model\Result\IResult;
use Kaso\Model\Result\Result;

class DB
{
    protected IConnection $connection;

    public function __construct(
        protected IDriver $driver,
        protected IConnectionPool $connectionPool
    ) {
        $this->connectionPool->onAttachToDb($driver);
    }

    public function query(IHydrator|string|null $hydratorOrEntityClass = null): BaseQuery
    {
        $hydrator = null;
        if (!empty($hydratorOrEntityClass)) {
            if ($hydratorOrEntityClass instanceof IHydrator) {
                $hydrator = $hydratorOrEntityClass;
            } else {
                $hydrator = new Hydrator($hydratorOrEntityClass);
            }
        }

        return $this->driver->createQuery($hydrator);
    }

    public function raw(string $queryString, mixed $params = []): RawQuery {
        if (!is_array($params)) {
            $params = [$params];
        }

        return new RawQuery($queryString, $params);
    }

    public function execute(IQuery $query): IResult
    {
        $builtQuery = $query->build();

        var_dump($builtQuery->getBuiltString(), $builtQuery->getParams());

        $stmt = $this->driver->createStatement(
            $this->connectionPool->getConnection(),
            $builtQuery->getBuiltString()
        );

        $stmt->execute($builtQuery->getParams());

        return new Result($stmt, $query->getHydrator());
    }
}
