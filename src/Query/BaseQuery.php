<?php

namespace Kaso\Model\Query;

use Exception;
use Kaso\Model\Query\Join;
use Kaso\Model\Hydrator\IHydrator;

abstract class BaseQuery implements IQuery
{
    const VALID_OPERATORS = ["=", "!=", "<=>", "IS", "IN"];

    private ?QueryType $type = null;

    private string $table;
    private array $select = [];
    private array $set = [];
    private array $where = [];
    private array $joins = [];

    public function __construct(
        private ?IHydrator $hydrator = null
    ) {}

    public function update(string $table): self
    {
        $this->setType(QueryType::UPDATE);
        $this->table = $table;
        return $this;
    }

    public function set(string | array $keyOrAssoc, string $value = null): self
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

    public function select(string | array $select): self
    {
        $this->setType(QueryType::SELECT);
        if (is_array($select)) {
            foreach ($select as $sel) {
                $this->select[] = $sel;
            }
        } else {
            $this->select[] = $select;
        }

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

    public function where(string | array $keyOrAssoc, mixed $valueOrOperator = null, mixed $value = null): self
    {
        if (is_array($keyOrAssoc)) {
            foreach ($keyOrAssoc as $key => $valueOrOperator) {
                $this->where[] = new Where($key, null, $valueOrOperator);
            }
        } else if (isset($valueOrOperator) && in_array($valueOrOperator, self::VALID_OPERATORS)) {
            $this->where[] = new Where($keyOrAssoc, $valueOrOperator, $value);
        } else {
            $this->where[] = new Where($keyOrAssoc, null, $valueOrOperator);
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

    protected function getTable()
    {
        return $this->table;
    }

    protected function getselect()
    {
        return $this->select;
    }

    protected function getSet()
    {
        return $this->set;
    }

    /**
     * @return Where[]
     */
    protected function getWhere(): array
    {
        return $this->where;
    }

    protected function getJoins()
    {
        return $this->joins;
    }
}
