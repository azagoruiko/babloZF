<?php

namespace Bablo\Controller;

use Bablo\Form\LoginForm;
use Bablo\Service\AuthUserService;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
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
    public function getAuthService() {
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
        $view = new ViewModel();
        $id = $this->getAuthService()->getIdentity();
        $view->user = $this->getUserService()->find($id);
        return $view;
    }
    
    function loginAction() {
        $view = new ViewModel();
        $name = $this->params()->fromPost('name');
        $pass = $this->params()->fromPost('pass');
        $service = $this->getAuthService();
        $service->getAdapter()->setName($name);
        $service->getAdapter()->setPass($pass);
        if (empty($name) && empty($pass)) {
            return $this->redirect()->toUrl('/');
        }
        $result = $service->authenticate();
        
        if ($result->getCode() === Result::SUCCESS) {
            $this->getAuthService()->getStorage()->rememberMe();
            return $this->redirect()->toUrl('/');
        } else {
            $view->error = "Login failed! You're a hacker!";
            return $this->redirect()->toUrl('/');
        }
    }
    
    function logoutAction() {
        $this->getAuthService()->getStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();
        return $this->redirect()->toUrl('/');
    }
    
    function userInfoAction() {
        $id = $this->params()->fromRoute('param1');
        $user = $this->getUserService()->find($id);
        if ($user == null) {
            echo "User not found\n";
        } else {
            echo $user->getEmail() . "\n";
        }
    }
    
    function resetPasswordAction() {
        $email = $this->params()->fromRoute('param1');
        $pass =  $this->params()->fromRoute('param2', Rand::getString(16));
        $result = $this->getUserService()->resetPassword($email, $pass);
        if ($result > 0) {
            echo "Password set to $pass\n";
        } else {
            echo "User not found\n";
        }
    }

}

