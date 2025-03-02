<?php

namespace Kaso\Model\Query;

use Kaso\Model\Hydrator\IHydrator;

interface IQuery
{
    public function select(string|array $select): self;
    public function update(string $table): self;
    public function set(string | array $keyOrAssoc, ?string $vlaue = null): self;
    public function from(string $from): self;
    public function join(string $table, string $on, $type = JoinType::INNER): self;
    public function where(string | array $keyOrAssoc, mixed $valueOrOperator = null, mixed $value = null): self;
    public function getHydrator(): ?IHydrator;
    public function build(): IBuiltQuery;
}
