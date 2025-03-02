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
        return new QueryFragment("UPDATE {$this->escapeFieldName($this->getTable())}");
    }

    protected function buildSet(): QueryFragment
    {
        if (empty($this->getSet())) {
            throw new Exception("You must SET at least one value in an update query");
        }

        $set = [];
        $params = [];
        foreach ($this->getSet() as $key => $val) {
            $set[] = "{$this->escapeFieldName($key)} = ?";
            $params[] = $val;
        }

        return new QueryFragment("SET " . implode(",", $set), $params);
    }

    protected function buildSelect(): QueryFragment
    {
        $select = "*";

        if (!empty($this->getselect())) {
            $selectParts = [];

            foreach ($this->getSelect() as $field) {
                $selectParts[] = $this->escapeFieldName($field);
            }

            $select = implode(",", $selectParts);
        }

        return new QueryFragment("SELECT $select");
    }

    protected function buildFrom(): QueryFragment
    {
        if (empty($this->getTable())) {
            throw new Exception("Invalid query empty FROM clause");
        }

        return new QueryFragment("FROM {$this->escapeFieldName($this->getTable())}");
    }

    protected function buildJoins(): QueryFragment
    {
        $joins = [];
        foreach ($this->getJoins() as $join) {
            // TODO: esacpe "ON"
            $joins[] = "{$join->type->value} JOIN {$this->escapeFieldName($join->table)} ON {$join->on}";
        }

        return new QueryFragment(implode(" ", $joins));
    }

    protected function buildWhere(): QueryFragment
    {
        if (empty($this->getWhere())) {
            return new QueryFragment();
        }

        $wheres = [];
        $params = [];

        foreach ($this->getWhere() as $where) {
            $op = $where->getOperator();
            if ($where->getValue() === null) {
                if (!empty($op) && $op !== "IS") {
                    throw new Exception("Invalid operator for NULL value");
                }

                $op = "IS";
            }

            if (is_array($where->getValue())) {
                if (empty($where->getValue())) {
                    throw new Exception("List parameter cannot be empty");
                }

                if (!empty($op) && $op !== "IN") {
                    throw new Exception("Invalid operator for LIST value");
                }

                $op = "IN";
            }

            if (empty($op)) {
                $op = "=";
            }

            $placeholders = is_array($where->getValue())
                ? implode(",", array_map(fn() => "?", $where->getValue()))
                : "?";

            if ($op === "IN") {
                $placeholders = "({$placeholders})";
            }

            $wheres[] = "{$this->escapeFieldName($where->getKey())} {$op} {$placeholders}";

            if (is_array($where->getValue())) {
                $params = array_merge($params, $where->getValue());
            } else {
                $params[] = $where->getValue();
            }
        }

        $whereClause = implode(" AND ", $wheres);
        return new QueryFragment("WHERE {$whereClause}", $params);
    }

    private function escapeFieldName(string $fieldName): string
    {
        $escaped = str_replace("`", "", $fieldName);
        return "`{$escaped}`";
    }
}
