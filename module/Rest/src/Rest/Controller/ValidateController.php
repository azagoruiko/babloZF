<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rest\Controller;

use Bablo\Form\IncomeForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

/**
 * Description of ValidateController
 *
 * @author andrii
 */
class ValidateController extends AbstractActionController {
    public function incomeAction() {
        $view = new JsonModel();
        $form = new IncomeForm();
        $form->setData($this->getRequest()->getPost());
        $form->isValid();
        $view->result = $form->getMessages();
        return $view;
    }
}
