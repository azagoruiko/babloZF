<?php
namespace BabloTest\Controller;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
class AdapterInterfaceMock implements AdapterInterface {
    public function authenticate() {
        $r = new Result(Result::SUCCESS, 1);
        print_r($r);
        return $r;
    }

}

