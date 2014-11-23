<?php

namespace bablo\model;

class IncomeSearchFilter implements \JsonSerializable {
    private $monthFrom;
    private $monthTo;
    private $minAmount;
    private $maxAmount;
    private $source;
    private $currency;
    public function getMonthFrom() {
        return $this->monthFrom;
    }

    public function getMonthTo() {
        return $this->monthTo;
    }

    public function getMinAmount() {
        return $this->minAmount;
    }

    public function getMaxAmount() {
        return $this->maxAmount;
    }

    public function getSource() {
        return $this->source;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function setMonthFrom($monthFrom) {
        $this->monthFrom = $monthFrom;
    }

    public function setMonthTo($monthTo) {
        $this->monthTo = $monthTo;
    }

    public function setMinAmount($minAmount) {
        $this->minAmount = $minAmount;
    }

    public function setMaxAmount($maxAmount) {
        $this->maxAmount = $maxAmount;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

        
    public function exchangeArray($data) {
        foreach ($data as $field => $val) {
            $this->$field = $val;
        }
    }
    
    public function toArray() {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

    public function jsonSerialize() {
        return json_encode($this->toArray());
    }

}
