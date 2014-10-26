<?php



namespace bablo\dao;

use bablo\model\Income;

/**
 *
 * @author andrii
 */
interface IncomeDAO extends DAO{
    function findAll($userId=0, $month=null, $year=null);
    function save(Income $income);
    function getUpdates($userId=0, $lastId=0);
    function delete($id);
    function getAnnualBalance ($userId=0, $year=null);
    function getRevenueBrokenByMonth ($userId=0, $month='', $year='');
}
