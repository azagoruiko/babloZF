<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bablo\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Description of IncomeForm
 *
 * @author andrii
 */
class IncomeForm extends Form {
    protected $em;
    
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm() {
        return $this->em;
    }

    public function setEm($em) {
        $this->em = $em;
    }
    
    private function setupFields() {
        $this->setHydrator(new ClassMethods());
        $this->add(array(
             'name' => 'id',
             'type' => 'hidden',
         ));
        $this->add(array(
             'name' => 'amount',
             'type' => 'Number',
             'options' => array(
                 'label' => 'Amount',
             ),
         ));
        /*$this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'currency_id',
            'attributes' => ['type' => 'select', 'id' => 'currency_id'],
            'options' => ['label' => 'Currency: '],
        ]);*/
        
        $this->add(array(
           'name' => 'currency',
           'type' => 'DoctrineModule\Form\Element\ObjectSelect',
           'options' => array(
                'object_manager'     => $this->em,
                'target_class'       => 'bablo\model\Currency',
                'property' => 'name',
                'is_method' => true,
                'find_method'        => array(
                    'name'   => 'findAll',
                ),
            ), 
        ));
        
        $this->add(array(
             'name' => 'date',
             'type' => 'date',
             'options' => array(
                 'label' => 'Date',
             ),
         ));
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'source_id',
            'attributes' => ['type' => 'select', 'id' => 'currency_id', 'multiple' => 'multiple'],
            'options' => ['label' => 'Source: '],
        ]);
        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Add',
                 'id' => 'submitbutton',
             ),
         ));
    }
    
    private function setUpFilters() {
        $filter = $this->getInputFilter();
        $filter->add(array(
            'name'     => 'amount',
            'required' => true,
            'filters'  => array(
                array('name' => 'Int'),
            ),
        ));
        $filter->add(array(
            'name'     => 'source_id',
            'required' => true,
        ));
        $filter->add(array(
            'name'     => 'date',
            'required' => true,
        ));
    }
    
    function __construct($name = null) {
        if ($name instanceof \Doctrine\ORM\EntityManager) {
            $this->em = $name;
        }
        parent::__construct('income');
        $this->setupFields();
        $this->setUpFilters();
    }
}
