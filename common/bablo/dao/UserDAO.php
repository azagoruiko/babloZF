<?php
namespace bablo\dao;

use bablo\model\User;

/**
 *
 * @author andrii
 */
interface UserDAO extends DAO {
    //function find($id);
    function save(User $user);
    function findByNameAndPass($name, $pass);
    function resetPassword($email, $pass);
}
