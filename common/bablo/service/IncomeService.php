<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace bablo\service;

use bablo\model\Income;

/**
 *
 * @author andrii
 */
interface IncomeService {
    function save(Income $income);
    function findAll($userId, $month=null, $year=null);
    function find($id);
    public function getUpdates($userId=0, $lastId=0, $month=0, $year=0);
    function delete($id);
    function getAnnualBalance ($userId=0, $year=null);
    function getRevenueBrokenByMonth ($userId=0, $month='', $year='');
}
