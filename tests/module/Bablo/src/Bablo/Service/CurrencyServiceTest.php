<?php
namespace Bablo\Service;

class CurrencyServiceTest extends \PHPUnit_Framework_TestCase {
    private $srv;
    
    public function __construct() {
        $this->srv = new CurrencyServiceImpl();
        $this->srv->setGw(new \Bablo\Mock\TableGatewayMock());
        $this->srv->setCache(new \Bablo\Mock\AccountingCacheMock());
    }
    
    function testGetRate() {
        $rate = $this->srv->getRate(1, '2014-11-01');
        
        $this->assertNotEmpty($rate, 'getRate() must return a number. Returned ' );
        $this->assertTrue(is_array($rate));
        $this->assertTrue($this->srv->getCache()->getGetCalled(), 'getRate() must check data in cache');
        $this->assertTrue($this->srv->getCache()->getPutCalled(), 'getRate() must put some data into cache');
        $this->assertTrue($this->srv->getGw()->getSelectCalled(), 'getRate() must select some data from the database');
    }
}
