<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bablo\Form;

use Zend\Form\Form;

/**
 * Description of LoginForm
 *
 * @author andrii
 */
class LoginForm extends Form{
    function __construct($name = null) {
        parent::__construct('login');
        $this->add(array(
             'name' => 'name',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Email',
             ),
         ));
        $this->add(array(
             'name' => 'pass',
             'type' => 'Password',
             'options' => array(
                 'label' => 'Password',
             ),
         ));
        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Login',
                 'id' => 'submitbutton',
             ),
         ));
    }

}
