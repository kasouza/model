<?php

define("FCPATH", __DIR__ . "/../");
require_once FCPATH . "/vendor/autoload.php";

use Kaso\Model\Connection\ConnectionConfiguration;
use Kaso\Model\ConnectionPool\SimpleConnectionPool;
use Kaso\Model\DB\DB;
use Kaso\Model\Driver\MySQL\Driver;
use Kaso\Model\Entity\Entity;
use Kaso\Model\Hydrator\Hydrator;
use Kaso\Model\Query\RawQuery;

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
// TODO: UPDATE with JOINS
// TODO: Possibilitar fazer selects com . (ex: user.name -> `user`.`name`)
// TODO: Possibilitar fazer selects com * (ex: * -> *; user.* -> `user`.*)
// TODO: Possibilitar fazer JOIN com . (ex: user.id = post.user_id -> `user`.`id` = `post`.`user_id`)
// TODO: OR WHERE
// TODO: PARENTHESIS
// TODO: Raw queries (with parameters)

class Relatorio
{
    public int $average;
}

$query = $db->query(new Hydrator(User::class))
    ->select($db->raw("users.*"))
    ->from($db->raw("users"))
    ->join($db->raw("LEFT JOIN posts ON users.id = posts.user_id"))
    ->where($db->raw("id IN (?, ?)", [1, 2]));
$result = $db->execute($query);
print_r($result->getAll());
die;

//$query = $db->query()
//->update($db->raw("users"))
//->where($db->raw("name = ?", ['Jhon']))
//->set($db->raw("name = 'Lemom'"));

$result = $db->execute($query);
var_dump($result->getFirst());

exit;
