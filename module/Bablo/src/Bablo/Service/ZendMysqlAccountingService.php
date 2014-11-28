<?php

namespace Bablo\Service;

use bablo\model\Income;
use bablo\model\IncomeSearchFilter;
use bablo\service\IncomeService;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Expression as PredExpression;
use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\TableGatewayInterface;

class ZendMysqlAccountingService implements IncomeService {
    
    private $gw;
    private $cache;
    
    
    /**
     * 
     * @return AccountingCache
     */
    public function getCache() {
        return $this->cache;
    }

    public function setCache(AccountingCache $cache) {
        $this->cache = $cache;
    }

        
    
    /**
     * 
     * @return TableGateway
     */
    public function getGw() {
        return $this->gw;
    } 

    public function setGw(TableGatewayInterface $gw) {
        $this->gw = $gw;
    }

        
    public function delete($id) {
        return $this->getGw()->delete(['id' => $id]);
    }

    public function find($id) {
        $rowSet = $this->getGw()->select(['id' => $id]);
        return $rowSet->current();
    }

    private function prepareIncomeSelect(Select $select) {
        // join currency table
        $select->join(['c' => 'currency'], 'c.id = currency_id' , ['currency' => 'name']);
        // prepare subselect from `rate` table
        $joinSelect = new Select('rate');
        // select MAX(rate.date) from `rate`
        $joinSelect->columns([ new PredExpression('MAX(rate.date)') ]);

        // preparing predicate for ON statement
        $onExp = new Predicate();
        // income.currency_id = r.currency_id (not income.currency_id = 'r.currency_id')
        $onExp->equalTo('income.currency_id', new Expression('r.currency_id'));
        // income.currency_id = r.currency_id AND r.date = (select MAX(rate.date) from `rate`)
        $onExp->equalTo('r.date', $joinSelect);

        // join rate as r ON income.currency_id = r.currency_id AND r.date = (select MAX(rate.date) from `rate`)
        $select->join(['r' => 'rate'], $onExp, ['rate' => 'rate']);

        // column list for main select:
        // select *, (amount * rate) as usdAmount from (...the rest of our query...)
        $select->columns([
            '*',
            'usdAmount' => new Expression('amount * rate'),
        ]);
    }
    
    private function addIncomeFiltersToSelect(Select $select, IncomeSearchFilter $filter) {
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
            $lastDayExpr = new Expression('LAST_DAY(?)', $dateTo);
            // WHERE ... income.date between [dateFrom] AND LAST_DAY([dateTo]) ...
            $select->where->between('income.date', $dateFrom, $lastDayExpr);
        }

        if (!empty($filter->getMaxAmount())) {
            // WHERE ... AND amount <= [maxAmount]
            $select->where->lessThanOrEqualTo('amount', $filter->getMaxAmount());
        }

        if (!empty($filter->getMinAmount())) {
            // WHERE ... AND amount >= [minAmount]
            $select->where->greaterThanOrEqualTo('amount', $filter->getMinAmount());
        }

        if (!empty($filter->getCurrency())) {
            // new predicate set: AND(...)
            $pred = new PredicateSet([], PredicateSet::COMBINED_BY_OR);
            foreach ($filter->getCurrency() as $id) {
                $pr = new Predicate();
                // income.currency_id = [id]
                $pr->equalTo('income.currency_id', $id);
                // AND (... income.currency_id = [id] OR ...)
                $pred->addPredicate($pr);
            }
            // AND (income.currency_id = [id1] OR income.currency_id = [id1] OR ...)
            $select->where->addPredicate($pred);
        }

        /*if (!empty($filter->getSource())) {
            foreach ($filter->getSource() as $id) {
                $select->where->equalTo('income.source_id', $id);
            }
        }*/
    }
    
    public function findAll($userId, IncomeSearchFilter $filter) {
        $key = $userId . '-' . $filter;
        if (($_data = $this->getCache()->get($key)) === null) {
            $data = $this->getGw()->select( function (Select $select) use ($filter) {
                $this->prepareIncomeSelect($select);
                $this->addIncomeFiltersToSelect($select, $filter);

                // test your sql code!
                //$sql = $select->getSqlString();
            });

            foreach ($data as $item) {
                $_data[] = $item;
            }
            $this->getCache()->put($key, $_data);
        }
        return $_data;
    }

    public function getUpdates($userId = 0, $lastId = 0, IncomeSearchFilter $filter) {
        return $this->getGw()->select( function (Select $select) use ($filter, $lastId) {
            $this->prepareIncomeSelect($select);
            $this->addIncomeFiltersToSelect($select, $filter);
            $select->where->greaterThan('id', $lastId);
            // test your sql code!
            //$sql = $select->getSqlString();
        });
    }

    public function save(Income $income) {
        $this->getCache()->invalidate($income->getUserid());
        if (!empty($income->getId())) {
            return $this->getGw()->update($income->toArray(false, false), ['id' => $income->getId()]);
        } else {
            return $this->getGw()->insert($income->toArray(true, false));
        }
    }

    public function getAnnualBalance($userId = 0, $year = null) {
        
    }

    public function getRevenueBrokenByMonth($userId = 0, $month = '', $year = '') {
        
    }

}
