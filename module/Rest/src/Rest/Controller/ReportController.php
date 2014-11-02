<?php

namespace Rest\Controller;

use bablo\dao\ExpenceDAO;
use bablo\service\IncomeService;
use bablo\zf2\BaseAccountingController;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;

class ReportController extends BaseAccountingController
{
    
    
    /**
     * 
     * @return ExpenceDAO Description
     */
    protected function getExpenceService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Rest\service\ExpenceService');
    }
    
    

    function monthlyIncomeAction() {
        $view = new JsonModel();
        $month = date("m");
        $year = date("Y");
        $view->updates = $this->getIncomeService()->getUpdates(
                $this->getAuthService()->getIdentity(), 
                0,
                $month, $year);
        return $view;
    }
    
    function revenue12MonthsAction() {
        $view = new JsonModel();
        $view->revenue = $this->getIncomeService()->getRevenueBrokenByMonth ($this->getAuthService()->getIdentity());
        
        return $view;
    }

    function annualBalanceAction() {
        $view = new JsonModel();
        $year = date("Y");
        $view->updates = $this->getIncomeService()->getAnnualBalance ($this->getAuthService()->getIdentity(), $year);
        
        return $view;
    }
    
    function monthlyExpenceAction() {
        $view = new JsonModel();
        $month = date("m");
        $year = date("Y");
        $view->updates = $this->getExpenceService()->findAll($this->getAuthService()->getIdentity(), $month, $year);
        return $view;
    }
    
    function incomeUpdatesAction() {
        $view = new JsonModel();
        $dates = $this->getSelectedYearMonth();
        $view->updates = $this->getIncomeService()->getUpdates(
                $this->getAuthService()->getIdentity(), 
                $this->params()->fromPost('since'),
                $dates[0], $dates[1]);
        $maxId = $this->params()->fromPost('since');
        foreach ($view->updates as $update) {
            if ($maxId < $update->getId()) {
                $maxId = $update->getId();
            }
        }
        $view->maxId = $maxId;
        return $view;
    }
}

