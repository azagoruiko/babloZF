<?php

namespace Bablo\Mock;

use Bablo\Service\AccountingCache;

class AccountingCacheMock implements AccountingCache {
    private $putCalled = false;
    private $getCalled = false;
    
    private $data = [];
    
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

    public function invalidate($iserId) {
        
    }

    public function put($key, $data) {
        $this->putCalled = true;
        $this->data[$key] = $data;
    }

}
