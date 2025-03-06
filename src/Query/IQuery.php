<?php

namespace Kaso\Model\Query;

use Kaso\Model\Hydrator\IHydrator;

interface IQuery
{
    public function select(string|array|RawQuery $select): self;
    public function update(string|RawQuery $table): self;
    public function set(string|array|RawQuery $keyOrAssoc, ?string $vlaue = null): self;
    public function from(string|RawQuery $from): self;
    public function join(string|RawQuery $tableOrRaw, string $onLeft = null, string $onRight = null, $type = JoinType::INNER): self;
    public function where(string|array|RawQuery $keyOrAssocOrRaw, mixed $valueOrOperator = null, mixed $value = null): self;
    public function getHydrator(): ?IHydrator;
    public function build(): IBuiltQuery;
}
