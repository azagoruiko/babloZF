<?php

namespace Bablo\Service;

use Doctrine\ORM\EntityManager;

class DoctrineCurrencyService implements CurrencyService {
    private $em;
    
    /**
     * 
     * @return EntityManager
     */
    
    public function getEm() {
        return $this->em;
    }

    public function setEm(EntityManager $em) {
        $this->em = $em;
    }

        
    public function getRate($currencyId, $date) {
        $key = "rate-$currencyId-$date";
        if (($rate = $this->getCache()->get($key)) === null) {
            $_rate = $this->getEm()
                    ->getRepository('common\model\Rate')
                    ->findBy(['date' => $date,'currency_id' => $currencyId])
                    ->toArray();
            foreach ($_rate as $r) {
                $rate[] = $r;
            }
            $this->getCache()->put($key, $rate);
        } 
        return $rate;
    }

    public function setRate($cureencyId, $rate, $date) {
        
    }
}
