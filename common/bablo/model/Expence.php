<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bablo\model;

/**
 * Description of Expence
 *
 * @author Денис
 */
class Expence {
    private $id;
    private $amount;
    private $currency;
    private $userid;
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

        public function getCurrency_id() {
        return $this->currency_id;
    }

    public function setCurrency_id($currency_id) {
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
        return $this->userid;
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
        $this->userid = $userid;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setDate($date) {
        $this->date = $date;
    }
}
