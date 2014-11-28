<?php
namespace Bablo\Service;

class AccountingCacheImpl implements AccountingCache {
    /**
     *
     * @var \Zend\Cache\Storage\Adapter\Filesystem
     */
    private $cacheAdapter;
    
    public function getCacheAdapter() {
        return $this->cacheAdapter;
    }

    public function setCacheAdapter($cacheAdapter) {
        $this->cacheAdapter = $cacheAdapter;
    }

        
    public function get($key) {
        return $this->cacheAdapter->getItem($key);
    }

    public function put($key, $data) {
        $this->cacheAdapter->setItem($key, $data);
    }

    public function invalidate($userId) {
        $this->cacheAdapter->clearByPrefix($userId . '-');
    }

}
