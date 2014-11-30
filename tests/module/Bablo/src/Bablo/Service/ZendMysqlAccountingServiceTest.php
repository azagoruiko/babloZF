<?php

namespace Bablo\Service;

use Bablo\Mock\AccountingCacheMock;
use Bablo\Mock\TableGatewayMock;
use bablo\model\IncomeSearchFilter;

class ZendMysqlAccountingServiceTest  extends \PHPUnit_Framework_TestCase {
    /**
     *
     * @var ZendMysqlAccountingService
     */
    private $srv;
    
    /**
     *
     * @var AccountingCacheMock
     */
    private $cache;
    
    /**
     *
     * @var TableGatewayMock
     */
    private $gw;
    
    /**
    * 
    * @return \Bablo\Mock\SelectMock
    */
    private function getSelect() {
        return $this->gw->getSelect();
    }
    
    public function __construct() {
        $this->srv = new ZendMysqlAccountingService();
        $this->cache = new AccountingCacheMock();
        $this->gw = new TableGatewayMock();
        $this->srv->setCache($this->cache);
        $this->srv->setGw($this->gw);
    }
    
    public function setUp() {
    }
    
    public function tearDown() {
    }
    
    public function testFindAll() {
        echo "1st time test..\n";
        $filter = new IncomeSearchFilter();
        $data = $this->srv->findAll(1, $filter);
        
        $this->assertNotNull($data);
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertTrue(array_key_exists(0, $data));
        $this->assertEquals('data', $data[0]);
        
        $this->assertTrue($this->cache->getGetCalled());
        $this->assertTrue($this->cache->getPutCalled());
        $this->assertTrue($this->gw->getSelectCalled());
        
        echo "2nd time test..\n";
    
        $this->gw->reset();
        $this->cache->reset();
        $filter = new IncomeSearchFilter();
        $data = $this->srv->findAll(1, $filter);
        
        $this->assertNotNull($data);
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertTrue(array_key_exists(0, $data));
        $this->assertEquals('data', $data[0]);
        
        $this->assertTrue($this->cache->getGetCalled());
        $this->assertFalse($this->cache->getPutCalled());
        $this->assertFalse($this->gw->getSelectCalled());
    }
    
    public function testFindAllSelect() {
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom("8,14");
        $filter->setMonthTo("11,14");
        
        $data = $this->srv->findAll(1, $filter);
        
        $select = $this->getSelect();
        
        $predicates = $select->getWhere()->getPredicates();
        $this->assertEquals(1, count($predicates), 'WHERE has more than ONE predocate when setting only dates in the filter');
        
        $this->assertTrue($predicates[0][1] instanceof \Zend\Db\Sql\Predicate\Between);
        $this->assertEquals('AND', $predicates[0][0]);
        
        $this->assertEquals('2014-08-01', $predicates[0][1]->getMinValue());
        $this->assertTrue($predicates[0][1]->getMaxValue() instanceof \Zend\Db\Sql\Expression);
        
        $this->assertEquals('2014-11-01', $predicates[0][1]->getMaxValue()->getParameters());
        $this->assertEquals('LAST_DAY(?)', $predicates[0][1]->getMaxValue()->getExpression());
        
        //print_r($select->getWhere()->getPredicates());
    }
}
