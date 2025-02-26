<?php

define("DB_URL", "mysql:host=127.0.0.1;dbname=teste");
define("DB_USER", "root");
define("DB_PASSWORD", "root");

class DB extends PDO
{
    private static DB $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new DB(
                DB_URL,
                DB_USER,
                DB_PASSWORD
            );
        }

        return self::$instance;
    }
}

interface IHydrator
{
    public function hydrate($value);
    public function extract($value);
}

enum Algo: string
{
    case ALGO1 = "algo1";
    case ALGO2 = "algo2";
}

class AlgoHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return Algo::from($value);
    }

    public function extract($value)
    {
        return $value->value;
    }
}

class DateTimeHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return new DateTime($value);
    }

    public function extract($value)
    {
        return $value->format("Y-m-d H:i:s");
    }
}

class DateTimeImmutableHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return new DateTimeImmutable($value);
    }

    public function extract($value)
    {
        return $value->format("Y-m-d H:i:s");
    }
}

class IntHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return intval($value);
    }

    public function extract($value)
    {
        return $value;
    }
}

class FloatHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return floatval($value);
    }

    public function extract($value)
    {
        return $value;
    }
}

class BoolHydrator implements IHydrator
{
    public function hydrate($value)
    {
        return boolval($value);
    }

    public function extract($value)
    {
        return $value ? 1 : 0;
    }
}

class BaseModel
{
    protected string $table;
    protected string $idColumn = "id";
    protected string $entityClass = stdClass::class;
    protected ReflectionClass $reflectionClass;

    /**
     * @var IHydrator[] $hydrators
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

        $this->addHydrator("bool", new BoolHydrator);
        $this->addHydrator("int", new IntHydrator);
        $this->addHydrator("float", new FloatHydrator);
        $this->addHydrator("DateTime", new DateTimeHydrator);
        $this->addHydrator("DateTimeImmutable", new DateTimeImmutableHydrator);

        $this->afterConstruct();
    }

    protected function afterConstruct() {}

    public function addHydrator(string $type, IHydrator $hydrator)
    {
        $this->hydrators[$type] = $hydrator;
    }

    public function getHydrator(string $type): ?IHydrator
    {
        return $this->hydrators[$type] ?? null;
    }

    public function findById(string|int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->idColumn} = ?");
        $stmt->execute([$id]);

        return $this->hydrate($stmt->fetchObject());
    }

    public function hydrate(object $obj)
    {
        $hydrated = new $this->entityClass;
        $fields = $this->reflectionClass->getProperties();

        foreach ($fields as $field) {
            $name = $field->getName();
            $type = $field->getType();
            $typeName = $type->getName();

            if (!isset($obj->$name)) {
                if ($type->allowsNull()) {
                    $hydrated->$name = null;
                } else {
                    throw new Exception("{$name} must not be empty");
                }
            } else {
                $value = $obj->$name;

                $hydrator = $this->getHydrator($typeName);
                if (!empty($hydrator)) {
                    $value = $hydrator->hydrate($value);
                }

                $hydrated->$name = $value;
            }
        }

        return $hydrated;
    }

    public function extract(object $object)
    {
        $extracted = new stdClass;

        $fields = $this->reflectionClass->getProperties();

        foreach ($fields as $field) {
            $name = $field->getName();
            $type = $field->getType();
            $typeName = $type->getName();

            if (!isset($object->$name)) {
                $extracted->$name = $object->$name;
            } else {
                $value = $object->$name;

                $hydrator = $this->getHydrator($typeName);
                if (!empty($hydrator)) {
                    $value = $hydrator->extract($value);
                }

                $extracted->$name = $value;
            }
        }

        return $extracted;
    }
}

class Entity {}

class User extends Entity
{
    public int $id;
    public string $name;
    public int $age;
    public float $score;
    public bool $flag;
    public Algo $algo;
    public DateTime $createdAt;
}

class UserModel extends BaseModel
{
    protected string $table = "users";
    protected string $entityClass = User::class;

    protected function afterConstruct()
    {
        $this->addHydrator("Algo", new AlgoHydrator);
    }
}

$model = new UserModel();
$user = $model->findById(1);

$extracted = $model->extract($user);
var_dump($user);
var_dump($extracted);
