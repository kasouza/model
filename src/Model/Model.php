<?php

namespace Kaso\Model\Model;

use Exception;
use Kaso\Model\Hydrator\BoolHydrator;
use Kaso\Model\Hydrator\DateTimeHydrator;
use Kaso\Model\Hydrator\DateTimeImmutableHydrator;
use Kaso\Model\Hydrator\FloatHydrator;
use Kaso\Model\Hydrator\Field\IFieldHydrator;
use Kaso\Model\Hydrator\IntHydrator;
use PDOStatement;
use ReflectionClass;
use stdClass;

class Model
{
    protected string $table;
    protected string $idColumn = "id";
    protected string $entityClass = stdClass::class;
    protected ReflectionClass $reflectionClass;

    /**
     * @var IFieldHydrator[] $hydrators
     */
    protected array $hydrators = [];

    public function __construct(
        protected ?DB $db = null
    ) {
        if (empty($this->db)) {
            $this->db = DB::getInstance();
        }

        if (!class_exists($this->entityClass)) {
            throw new Exception("Invalid entity class");
        }

        $this->reflectionClass = new ReflectionClass($this->entityClass);

        $this->afterConstruct();
    }

    protected function afterConstruct() {}

    public function findById(string|int $id)
    {
        /** @var PDOStatement $stmt */
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->idColumn} = ?");
        $stmt->execute([$id]);

        return $this->hydrate($stmt->fetchObject());
    }

    // TODO: Make this hydrate all the properties
    // if the entityClass == stdClass
    public function hydrate(object $obj)
    {
    }

    public function extract(object $object)
    {
    }
}
