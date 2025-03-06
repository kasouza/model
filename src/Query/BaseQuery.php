<?php

namespace Kaso\Model\Query;

use Exception;
use InvalidArgumentException;
use Kaso\Model\Query\Join;
use Kaso\Model\Hydrator\IHydrator;

abstract class BaseQuery implements IQuery
{
    const VALID_OPERATORS = ["=", "!=", "<=>", "IS", "IN"];

    private ?QueryType $type = null;

    private string|RawQuery $table;

    /** @var (string|RawQuery)[] $select */
    private array $select = [];

    /** @var (Set|RawQuery)[] $set */
    private array $set = [];

    /** @var (Where|RawQuery)[] $where */
    private array $where = [];

    /** @var (Join|RawQuery)[] $joins */
    private array $joins = [];

    public function __construct(
        private ?IHydrator $hydrator = null
    ) {}

    public function update(string|RawQuery $table): self
    {
        $this->setType(QueryType::UPDATE);
        $this->table = $table;
        return $this;
    }

    public function set(string|array|RawQuery $keyOrAssoc, string $value = null): self
    {
        if (is_array($keyOrAssoc)) {
            foreach ($keyOrAssoc as $key => $value) {
                $this->set[] = new Set($key, $value);
            }
        } else if ($keyOrAssoc instanceof RawQuery) {
            $this->set[] = $keyOrAssoc;
        } else {
            $this->set[] = new Set($keyOrAssoc, $value);
        }

        return $this;
    }

    public function select(string|array|RawQuery $select): self
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

    public function from(string|RawQuery $from): self
    {
        $this->ensureTypeAllowed(QueryType::SELECT);
        $this->table = $from;
        return $this;
    }

    public function join(string|RawQuery $tableOrRaw, string $onLeft = null, string $onRight = null, $type = JoinType::INNER): self
    {
        if ($tableOrRaw instanceof RawQuery) {
            $this->joins[] = $tableOrRaw;
        } else {
            if (empty($onLeft)) {
                throw new InvalidArgumentException("The left part of ON of a JOIN cannot be null");
            }

            if (empty($onRight)) {
                throw new InvalidArgumentException("The right part of ON of a JOIN cannot be null");
            }

            $this->joins[] = new Join($tableOrRaw, $onLeft, $onRight, $type);
        }

        return $this;
    }

    public function where(string|array|RawQuery $keyOrAssocOrRaw, mixed $valueOrOperator = null, mixed $value = null): self
    {
        if ($keyOrAssocOrRaw instanceof RawQuery) {
            $this->where[] = $keyOrAssocOrRaw;
        } else if (is_array($keyOrAssocOrRaw)) {
            foreach ($keyOrAssocOrRaw as $key => $valueOrOperator) {
                $this->where[] = new Where($key, null, $valueOrOperator);
            }
        } else if (isset($valueOrOperator) && in_array($valueOrOperator, self::VALID_OPERATORS)) {
            $this->where[] = new Where($keyOrAssocOrRaw, $valueOrOperator, $value);
        } else {
            $this->where[] = new Where($keyOrAssocOrRaw, null, $valueOrOperator);
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
