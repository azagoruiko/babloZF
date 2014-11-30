<?php
namespace Bablo\Mock;

class SelectMock extends \Zend\Db\Sql\Select {
    /**
     * 
     * @return \Zend\Db\Sql\Where
     */
    public function getWhere() {
        return $this->where;
    }
}
