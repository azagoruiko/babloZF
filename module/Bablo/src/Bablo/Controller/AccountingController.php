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
        foreach ($currencies as $c_) {
            $c = $c_->toArray();
            $_curr[$c['id']] = $c['name'];
        }

        $view->form->get('currency')->setValueOptions($_curr);
        
        $view->incomes=[];
        
        if ($this->getRequest()->isPost()) {
            $page = $this->params()->fromPost('page');
            $view->form->bind($theFilter);
            $view->form->setData($this->getRequest()->getPost()->toArray());
            if ($view->form->isValid()) {
                $view->form->setData($this->getRequest()->getPost()->toArray());
                $view->incomes = $this->getIncomeService()->findAll($this->getAuthService()->getIdentity(), $theFilter, $page);
            } 
        }
        
        $view->message = '';
        $view->theFilter = $theFilter->jsonSerialize();
        
        
        return $view;
    }

    public function editIncomeAction()
    {
        $view = new ViewModel();
        $income = new Income();
        
        $view->form = new IncomeForm($this->getIncomeService()->getEm());
        
        $id = $this->params()->fromRoute('id');
        
        if (!empty($id)) {
            $income = $this->getIncomeService()->find($id);
            $income->setDate(substr($income->getDate(), 0, 10));
            $view->form->bind($income);
        }
        $view->form->get('source_id')->setValueOptions(['1' => 'source 1', 2 => 'source 2', 3 => 'source 3']);
        
        if ($this->getRequest()->isPost()) {
            $view->form->bind($income);
            $view->form->setData($this->getRequest()->getPost());
            if ($view->form->isValid()) {
                $income->setUserid($this->getAuthService()->getIdentity());
                $user = $this->getUserService()->find($income->getUserId());
                $income->setUser($user);
                $currency = $this->getCurrencyService()->find($income->getCurrency());
                $income->setCurrency($currency);
                $income->setDate(new \DateTime($income->getDate()));
                $this->getIncomeService()->save($income);
            }
        }
        $view->currencies = $this->getCurrencyService()->findAll();
        
        
        return $view;
    }


}

