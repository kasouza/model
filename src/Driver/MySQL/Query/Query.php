<?php

namespace Kaso\Model\Driver\MySQL\Query;

use Exception;
use Kaso\Model\Query\BaseQuery;
use Kaso\Model\Query\BuiltQuery;
use Kaso\Model\Query\IBuiltQuery;
use Kaso\Model\Query\QueryType;

class Query extends BaseQuery
{
    public function build(): IBuiltQuery
    {
        switch ($this->getType()) {
            case QueryType::UPDATE:
                return $this->buildUpdateQuery();

            case QueryType::SELECT:
            default:
                return $this->buildSelectQuery();
        }
    }

    protected function buildUpdateQuery()
    {
        $parts = [
            $this->buildUpdate(),
            $this->buildJoins(),
            $this->buildSet(),
            $this->buildWhere()
        ];

        $stringParts = [];
        $params = [];

        foreach ($parts as $part) {
            $stringParts[] = $part->getQueryString();
            $params = array_merge($params, $part->getParams());
        }

        return new BuiltQuery(implode(" ", array_filter($stringParts)), $params);
    }

    protected function buildSelectQuery()
    {
        $parts = [
            $this->buildSelect(),
            $this->buildFrom(),
            $this->buildJoins(),
            $this->buildWhere()
        ];

        $stringParts = [];
        $params = [];

        foreach ($parts as $part) {
            $stringParts[] = $part->getQueryString();
            $params = array_merge($params, $part->getParams());
        }

        return new BuiltQuery(implode(" ", array_filter($stringParts)), $params);
    }

    protected function buildUpdate(): QueryFragment
    {
        return new QueryFragment("UPDATE {$this->getTable()}");
    }

    protected function buildSet(): QueryFragment
    {
        if (empty($this->getSet())) {
            throw new Exception("You must SET at least one value in an update query");
        }

        $set = [];
        $params = [];
        foreach ($this->getSet() as $key=>$val){
            $set[] = "{$key} = ?";
            $params[] = $val;
        }

        return new QueryFragment("SET " . implode(",", $set), $params);
    }

    protected function buildSelect(): QueryFragment
    {
        $select = "*";

        if (!empty($this->getselect())) {
            $select = implode(", ", $this->getselect());
        }

        return new QueryFragment("SELECT $select");
    }

    protected function buildFrom(): QueryFragment
    {
        if (empty($this->getTable())) {
            throw new Exception("Invalid query empty FROM clause");
        }

        return new QueryFragment("FROM {$this->getTable()}");
    }

    protected function buildJoins(): QueryFragment
    {
        $joins = [];
        foreach ($this->getJoins() as $join) {
            $joins[] = "{$join->type->value} JOIN {$join->table} ON {$join->on}";
        }

        return new QueryFragment(implode(" ", $joins));
    }

    protected function buildWhere(): QueryFragment
    {
        $where = $this->getWhere();
        if (empty($where)) {
            return new QueryFragment();
        }

        $wheres = [];
        $params = [];

        foreach ($this->getWhere() as $field => $value) {
            $wheres[] = "{$field} = ?";
            $params[] = $value;
        }

        $whereClause = implode(" AND ", $wheres);
        return new QueryFragment("WHERE {$whereClause}", $params);
    }
}
