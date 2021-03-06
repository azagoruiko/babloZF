<?php

namespace Bablo\Service;

use Doctrine\ORM\EntityRepository;

class CurrencyServiceImpl extends EntityRepository implements CurrencyService {
    private $gw;
    private $cache;
    
    public function getGw() {
        return $this->gw;
    }

    public function setGw($gw) {
        $this->gw = $gw;
    }

    public function getCache() {
        return $this->cache;
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

        
    public function getRate($currencyId, $date) {
        $key = "rate-$currencyId-$date";
        if (($rate = $this->getCache()->get($key)) === null) {
            $_rate = $this->getGw()->select(['date' => $date,'currency_id' => $currencyId]);
            foreach ($_rate as $r) {
                $rate[] = $r;
            }
            $this->getCache()->put($key, $rate);
        } 
        return $rate;
    }

    public function setRate($cureencyId, $rate, $date) {
        
    }

    public function findAll() {
        $res = $this->_em->getRepository($this->getEntityName())->
                createQueryBuilder('c')->getQuery()->getResult();
        return $res;
    }

}
