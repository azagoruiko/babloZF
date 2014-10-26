<?php
namespace bablo\dao;

use bablo\model\User;
use RuntimeException;

/**
 * Description of CSVUserDAO
 *
 * @author andrii
 */
class CSVUserDAO implements UserDAO {
    static $file =  'users.csv';
    private $users = [];
    
    public function __construct() {
        self::$file = \Config::$dataPath . 'users.csv';
        $filename = self::$file;
        if (!file_exists($filename)) {
            throw new RuntimeException("File not found");
        }
        $data = file($filename);
        foreach ($data as $item) {
            $parts = explode(',', trim($item));
            $user = new User();
            $user->setId($parts[0]);
            $user->setName($parts[1]);
            $user->setPass($parts[2]);
            // TODO: add more fields
            $this->users[$user->getId()] = $user;
        }
    }
    
    public function find($id) {
        return $this->users[$id];
    }

    public function save(User $user) {
        if(empty($user->getId())){
            $id = rand(1,PHP_INT_MAX);
            $user->setId($id);
        }
        
        $str ='';
        $this->users[$user->getId()] = $user;
        foreach ($this->users as $_user){
            $str.=$_user->getId().","
                    .$_user->getName().","
                    .$_user->getPass()."\n";
        }
        file_put_contents(self::$file,$str);
        return $user;
    }

    public function findByNameAndPass($name, $pass) {
        foreach ($this->users as $user) {
            if ($user->getName() === $name && $user->getPass() === $pass) {
                return $user;
            }
        }
        return false;
    }

//put your code here
}
