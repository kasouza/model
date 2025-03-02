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

        return new BuiltQuery(implode(" ", array_filter($parts)), []);
    }

    protected function buildSelectQuery()
    {
        $parts = [
            $this->buildSelect(),
            $this->buildFrom(),
            $this->buildJoins(),
            $this->buildWhere()
        ];

        return new BuiltQuery(implode(" ", array_filter($parts)), []);
    }

    protected function buildUpdate()
    {
        return "UPDATE {$this->getTable()}";
    }

    protected function buildSet()
    {
        if (empty($this->getSet())) {
            throw new Exception("You must SET at least one value in an update query");
        }

        $set = [];
        foreach ($this->getSet() as $key=>$val){
            $set[] = "{$key} = {$val}";
        }

        return "SET " . implode(",", $set);
    }

    protected function buildSelect()
    {
        $select = "*";

        if (!empty($this->getselect())) {
            $select = implode(", ", $this->getselect());
        }

        return "SELECT $select";
    }

    protected function buildFrom()
    {
        if (empty($this->getTable())) {
            throw new Exception("Invalid query empty FROM clause");
        }

        return "FROM {$this->getTable()}";
    }

    protected function buildJoins()
    {
        $joins = [];
        foreach ($this->getJoins() as $join) {
            $joins[] = "{$join->type->value} JOIN {$join->table} ON {$join->on}";
        }

        return implode(" ", $joins);
    }

    protected function buildWhere()
    {
        $wheres = [];
        foreach ($this->getWhere() as $field => $value) {
            $wheres[] = "{$field} = {$value}";
        }

        $whereClause = implode(" AND ", $wheres);
        return "WHERE {$whereClause}";
    }
}
