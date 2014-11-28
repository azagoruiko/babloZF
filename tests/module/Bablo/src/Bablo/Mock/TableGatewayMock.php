<?php

namespace Bablo\Mock;

use Zend\Db\TableGateway\TableGatewayInterface;
class TableGatewayMock implements TableGatewayInterface {
    private $selectCalled = false;
    
    public function getSelectCalled() {
        return $this->selectCalled;
    }
    
    public function delete($where) {
        
    }

    public function getTable() {
        
    }

    public function insert($set) {
        
    }

    public function select($where = null) {
        $this->selectCalled = true;
        return ['data' => 'data'];
    }

    public function update($set, $where = null) {
        
    }

}
