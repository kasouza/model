<?php

namespace Kaso\Model\Query;

use Kaso\Model\Hydrator\IHydrator;

interface IQuery
{
    public function select($select): self;
    public function update($table): self;
    public function set($keyOrAssoc, $vlaue = null): self;
    public function from(string $from): self;
    public function join(string $table, string $on, $type = JoinType::INNER): self;
    public function where($keyOrAssoc, $value = null): self;
    public function getHydrator(): ?IHydrator;
    public function build(): IBuiltQuery;
}
