<?php

namespace bablo\dao;

use bablo\model\Income;
use PDO;

class MysqlIncomeDAO implements IncomeDAO {
    /**
     *
     * @var \PDO
     */
    private $mysql;
    
    public function __construct(\PDO $pdo) {
        $this->mysql = $pdo;
    }
    
    public function find($id) {
        $stmt = $this->mysql->prepare("SELECT * from income where id=:id");
        $stmt->bindParam('id', $id);
        $stmt->execute();
        while ($income = $stmt->fetchObject('\bablo\model\Income')) {
            return $income;
        }
        return null;
    }

    public function findAll($userId=0, $month=null, $year=null) {
        if (empty($month) || empty($year)) {
            list($month, $year) = explode(',', date('m,Y'));
        }
        $dateFrom = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
        $stmt = $this->mysql->prepare("SELECT i.*, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id "
                . "and i.date between :date_from and LAST_DAY(:date_from) "
                . "order by i.date");
        $stmt->bindParam('user_id', $userId);
        $stmt->bindParam('date_from', $dateFrom);
        $stmt->execute();
        $incomes = [];
        while ($income = $stmt->fetchObject('\bablo\model\Income')) {
            $incomes[] = $income;
        }
        return $incomes;
    }
    
    public function getUpdates($userId=0, $lastId=0, $month=0, $year=0) {
        $dateFrom = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
        $stmt = $this->mysql->prepare("SELECT i.*, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id and i.id > :last_id "
                . "and i.date between :date_from and LAST_DAY(:date_from) "
                . "order by i.date");
        $stmt->bindParam('user_id', $userId);
        $stmt->bindParam('last_id', intval($lastId));
        $stmt->bindParam('date_from', $dateFrom);
        $stmt->execute();
        $incomes = [];
        while ($income = $stmt->fetchObject('\bablo\model\Income')) {
            $incomes[] = $income;
        }
        return $incomes;
    }

    public function getCombinedReport ($userId=0, $month=null, $year=null){
        if (empty($month) || empty($year)) {
            list($month, $year) = explode(',', date('m,Y'));
        }
        $dateFrom = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
        $dateTo = date('Y-m-d', mktime(0,0,0,++$month, 1, $year));
        $stmt = $this->mysql->prepare(
                "select type, balance, user_id, date, currency, usdAmount from ("
                . "(SELECT 1 as type, e.amount*-1 as balance, e.user_id, e.date, c.name as currency, (e.amount*rate*-1) as usdAmount "
                . "from expence e "
                . "join currency c "
                ."on e.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where e.user_id=:user_id "
                . "and e.date between :date_from and LAST_DAY(:date_from)) "

                ."UNION "

                . "(SELECT 0 as type, i.amount, i.user_id, i.date, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id "
                . "and i.date between :date_from and LAST_DAY(:date_from)) "
                . ") as balanceTable "
                . "order by date "
                );
        $stmt->bindParam('user_id', $userId);
        $stmt->bindParam('date_from', $dateFrom);
        $stmt->bindParam('date_to', $dateTo);
        $stmt->execute();
        $balance = [];
        while ($balanc = $stmt->fetch (PDO::FETCH_ASSOC)) {
            $balance[] = $balanc;
        }
        return $balance;
    }
    
    public function getSumary ($userId=0, $month=null, $year=null){
        if (empty($month) || empty($year)) {
            list($month, $year) = explode(',', date('m,Y'));
        }
        $dateFrom = date('Y-m-d', mktime(0,0,0,$month, 1, $year));
        $dateTo = date('Y-m-d', mktime(0,0,0,++$month, 1, $year));
        $stmt = $this->mysql->prepare(
                "select `month`, `year`, SUM(expence) as expence, SUM(income) as income, (SUM(income)+SUM(expence)) as balance, user_id, date, currency, SUM(usdAmount) as usdAmount from ("
                . "(SELECT 1 as type, MONTH(e.date) as `month`, YEAR(e.date) as `year`, e.amount*-1 as expence, 0 as income, e.user_id, e.date, c.name as currency, (e.amount*rate*-1) as usdAmount "
                . "from expence e "
                . "join currency c "
                ."on e.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where e.user_id=:user_id)"
                
                ."UNION "

                . "(SELECT 0 as type, MONTH(i.date) as `month`, YEAR(i.date) as `year`, 0 as expence, i.amount as income, i.user_id, i.date, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id)"
                . ") as balanceTable "
                . "group by month, year"
                );
        $stmt->bindParam('user_id', $userId);
        $stmt->execute();
        $sumary = [];
        while ($sum = $stmt->fetch (PDO::FETCH_ASSOC)) {
            $sumary[] = $sum;
        }
        return $sumary;
    }
    
    public function getAnnualBalance ($userId=0, $year=null){
        if (empty($month) || empty($year)) {
            list($month, $year) = explode(',', date('m,Y'));
        }
        $dateFrom = date('Y-m-d', mktime(0,0,0,1, 1, $year));
        $dateTo = date('Y-m-d', mktime(0,0,0,12, 31, $year));
        $stmt = $this->mysql->prepare(
                "select `year`, SUM(expence) as expence, SUM(income) as income, (SUM(income)+SUM(expence)) as balance, user_id, date, currency, SUM(usdAmount) as usdAmount from ("
                . "(SELECT 1 as type, YEAR(e.date) as `year`, e.amount*-1 as expence, 0 as income, e.user_id, e.date, c.name as currency, (e.amount*rate*-1) as usdAmount "
                . "from expence e "
                . "join currency c "
                ."on e.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where e.user_id=:user_id)"
                
                ."UNION "

                . "(SELECT 0 as type, YEAR(i.date) as `year`, 0 as expence, i.amount as income, i.user_id, i.date, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id)"
                . ") as balanceTable "
                . "group by year"
                );
        $stmt->bindParam('user_id', $userId);
        $stmt->execute();
        $sumary = [];
        while ($sum = $stmt->fetch (PDO::FETCH_ASSOC)) {
            $sumary[] = $sum;
        }
        return $sumary;
    }
    
    public function getRevenueBrokenByMonth ($userId=0, $month='', $year=''){
        if (empty($month) || empty($year)) {
            list($month, $year) = explode(',', date('m,Y'));
            $year--;
        }
        $dateFrom = date('Y-m-d', mktime(0,0,0,1, $month, $year));
        $stmt = $this->mysql->prepare(
                "select `month`, `year`, SUM(expence) as expence, SUM(income) as income, (SUM(income)+SUM(expence)) as balance, user_id, date, currency, SUM(usdAmount) as usdAmount from ("
                . "(SELECT 1 as type, MONTH(e.date) as `month`, YEAR(e.date) as `year`, e.amount*-1 as expence, 0 as income, e.user_id, e.date, c.name as currency, (e.amount*rate*-1) as usdAmount "
                . "from expence e "
                . "join currency c "
                ."on e.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where e.user_id=:user_id "
                . "and e.date > :date) "
                
                ."UNION "

                . "(SELECT 0 as type, MONTH(i.date) as `month`, YEAR(i.date) as `year`, 0 as expence, i.amount as income, i.user_id, i.date, c.name as currency, (i.amount*rate) as usdAmount "
                . "from income i "
                . "join currency c "
                . "on i.currency_id=c.id "
                . "join rate r "
                . "on r.id=c.id and r.date=(select MAX(rate.date) as d from rate) "
                . "where i.user_id=:user_id "
                . "and i.date > :date) "
                . ") as balanceTable "
                . "group by `month`, `year` order by year DESC, month DESC limit 12"
                );
        $stmt->bindParam('user_id', $userId);
        $stmt->bindParam('date', $dateFrom);
        $stmt->execute();
        $sumary = [];
        while ($sum = $stmt->fetch (PDO::FETCH_ASSOC)) {
            $sumary[] = $sum;
        }
        return $sumary;
    }
    
    public function save(Income $income) {
        $stmt = $this->mysql->prepare("INSERT INTO income "
                . "(date, amount, currency_id, user_id) "
                . "values "
                . "(:date, :amount, :currency_id, :user_id)");
        $stmt->bindParam('user_id', $income->getUserid());
        $stmt->bindParam('date', $income->getDate());
        $stmt->bindParam('amount', $income->getAmount());
        $stmt->bindParam('currency_id', $income->getCurrency_id());
        return $stmt->execute();
    }
    
    function delete($id) {
        $stmt = $this->mysql->prepare('delete from income where id=:id');
        $stmt->bindParam('id', $id);
        return $stmt->execute();
    }

}
