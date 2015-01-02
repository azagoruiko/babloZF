<?php

namespace Rest\Controller;

use bablo\dao\ExpenceDAO;
use Bablo\Form\IncomeFilterForm;
use bablo\model\IncomeSearchFilter;
use bablo\zf2\BaseAccountingController;
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
    
    /**
     * 
     * @return \Bablo\Service\CurrencyService
     */
    protected function getRateService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Bablo\service\RateService');
    }

    function monthlyIncomeAction() {
        $view = new JsonModel();
        $month = date("m");
        $year = date("Y");
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom("$month,$year");
        $filter->setMonthTo("$month,$year");
        $view->updates = $this->getIncomeService()->getUpdates(
                $this->getAuthService()->getIdentity(), 
                0,
                $filter);
        foreach($view->updates as $u) {
            $u->usdAmount = (int)$u->getUsdAmount();
        }
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
        $form = new IncomeFilterForm();
        $theFilter = new IncomeSearchFilter();
        
        list($year, $month)=$this->getTodayYearMonth();
        $months = $this->getMonthArray($year, $month);
        
        $monthFrom = $form->get('month_from');
        $monthFrom->setValueOptions($months);
        
        $monthTo = $form->get('month_to');
        $monthTo->setValueOptions($months);
        
        $form->get('source')->setValueOptions(['1' => 'source 1', 2 => 'source 2', 3 => 'source 3']);
        
        $currencies = $this->getCurrencyService()->findAll();
        $_curr = [];
        foreach ($currencies as $c) {
            $_curr[$c['id']] = $c['name'];
        }

        $form->get('currency')->setValueOptions($_curr);
        
        $view->updates = [];
        if ($this->getRequest()->isPost()) {
            $form->bind($theFilter);
            $form->setData($this->getRequest()->getPost()->toArray());
            if ($form->isValid()) {
                $view->updates = $this->getIncomeService()->getUpdates(
                $this->getAuthService()->getIdentity(), 
                $this->params()->fromPost('since'),
                $theFilter);
                $maxId = $this->params()->fromPost('since');
                foreach ($view->updates as $update) {
                    if ($maxId < $update->getId()) {
                        $maxId = $update->getId();
                    }
                }
                $view->maxId = $maxId;
            } 
        }
        return $view;
    }
    
    function deleteAction() {
        $res = $this->getIncomeService()->delete($this->params()->fromPost('id'));
        return new JsonModel(['result' => $res]);
    }
    
    function rateAction() {
        $rate = $this->getRateService()->getRate($this->params()->fromQuery('currency'), $this->params()->fromQuery('date'));
        return new JsonModel($rate);
    }
}

