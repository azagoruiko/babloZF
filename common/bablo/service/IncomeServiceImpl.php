<?php

namespace bablo\service;

use bablo\dao\IncomeDAO;
use bablo\model\Income;

/**
 * Description of IncomeServiceImpl
 *
 * @author andrii
 */
class IncomeServiceImpl implements IncomeService {
    private $dao;
    function __construct(IncomeDAO $dao) {
        $this->dao = $dao;
    }

    
    public function find($id) {
        return $this->dao->find($id);
    }

    public function findAll($userId, $month=null, $year=null) {
        return $this->dao->findAll($userId, $month, $year);
    }
    
    public function getCombinedReport ($userId, $month=null, $year=null) {
            return $this->dao->getCombinedReport($userId, $month, $year);
    }
    
    public function getSumary ($userId, $month=null, $year=null) {
            return $this->dao->getSumary($userId, $month, $year);
    }
        
    public function save(Income $income) {
        return $this->dao->save($income);
    }

    public function getUpdates($userId = 0, $lastId=0, $month=0, $year=0) {
        return $this->dao->getUpdates($userId, $lastId, $month, $year);
    }
    
    function delete($id) {
        $this->dao->delete($id);
    }
    
    function getAnnualBalance ($userId=0, $year=null) {
        return $this->dao->getAnnualBalance($userId, $year);
    }

    public function getRevenueBrokenByMonth($userId = 0, $month = '', $year = '') {
        return $this->dao->getRevenueBrokenByMonth($userId, $month, $year);
    }

//put your code here
}
