<?php

namespace Bablo\Service;

use bablo\model\User;
use bablo\service\UserService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result as Result;

class DoctrineUserService implements UserService, AdapterInterface {
    private $name;
    private $pass;
    private $em;
    
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

    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm() {
        return $this->em;
    }

    public function setEm($em) {
        $this->em = $em;
    }
    
    public function authenticate() {
        $user = $this->authorize($this->name, $this->pass);
        if ($user == null) {
            return new Result(Result::FAILURE, null);
        } else {
            return new Result(Result::SUCCESS, $user->getId());
        }
    }

    public function authorize($name, $pass) {
        $em = $this->getEm();
        $em->getConfiguration()->addCustomStringFunction('password', 'Bablo\Doctrine\PasswordFunc');
        $q = $em
             ->createQuery('select u from bablo\model\User u where u.email = :email and u.password = password(:pass)')
                ->setParameters([':email' => $name, ':pass' => $pass]);
        
        return $q->getSingleResult();
    }

    public function find($id) {
        return $this->getEm()
                ->getRepository('bablo\model\User')
                ->find($id);
    }

    public function resetPassword($email, $pass) {
        
    }

    public function save(User $user) {
        
    }

}
