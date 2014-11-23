<?php
namespace bablo\model;

/**
 * Description of Income
 *
 * @author andrii
 */
class Income implements \JsonSerializable {
    private $id;
    private $amount;
    private $currency;
    private $user_id;
    private $source;
    private $date;
    private $currency_id;
    private $usdAmount;
    
    public function getUsdAmount() {
        return $this->usdAmount;
    }

    public function setUsdAmount($usdAmount) {
        $this->usdAmount = $usdAmount;
    }

        public function getCurrencyId() {
        return $this->currency_id;
    }

    public function setCurrencyId($currency_id) {
        $this->currency_id = $currency_id;
    }

        
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getUserid() {
        return $this->user_id;
    }

    public function getSource() {
        return $this->source;
    }

    public function getDate() {
        return $this->date;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function setUserid($userid) {
        $this->user_id = $userid;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function jsonSerialize() {
        $obj = [];
        foreach ($this as $prop => $val) {
            $obj[$prop] = $val;
        }
        return $obj;
    }
    
    public function exchangeArray($data) {
        foreach ($data as $field => $val) {
            $this->$field = $val;
        }
    } 
    
    public function toArray($withId = true, $withCustomFields = true) {
        $customFields = ['currency', 'source', 'usdAmount'];
        $array = [];
        foreach ($this as $key => $value) {
            if (($key !== 'id' || $withId) && (!in_array($key, $customFields) || $withCustomFields)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }

}
