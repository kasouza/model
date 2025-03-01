<?php

namespace Kaso\Model\Result;

interface IResult
{
    public function getFirst(): object;
    public function getAll(): array;

    /**
     * Returns the number of rows affected/returned, depending on the type of the query
     */
    public function count(): int;
}
