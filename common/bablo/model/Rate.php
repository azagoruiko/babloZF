<?php

namespace bablo\model;

use Doctrine\ORM\Mapping as ORM;
/** 
 * @ORM\Entity
 * @ORM\Table(name="bablo.rate") 
 */
class Rate {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;
    
    /**
     * @ORM\Column(type="float") 
     */
    protected $rate;
    
    /**
     * @ORM\Column(type="date") 
     */
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\bablo\model\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     **/
    protected $currency;
    
    public function getId() {
        return $this->id;
    }

    public function getRate() {
        return $this->rate;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setRate($rate) {
        $this->rate = $rate;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }
}
