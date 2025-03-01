<?php

namespace Kaso\Model\Driver\MySQL\Query;

use Exception;
use Kaso\Model\Query\JoinType;
use Kaso\Model\Query\BaseQuery;
use Kaso\Model\Query\QueryType;

class Query extends BaseQuery
{
    protected string $table;

    protected array $select = [];

    protected array $set = [];

    protected array $where = [];
    protected array $joins = [];

    public function update($table): Query
    {
        $this->setType(QueryType::UPDATE);
        $this->table = $table;
        return $this;
    }

    public function set($keyOrAssoc, $value = null): Query
    {
        if (is_array($keyOrAssoc)) {
            foreach ($keyOrAssoc as $key => $value) {
                $this->set[$key] = $value;
            }
        } else {
            $this->set[$keyOrAssoc] = $value;
        }

        return $this;
    }

    public function select($select): self
    {
        $this->setType(QueryType::SELECT);
        $this->select[] = $select;
        return $this;
    }

    public function from(string $from): self
    {
        $this->ensureTypeAllowed(QueryType::SELECT);
        $this->table = $from;
        return $this;
    }

    public function join(string $table, string $on, $type = JoinType::INNER): self
    {
        $this->joins[] = new Join($table, $on, $type);
        return $this;
    }

    public function where($keyOrAssoc, $value = null): self
    {
        if (is_array($keyOrAssoc)) {
            foreach ($keyOrAssoc as $key => $value) {
                $this->where[$key] = $value;
            }
        } else {
            $this->where[$keyOrAssoc] = $value;
        }

        return $this;
    }

    public function build(): string
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

        return implode(" ", array_filter($parts));
    }

    protected function buildSelectQuery()
    {
        $parts = [
            $this->buildSelect(),
            $this->buildFrom(),
            $this->buildJoins(),
            $this->buildWhere()
        ];

        return implode(" ", array_filter($parts));
    }

    protected function buildUpdate()
    {
        return "UPDATE {$this->table}";
    }

    protected function buildSet()
    {
        if (empty($this->set)) {
            throw new Exception("You must SET at least one value in an update query");
        }

        $set = [];
        foreach ($this->set as $key=>$val){
            $set[] = "{$key} = {$val}";
        }

        return "SET " . implode(",", $set);
    }

    protected function buildSelect()
    {
        $select = "*";
        if (!empty($this->select)) {
            $select = implode(", ", $this->select);
        }

        return "SELECT $select";
    }

    protected function buildFrom()
    {
        if (empty($this->table)) {
            throw new Exception("Invalid query empty FROM clause");
        }

        return "FROM {$this->table}";
    }

    protected function buildJoins()
    {
        $joins = [];
        foreach ($this->joins as $join) {
            $joins[] = "{$join->type->value} JOIN {$join->table} ON {$join->on}";
        }

        return implode(" ", $joins);
    }

    protected function buildWhere()
    {
        $wheres = [];
        foreach ($this->where as $field => $value) {
            $wheres[] = "{$field} = {$value}";
        }

        $whereClause = implode(" AND ", $wheres);
        return "WHERE {$whereClause}";
    }
}
