<?php

namespace Bablo\Service;

use bablo\model\Income;
use bablo\model\IncomeSearchFilter;
use bablo\service\IncomeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Zend\Paginator\Paginator;

class DoctrineIncomeService implements IncomeService {
    private $em;
    
    
    /**
     * 
     * @return EntityManager
     */
    public function getEm() {
        return $this->em;
    }

    public function setEm($em) {
        $this->em = $em;
    }

        
    public function delete($id) {
        
    }

    public function find($id) {
        
    }

    private function prepareIncomeSelect(QueryBuilder $qb) {
        $qb->select('i')
                ->from('bablo\model\Income', 'i')
                ->join('i.currency', 'c');
        
        $subQ = $this->getEm()->createQueryBuilder();
        $subQ->select('MAX(r.date)')->from('bablo\model\Rate', 'r');
        
        $qb->join('c.rates', 'ra', 'WITH', 'ra.date =(' . $subQ->getDQL() .')');
        
    }
    
    private function addIncomeFiltersToSelect(QueryBuilder $qb, IncomeSearchFilter $filter) {
        // building WHERE clause
        if (!empty($filter->getMonthFrom())) {
            if (empty($filter->getMonthTo())) {
                $dateTo = date();
            } else {
                list($month, $year) = explode(',', $filter->getMonthTo());
                $dateTo = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
            }
            list($month, $year) = explode(',', $filter->getMonthFrom());
            $dateFrom = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
            // WHERE ... income.date between [dateFrom] AND LAST_DAY([dateTo]) ...
            $this->getEm()->getConfiguration()->addCustomStringFunction('LAST_DAY', 'Bablo\Doctrine\LastDayFunc');
            $qb->where($qb->expr()->between('i.date', ':dateFrom', 'LAST_DAY(:dateTo)'));
            $qb->setParameter('dateFrom', $dateFrom);
            $qb->setParameter('dateTo', $dateTo);
        }

        if (!empty($filter->getMaxAmount())) {
            // WHERE ... AND amount <= [maxAmount]
            $qb->andWhere($qb->expr()->lte('i.amount', ':maxAmount'));
            $qb->setParameter('maxAmount', $filter->getMaxAmount());
        }

        if (!empty($filter->getMinAmount())) {
            // WHERE ... AND amount >= [minAmount]
            $qb->andWhere($qb->expr()->gte('i.amount', ':minAmount'));
            $qb->setParameter('minAmount', $filter->getMinAmount());
        }

        if (!empty($filter->getCurrency())) {
            // currency.id in (1,2,3...)
            $qb->andWhere($qb->expr()->in('c.id', $filter->getCurrency()));
        }
    }
    
    public function findAll($userId, IncomeSearchFilter $filter, $page=1, $count=10) {
        $em = $this->getEm();
        $qb = $em->createQueryBuilder();
        $this->prepareIncomeSelect($qb);
        $qb->andWhere($qb->expr()->eq("i.user.id", $userId));
        $this->addIncomeFiltersToSelect($qb, $filter);
        
        $adapter = new DoctrinePaginator(
                new DPaginator($qb));
        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage($count);
        $paginator->setCurrentPageNumber($page);
        return $paginator;
    }

    public function getAnnualBalance($userId = 0, $year = null) {
        $em = $this->getEm();
        $qb = $em->createQueryBuilder();
        $qb->select('SUM(i.amount * ra.rate) as usdAmount')
                ->from('bablo\model\Income', 'i')
                ->join('i.currency', 'c');
        
        $subQ = $this->getEm()->createQueryBuilder();
        $subQ->select('MAX(r.date)')->from('bablo\model\Rate', 'r');
        
        $qb->join('c.rates', 'ra', 'WITH', 'ra.date =(' . $subQ->getDQL() .')');
        $qb->andWhere($qb->expr()->eq("i.user.id", $userId));
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom("1,$year");
        $filter->setMonthTo("12,$year");
        
        $this->addIncomeFiltersToSelect($qb, $filter);
        return ['usdAmount' => $qb->getQuery()->getSingleScalarResult()];
    }

    public function getRevenueBrokenByMonth($userId = 0, $month = '', $year = '') {
        if (empty($year)) {
            $year = date('Y');
        }
        $em = $this->getEm();
        $qb = $em->createQueryBuilder();
        $this->getEm()->getConfiguration()->addCustomStringFunction('MONTH', 'Bablo\Doctrine\MonthFunc');
        $this->getEm()->getConfiguration()->addCustomStringFunction('YEAR', 'Bablo\Doctrine\YearFunc');
        $qb->select('SUM(i.amount * ra.rate) as balance, MONTH(i.date) as month, YEAR(i.date) as year')
                ->from('bablo\model\Income', 'i')
                ->join('i.currency', 'c');
        
        $subQ = $this->getEm()->createQueryBuilder();
        $subQ->select('MAX(r.date)')->from('bablo\model\Rate', 'r');
        
        $qb->join('c.rates', 'ra', 'WITH', 'ra.date =(' . $subQ->getDQL() .')');
        $qb->andWhere($qb->expr()->eq("i.user.id", $userId));
        $qb->groupBy('month');
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom("1,$year");
        $filter->setMonthTo("12,$year");
        
        $this->addIncomeFiltersToSelect($qb, $filter);
        return $qb->getQuery()->getScalarResult();
    }

    public function getUpdates($userId = 0, $lastId = 0, IncomeSearchFilter $filter) {  
        $em = $this->getEm();
        $qb = $em->createQueryBuilder();
        $this->prepareIncomeSelect($qb);
        $qb->andWhere($qb->expr()->eq("i.user.id", $userId));
        $qb->andWhere($qb->expr()->gt("i.id", $lastId));
        $this->addIncomeFiltersToSelect($qb, $filter);
        return $qb->getQuery()->getResult();
    }

    public function save(Income $income) {
        $this->getEm()->persist($income);
        $this->getEm()->flush();
    }

}
