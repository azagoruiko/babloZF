<?php
namespace bablo\dao;

use bablo\dao\UserDAO;
use bablo\model\User;

class MysqlUserDAO implements UserDAO {
    /**
     *
     * @var \PDO
     */
    private $mysql;
    
    public function __construct(\PDO $pdo) {
        $this->mysql = $pdo;
    }
    
    public function find($id) {
        $stmt = $this->mysql->prepare("SELECT id, email, name, password from user where id=:id");
        $stmt->bindParam('id', $id);
        $stmt->execute();
        while ($user = $stmt->fetchObject('\bablo\model\User')) {
            return $user;
        }
        return null;
    }

    public function findByNameAndPass($name, $pass) {
        $stmt = $this->mysql->prepare("SELECT id, email, name, password from user where email=:email and password=password(:pass)");
        $stmt->bindParam('email', $name);
        $stmt->bindParam('pass', $pass);
        $stmt->execute();
        while ($user = $stmt->fetchObject('\bablo\model\User')) {
            return $user;
        }
        return null;
    }

    public function save(User $user) {
        $stmt = $this->mysql->prepare("UPDATE user set email=:email, name=:name, password=password(:pass)where id=:id");
        $stmt->bindParam('email', $user->getEmail());
        $stmt->bindParam('pass', $user->getPass());
        $stmt->bindParam('name', $user->getName());
        $stmt->bindParam('id', $user->getId);
        return $stmt->execute();
    }

    public function resetPassword($email, $pass) {
        $stmt = $this->mysql->prepare("UPDATE user set password=password(:pass) where email=:email");
        $stmt->bindParam('email', $email);
        $stmt->bindParam('pass', $pass);
        $stmt->execute();
        return $stmt->rowCount();
    }

//put your code here
}
