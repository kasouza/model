<?php

namespace Kaso\Model\Driver\MySQL\Query;

use Exception;
use Kaso\Model\Query\BaseQuery;
use Kaso\Model\Query\BuiltQuery;
use Kaso\Model\Query\IBuiltQuery;
use Kaso\Model\Query\QueryType;
use Kaso\Model\Query\RawQuery;

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

    protected function buildUpdate(): RawQuery
    {
        $table = $this->getTable();
        if (empty($table)) {
            throw new Exception("Invalid query empty UPDATE clause");
        }

        $queryString = "";
        $params = [];

        if ($table instanceof RawQuery) {
            $queryString = "UPDATE {$table->getQueryString()}";
            $params = $table->getParams();
        } else {
            $queryString = "UPDATE {$this->escapeFieldName($table)}";
        }

        return new RawQuery($queryString, $params);
    }

    protected function buildSet(): RawQuery
    {
        if (empty($this->getSet())) {
            throw new Exception("You must SET at least one value in an update query");
        }

        $sets = [];
        $params = [];
        foreach ($this->getSet() as $set) {
            if ($set instanceof RawQuery) {
                $sets[] = $set->getQueryString();
                $params = array_merge($params, $set->getParams());
            } else {
                $sets[] = "{$this->escapeFieldName($set->getKey())} = ?";
                $params[] = $set->getValue();
            }
        }

        return new RawQuery("SET " . implode(",", $sets), $params);
    }

    protected function buildSelect(): RawQuery
    {
        $select = "*";
        $params = [];

        if (!empty($this->getselect())) {
            $selectParts = [];

            foreach ($this->getSelect() as $field) {
                if ($field instanceof RawQuery) {
                    $selectParts[] = $field->getQueryString();
                    $params = array_merge($params, $field->getParams());
                } else {
                    $selectParts[] = $this->escapeFieldName($field);
                }
            }

            $select = implode(",", $selectParts);
        }

        return new RawQuery("SELECT $select", $params);
    }

    protected function buildFrom(): RawQuery
    {
        $table = $this->getTable();
        if (empty($table)) {
            throw new Exception("Invalid query empty FROM clause");
        }

        $queryString = "";
        $params = [];

        if ($table instanceof RawQuery) {
            $queryString = "FROM {$table->getQueryString()}";
            $params = $table->getParams();
        } else {
            $queryString = "FROM {$this->escapeFieldName($table)}";
        }

        return new RawQuery($queryString, $params);
    }

    protected function buildJoins(): RawQuery
    {
        $joins = [];
        $params = [];

        foreach ($this->getJoins() as $join) {
            if ($join instanceof RawQuery) {
                $joins[] = $join->getQueryString();
                $params[] = array_merge($params, $join->getParams());
            } else {
                $tableEscaped = $this->escapeFieldName($join->getTable());
                $onLeftEscaped = $this->escapeFieldName($join->getOnLeft());
                $onRightEscaped = $this->escapeFieldName($join->getOnRight());

                $joins[] = "{$join->getType()->value} JOIN {$tableEscaped} ON {$onLeftEscaped} = {$onRightEscaped}";
            }
        }

        return new RawQuery(implode(" ", $joins), $params);
    }

    protected function buildWhere(): RawQuery
    {
        if (empty($this->getWhere())) {
            return new RawQuery();
        }

        $wheres = [];
        $params = [];

        foreach ($this->getWhere() as $where) {
            if ($where instanceof RawQuery) {
                $wheres[] = $where->getQueryString();
                $params = array_merge($params, $where->getParams());
            } else {
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
        }

        $whereClause = implode(" AND ", $wheres);
        return new RawQuery("WHERE {$whereClause}", $params);
    }

    private function escapeFieldName(string $fieldName): string
    {
        $escaped = str_replace("`", "", $fieldName);
        return "`{$escaped}`";
    }
}
