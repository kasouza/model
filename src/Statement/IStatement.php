<?php

namespace Kaso\Model\Statement;

interface IStatement {
    public function execute(): void;
    public function fetchRow(): object;
    public function fetchAllRows(): array;
    public function rowCount(): int;
}
