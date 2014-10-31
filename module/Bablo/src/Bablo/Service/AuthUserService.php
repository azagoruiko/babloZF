<?php
namespace Bablo\Service;

use bablo\service\UserServiceImpl;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Session\Storage\StorageInterface;

class AuthUserService extends UserServiceImpl implements AdapterInterface {
    private $name;
    private $pass;
        
    public function getName() {
        return $this->name;
    }

    public function getPass() {
        return $this->pass;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setPass($pass) {
        $this->pass = $pass;
    }
    
    public function authenticate() {
        $user = $this->authorize($this->name, $this->pass);
        if ($user == null) {
            return new Result(Result::FAILURE, null);
        } else {
            return new Result(Result::SUCCESS, $user->getId());
        }
    }
}
