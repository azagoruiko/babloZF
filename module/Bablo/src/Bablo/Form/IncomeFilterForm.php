<?php

namespace Bablo\Form;

use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods;

class IncomeFilterForm extends Form {
    private function setupFields() {
        $this->setHydrator(new ClassMethods());
        
        $this->add([
            'name' => 'page',
            'type' => 'Zend\Form\Element\Hidden',
        ]);
        
        $this->add([
            'name' => 'month_from',
            'type' => 'Zend\Form\Element\Select',
            'options' => [
                'label' => 'Since Month and Year',
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);
        
        $this->add([
            'name' => 'month_to',
            'type' => 'Zend\Form\Element\Select',
            'options' => [
                'label' => 'To Month and Year',
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);
        
        $this->add([
            'name' => 'min_amount',
            'type' => 'number',
            'options' => [
                'label' => 'Minimum amount per income',
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);
        
        $this->add([
            'name' => 'max_amount',
            'type' => 'number',
            'options' => [
                'label' => 'Maximum amount per income',
            ],
            'attributes' => [
                'class' => 'form-control',
            ]
        ]);
        
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'currency',
            'attributes' => ['type' => 'select', 'id' => 'currency_id', 'multiple' => 'multiple','class' => 'form-control',],
            'options' => ['label' => 'Currency: '],
        ]);
        
        $this->add([
            'type' => 'Zend\Form\Element\Select',
            'name' => 'source',
            
            'attributes' => ['type' => 'select', 'id' => 'source_id', 'multiple' => 'multiple','class' => 'form-control',],
            'options' => ['label' => 'Source: ',],
        ]);
        
        $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Apply Filters',
                 'id' => 'submitbutton',
                 'class' => 'form-control button btn-success',
             ),
         ));
    }
    
    private function setupFilters() {
        $filter = $this->getInputFilter();
        $filter->add(array(
            'name'     => 'min_amount',
            'required' => false,
            'filters'  => array(
                ['name' => 'Int',
                    'options' => [
                        'min' => 0,
                    ]],
            ),
        ));
        
        $filter->add(array(
            'name'     => 'max_amount',
            'required' => false,
            'filters'  => array(
                ['name' => 'Int',
                    'options' => [
                        'min' => 0,
                    ]],
            ),
        ));
        
        $filter->add(array(
            'name'     => 'currency',
            'required' => false,
            )
        );
        
        $filter->add(array(
            'name'     => 'source',
            'required' => false,
            )
        );
    }
    
    function __construct($name = null) {
        parent::__construct('income_report');
        $this->setupFields();
        $this->setUpFilters();
    }

}
