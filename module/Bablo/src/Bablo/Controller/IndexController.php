<?php

namespace Bablo\Controller;

use bablo\dao\MysqlUserDAO;
use Bablo\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $session;
    
    function __construct() {
        $this->session = new Container();
    }
    
    /**
     * 
     * @return MysqlUserDAO Description
     */
    private function getUserService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Bablo\dao\UserService');
    }

    public function indexAction()
    {
        $view = new ViewModel();
        $view->login = new LoginForm();
        if (isset($this->session['id'])) {
            //return $this->redirect()->toRoute('dashboard');
            return $this->redirect()->toRoute('bablo/default', ['action' => 'dashboard', 'controller' => 'index']);
        } else {
            $view->user = null;
        }  
        return $view;
    }
    
    public function dashboardAction() {
        $view = new ViewModel();
        $view->user = $this->getUserService()->find($this->session['id']);
        return $view;
    }
    
    function loginAction() {
        $view = new ViewModel();
        $name = $this->params()->fromPost('name');
        $pass = $this->params()->fromPost('pass');
        if (empty($name) && empty($pass)) {
            return $view;
        }
        else if (FALSE !== ($view->user = $this->getUserService()->authorize($name, $pass))) {
            $this->session['id'] = $view->user->getId();
            return $this->redirect()->toUrl('/');
        } else {
            $view->error = "Login failed! You're a hacker!";
            return $view;
        }
    }
    
    function logoutAction() {
        $this->session['id'] = NULL;
        return $this->redirect()->toUrl('/');
    }

}

