<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bablo\model;

class IncomeSearchFilterTest extends \PHPUnit_Framework_TestCase {
    public function test__toString() {
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom('7,14');
        $filter->setMonthTo('8,14');
        $filter->setCurrency([1,2]);
        $filter->setSource([1,2,3]);
        $filter->setMaxAmount(1000);
        $filter->setMinAmount(100);
        $this->assertEquals('-7-14-8-14-100-1000--1-2-3--1-2', $filter->__toString());
    }
    
    public function test__toStringIncompleteFilter() {
        $filter = new IncomeSearchFilter();
        $filter->setSource([1,2,3]);
        $filter->setMaxAmount(1000);
        $filter->setMinAmount(100);
        $this->assertEquals('---100-1000--1-2-3-', $filter->__toString());
    }
    
    public function test__toStringEmptyArrays() {
        $filter = new IncomeSearchFilter();
        $filter->setMonthFrom('7,14');
        $filter->setMonthTo('8,14');
        $filter->setCurrency([]);
        $filter->setSource([]);
        $filter->setMaxAmount(1000);
        $filter->setMinAmount(100);
        $this->assertEquals('-7-14-8-14-100-1000--', $filter->__toString());
    }
}
