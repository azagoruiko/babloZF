<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bablo\Service;

use bablo\model\User;
use bablo\service\UserService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Db\TableGateway\TableGateway;

/**
 * Description of ZendMysqlUserService
 *
 * @author andrii
 */
class ZendMysqlUserService implements UserService, AdapterInterface {
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
    /**
     *
     * @var TableGateway
     */
    
    private $gw;
    
    public function getGw() {
        return $this->gw;
    }

    public function setGw(TableGateway $gw) {
        $this->gw = $gw;
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
        $rowset = $this->gw->select(['email' => $name, 'password' => $pass]);
        return $rowset->current();
    }

    public function find($id) {
        $rowset = $this->gw->select(['id' => $id]);
        return $rowset->current();
    }

    public function resetPassword($email, $pass) {
        return $this->gw->update(['password' => $pass], ['email' => $email]);
    }

    public function save(User $user) {
        return empty($user->getId())
            ? $this->gw->insert(['name' => $user->getName(), 'password' => $user->getPass(), 'email' => $user->getEmail()])
            : $this->gw->update(['name' => $user->getName(), 'password' => $user->getPass(), 'email' => $user->getEmail()], ['id' => $user->getId()]);
    }

}
