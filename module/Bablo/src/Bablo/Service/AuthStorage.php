<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bablo\Service;

use Zend\Authentication\Storage\Session;

/**
 * Description of AuthStorage
 *
 * @author andrii
 */
class AuthStorage extends Session {
    
    function rememberMe() {
        $this->session->getManager()->rememberMe(1000 * 60 * 60);
    }
    
    function forgetMe() {
        $this->session->getManager()->forgetMe();
    }
}
