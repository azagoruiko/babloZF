<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bablo\zf2;

use bablo\service\IncomeService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * Description of BaseAccountingController
 *
 * @author andrii
 */
class BaseAccountingController extends AbstractActionController {
    /*protected $session;
    
    function __construct() {
        $this->session = new Container();
    }*/
    
    /**
     * 
     * @return \Zend\Authentication\AuthenticationService
     */
    protected function getAuthService() {
        $sm = $this->getServiceLocator();
        return $sm->get('AuthService');
    }
    
    /**
     * 
     * @return \bablo\dao\CurrencyDAO Description
     */
    protected function getCurrencyService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Bablo\service\CurrencyService');
    }
    
    /**
     * 
     * @return IncomeService Description
     */
    protected function getIncomeService() {
        $sm = $this->getServiceLocator();
        return $sm->get('Rest\service\IncomeService');
    }

    protected function getSelectedMonth($year, $month) {
        $selectedMonth = $this->params()->fromPost('months');
        if (empty($selectedMonth)) {
            $selectedMonth = implode(',', [$month, $year]);
        }
        return $selectedMonth;
    }
    
    protected function getMonthArray($year, $month) {
        $months = [];
        for ($i=0; $i<=12; $i++){
            $months["$month,$year"]=date("M", mktime(0, 0, 0, $month, 1, $year))." $year";
            $month--;
            if ($month==0) {
                $month=12;
                $year--;
            }
        }
        return $months;
    }
    
    protected function getSelectedYearMonth() {
        $month = $this->params()->fromPost('months');
        if (empty($month)) {
            return [null, null];
        } else {
            return explode(',', $this->params()->fromPost('months'));
        }
    }
    
    protected function getTodayYearMonth() {
        $today = date ('Y,m');
        return explode(',', $today);
    }
    
    protected function prepareMoneyReportForm(&$view) {
        list($year, $month)=$this->getTodayYearMonth();
        $view->selectedMonth = $this->getSelectedMonth($year, $month);
        $view->months = $this->getMonthArray($year, $month);
    }
}
