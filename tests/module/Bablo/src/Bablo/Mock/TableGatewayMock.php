<?php

namespace Bablo\Mock;

use Zend\Db\TableGateway\TableGatewayInterface;
class TableGatewayMock implements TableGatewayInterface {
    
    private $selectCalled = false;
    private $select = null;
    
    public function reset() {
        $this->selectCalled = false;
    }
    
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
        echo "selecting...\n";
        if (is_callable($where)) {
            $this->select = new SelectMock();
            $where($this->select);
        }
        $this->selectCalled = true;
        return ['data' => 'data'];
    }

    public function update($set, $where = null) {
        
    }
    
    /**
     * 
     * @return \Zend\Db\Sql\Select
     */
    public function getSelect() {
        return $this->select;
    }


}
