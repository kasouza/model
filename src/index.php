<?php

define("FCPATH", __DIR__ . "/../");
require_once FCPATH . "/vendor/autoload.php";

use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\ConnectionPool\SimpleConnectionPool;
use Kaso\Model\DB\DB;
use Kaso\Model\Driver\MySQL\Driver;
use Kaso\Model\Entity\Entity;
use Kaso\Model\Hydrator\Hydrator;

define("DB_URL", "mysql:host=127.0.0.1;dbname=teste");
define("DB_USER", "root");
define("DB_PASSWORD", "root");

class User extends Entity
{
    public int $id;
    public string $name;
    public int $age;
    public float $score;
    public bool $flag;
    public DateTime $createdAt;
}

//class UserModel extends Model
//{
//protected string $table = "users";
//protected string $entityClass = User::class;

//protected function afterConstruct() {}
//}

//$query = new Query();
//echo $query
//->select("*")
//->from("users")
//->where("id", 1)
//->join("saske", "user.id = saske.user_id")
//->build();

$db = new DB(
    new Driver(),
    new SimpleConnectionPool(
        new ConnectionConfiguration(
            "127.0.0.1",
            "3306",
            "teste",
            "root",
            "root"
        )
    )
);

// TODO: INSERT queries
// TODO: DELETE queries
// TODO: Raw queries (with parameters)

class Relatorio {
    public int $average;
}

$query = $db->query(new Hydrator(User::class))
    ->from("users")
    ->where("id", [ 1, 2])
;
$result = $db->execute($query);
print_r($result->getAll());
die;

//$query = $db->query()
    //->update("users")
    //->where("name", "Jhon")
    //->set("name", "Lemon");
//var_dump($query->build());
//die;

$result = $db->execute($query);
var_dump($result->getFirst());

exit;
