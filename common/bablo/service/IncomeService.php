<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bablo\service;

use bablo\model\Income;
use bablo\model\IncomeSearchFilter;
interface IncomeService {
    function save(Income $income);
    function findAll($userId, IncomeSearchFilter $filter);
    function find($id);
    public function getUpdates($userId=0, $lastId=0, IncomeSearchFilter $filter);
    function delete($id);
    function getAnnualBalance ($userId=0, $year=null);
    function getRevenueBrokenByMonth ($userId=0, $month='', $year='');
}
