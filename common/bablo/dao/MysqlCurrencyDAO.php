<?php

namespace bablo\dao;

use bablo\util\MySQL;

/**
 * Description of MysqlCurrencyDAO
 *
 * @author andrii
 */
class MysqlCurrencyDAO implements CurrencyDAO {
    /**
     *
     * @var \PDO
     */
    private $mysql;
    
    public function __construct(\PDO $pdo) {
        $this->mysql = $pdo;
    }
    public function findAll() {
        $stmt = $this->mysql->prepare('SELECT * from currency');
        $stmt->execute();
        $currencies = [];
        while (FALSE !== ($obj = $stmt->fetch())) {
            $currencies[] = $obj;
        }
        return $currencies;
    }
}
