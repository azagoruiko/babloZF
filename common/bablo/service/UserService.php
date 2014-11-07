<?php
namespace bablo\service;

use bablo\model\User;

/**
 *
 * @author andrii
 */
interface UserService {
    function find($id);
    function save(User $user);
    function authorize($name, $pass);
    function resetPassword($email, $pass);
}
