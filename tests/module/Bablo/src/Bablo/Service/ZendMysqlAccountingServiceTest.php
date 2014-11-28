<?php

namespace Bablo\Service;

use Bablo\Mock\AccountingCacheMock;
use Bablo\Mock\TableGatewayMock;
use bablo\model\IncomeSearchFilter;

class ZendMysqlAccountingServiceTest  extends \PHPUnit_Framework_TestCase {
    public function testFindAll() {
        $srv = new ZendMysqlAccountingService();
        $cache = new AccountingCacheMock();
        $gw = new TableGatewayMock();
        $srv->setCache($cache);
        $srv->setGw($gw);
        
        $filter = new IncomeSearchFilter();
        $data = $srv->findAll(1, $filter);
        
        $this->assertNotNull($data);
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertTrue(array_key_exists(0, $data));
        $this->assertEquals('data', $data[0]);
        
        $this->assertTrue($cache->getGetCalled());
        $this->assertTrue($cache->getPutCalled());
        $this->assertTrue($gw->getSelectCalled());
        
        $gw = new TableGatewayMock();
        $srv->setGw($gw);
        $data = $srv->findAll(1, $filter);
        
        $this->assertNotNull($data);
        $this->assertTrue(is_array($data));
        $this->assertNotEmpty($data);
        $this->assertTrue(array_key_exists(0, $data));
        $this->assertEquals('data', $data[0]);
        
        $this->assertTrue($cache->getGetCalled());
        //$this->assertFalse($cache->getPutCalled());
        $this->assertFalse($gw->getSelectCalled());
    }
}
