<?php

namespace Kaso\Model\Query;

interface IBuiltQuery {
    public function getBuiltString(): string;
    public function getParams(): array;
}
