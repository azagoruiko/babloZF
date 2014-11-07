<?php


namespace bablo\service;

/**
 * Description of UserServiceImpl
 *
 * @author andrii
 */
class UserServiceImpl implements UserService {
    private $userDAO;
    
    function __construct(\bablo\dao\UserDAO $dao) {
        $this->userDAO = $dao;
    }

    public function find($id) {
        return $this->userDAO->find($id);
    }

    public function save(\bablo\model\User $user) {
        return $this->userDAO->save($user);
    }

    public function authorize($name, $pass) {
        return $this->userDAO->findByNameAndPass($name, $pass);
    }

    public function resetPassword($email, $pass) {
        return $this->userDAO->resetPassword($email, $pass);
    }

}
