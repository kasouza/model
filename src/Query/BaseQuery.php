<?php

namespace Kaso\Model\Query;

use Exception;
use Kaso\Model\Driver\MySQL\Query\Join;
use Kaso\Model\Hydrator\IHydrator;

abstract class BaseQuery implements IQuery
{
    private ?QueryType $type = null;

    private string $table;
    private array $select = [];
    private array $set = [];
    private array $where = [];
    private array $joins = [];

    public function __construct(
        private ?IHydrator $hydrator = null
    ) {}

    public function update($table): self
    {
        $this->setType(QueryType::UPDATE);
        $this->table = $table;
        return $this;
    }

    public function set($keyOrAssoc, $value = null): self
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

    public function getHydrator(): ?IHydrator
    {
        return $this->hydrator;
    }

    protected function getType(): ?QueryType
    {
        return $this->type;
    }

    protected function setType(QueryType $type): self
    {
        if (!empty($this->type) && $this->type !== $type) {
            throw new Exception("Query type has already been set");
        }

        $this->type = $type;

        return $this;
    }

    protected function ensureTypeAllowed(QueryType $type): void
    {
        if (!empty($this->type) && $this->type !== $type) {
            throw new Exception("You cannot use this command on a query of type " . $this->type->value);
        }
    }

    protected function getTable() {
        return $this->table;
    }

    protected function getselect() {
        return $this->select;
    }

    protected function getSet() {
        return $this->set;
    }

    protected function getWhere() {
        return $this->where;
    }

    protected function getJoins() {
        return $this->joins;
    }
}
