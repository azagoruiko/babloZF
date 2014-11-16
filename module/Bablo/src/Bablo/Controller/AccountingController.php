<?php

namespace Bablo\Controller;

use Bablo\Form\IncomeFilterForm;
use Bablo\Form\IncomeForm;
use bablo\model\Income;
use bablo\model\IncomeSearchFilter;
use bablo\zf2\BaseAccountingController;
use Zend\View\Model\ViewModel;

class AccountingController extends BaseAccountingController
{

    public function incomeAction()
    {
        $view = new ViewModel();
        $view->form = new IncomeFilterForm();
        
        $theFilter = new IncomeSearchFilter();
        
        list($year, $month)=$this->getTodayYearMonth();
        $months = $this->getMonthArray($year, $month);
        
        $monthFrom = $view->form->get('month_from');
        $monthFrom->setValueOptions($months);
        
        $monthTo = $view->form->get('month_to');
        $monthTo->setValueOptions($months);
        $theFilter->setMonthFrom(implode(',', [$month++, $year]));
        $theFilter->setMonthTo(implode(',', [$month, $year]));
        
        $view->form->get('source')->setValueOptions(['1' => 'source 1', 2 => 'source 2', 3 => 'source 3']);
        
        $currencies = $this->getCurrencyService()->findAll();
        $_curr = [];
        foreach ($currencies as $c) {
            $_curr[$c['id']] = $c['name'];
        }

        $view->form->get('currency')->setValueOptions($_curr);
        
        $view->incomes=[];
        
        if ($this->getRequest()->isPost()) {
            $view->form->bind($theFilter);
            $view->form->setData($this->getRequest()->getPost()->toArray());
            if ($view->form->isValid()) {
                $view->incomes = $this->getIncomeService()->findAll($this->getAuthService()->getIdentity(), $theFilter);
            } 
        }
        
        $view->message = '';
        
        
        return $view;
    }

    public function editIncomeAction()
    {
        $view = new ViewModel();
        $income = new Income();
        
        $view->form = new IncomeForm();
        $currencies = $this->getCurrencyService()->findAll();
        $_curr = [];
        foreach ($currencies as $c) {
            $_curr[$c['id']] = $c['name'];
        }
        
        $id = $this->params()->fromRoute('id');
        
        if (!empty($id)) {
            $income = $this->getIncomeService()->find($id);
            $income->setDate(substr($income->getDate(), 0, 10));
            $view->form->bind($income);
        }
        
        $view->form->get('currency_id')->setValueOptions($_curr);
        $view->form->get('source_id')->setValueOptions(['1' => 'source 1', 2 => 'source 2', 3 => 'source 3']);
        
        if ($this->getRequest()->isPost()) {
            $view->form->setData($this->getRequest()->getPost());
            if ($view->form->isValid()) {
                $income->setAmount($this->params()->fromPost('amount'));
                $income->setCurrency_id($this->params()->fromPost('currency_id'));
                $income->setDate($this->params()->fromPost('date'));
                $income->setSource($this->params()->fromPost('source_id'));
                $income->setUserid($this->getAuthService()->getIdentity());
                $this->getIncomeService()->save($income);
            }
        }
        $view->currencies = $this->getCurrencyService()->findAll();
        
        
        return $view;
    }


}

