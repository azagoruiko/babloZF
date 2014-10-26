<?php

namespace bablo\dao;

use bablo\model\Expence;

/**
 *
 * @author andrii
 */
interface ExpenceDAO extends DAO{
    function findAll($userId=0, $month=null, $year=null);
    function save(Expence $income);
}
