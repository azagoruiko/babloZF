<?php

namespace Bablo\Mock;

use Bablo\Service\AccountingCache;

class AccountingCacheMock implements AccountingCache {
    public function __construct() {
        echo "constructing cache\n";
    }
    
    private $putCalled = false;
    private $getCalled = false;
    
    private $data = [];
    
    public function reset() {
        $this->getCalled = false;
        $this->putCalled = false;
    }
    
    public function getPutCalled() {
        return $this->putCalled;
    }

    public function getGetCalled() {
        return $this->getCalled;
    }

        
    public function get($key) {
        $this->getCalled = true;
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    public function invalidate($userId) {
        
    }

    public function put($key, $data) {
        $this->putCalled = true;
        $this->data[$key] = $data;
    }

}
