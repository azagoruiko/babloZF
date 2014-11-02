<?php

namespace Bablo\Controller;

use Bablo\Form\LoginForm;
use Bablo\Service\AuthUserService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    //private $session;
    
    /*function __construct() {
        $this->session = new Container();
    }*/
    
    /**
     * 
     * @return AuthUserService Description
     */
    private function getUserService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Bablo\dao\UserService');
    }
    
    /**
     * 
     * @return AuthenticationService Description
     */
    private function getAuthService() {
        $sm = $this->getServiceLocator();
        return $sm->get('AuthService');
    }

    public function indexAction()
    {
        $view = new ViewModel();
        $view->login = new LoginForm();
        if ($this->getAuthService()->hasIdentity()) {       
            return $this->redirect()->toRoute('bablo/default', ['action' => 'dashboard', 'controller' => 'index']);
        } else {
            $view->user = null;
        }  
        return $view;
    }
    
    public function dashboardAction() {
        if (!$this->getAuthService()->hasIdentity()) {
            return $this->redirect()->toUrl('/');
        } else {
            $view = new ViewModel();
            $id = $this->getAuthService()->getIdentity();
            $view->user = $this->getUserService()->find($id);
            return $view;
        }
    }
    
    function loginAction() {
        $view = new ViewModel();
        $name = $this->params()->fromPost('name');
        $pass = $this->params()->fromPost('pass');
        $service = $this->getAuthService();
        $service->getAdapter()->setName($name);
        $service->getAdapter()->setPass($pass);
        if (empty($name) && empty($pass)) {
            return $view;
        }
        $result = $service->authenticate();
        
        if ($result->getCode() === Result::SUCCESS) {
            $this->getAuthService()->getStorage()->rememberMe();
            return $this->redirect()->toUrl('/');
        } else {
            $view->error = "Login failed! You're a hacker!";
            return $view;
        }
    }
    
    function logoutAction() {
        $this->getAuthService()->getStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
        return $this->redirect()->toUrl('/');
    }

}

