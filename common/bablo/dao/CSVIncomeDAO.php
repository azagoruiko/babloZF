<?php



namespace bablo\dao;

use bablo\model\Income;

/**
 * Description of CSVIncomeDAO
 *
 * @author andrii
 */
class CSVIncomeDAO implements IncomeDAO{
    private $file =  'incomes.csv';
    private $incomes = [];
    
    public function __construct() {
        $this->file = \Config::$dataPath . $this->file;
        $filename = $this->file;
        if (!file_exists($filename)) {
            throw new RuntimeException("File not found");
        }
        $data = file($filename);
        foreach ($data as $item) {
            $parts = explode(',', trim($item));
            $income = new Income();
            $income->setId($parts[0]);
            $income->setAmount($parts[1]);
            $income->setCurrency($parts[2]);
            $income->setDate($parts[3]);
            $income->setSource($parts[4]);
            $income->setUserid($parts[5]);
            // TODO: add more fields
            $this->incomes[$income->getId()] = $income;
        }
    }        

    public function find($id) {
        return $this->incomes[$id];
    }

    public function findAll() {
        return $this->incomes;
    }

    public function save(Income $income) {
         if(empty($income->getId())){
            $id = rand(1,PHP_INT_MAX);
            $income->setId($id);
        }
        
        $str ='';
        $this->incomes[$income->getId()] = $income;
        foreach ($this->incomes as $_income){
            $str.=$_income->getId().","
                    .$_income->getAmount().","
                    .$_income->getCurrency().","
                    .$_income->getDate().","
                    .$_income->getSource().","
                    .$_income->getUserid()."\n";
        }
        file_put_contents($this->file,$str);
        return $income;      
    }

}
