<?php

namespace Kaso\Model\Query;

use Exception;
use Kaso\Model\Hydrator\IHydrator;

abstract class BaseQuery implements IQuery
{
    private ?QueryType $type = null;

    public function __construct(
        private ?IHydrator $hydrator = null
    ) {}

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
}
