<?php
namespace bablo\model;

/**
 * Description of User
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("User")
 * @author andrii
 */
class User {
    private $id;
    
    /**
     * @Annotation\Type("Zend\Form\Element\Text") 
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Name"})
     */
    private $name;
    /**
     * @Annotation\Type("Zend\Form\Element\Password") 
     * @Annotation\Required({"required":"true"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Options({"label":"Password"})
     */
    private $password;
    private $email;
    
    function getName() {
        return $this->name;
    }
    
    function setName($name) {
        $this->name = $name;
    }
    
    function getId() {
        return $this->id;
    }
    
    function setId($id) {
        $this->id = $id;
    }
    
    function getPass() {
        return $this->password;
    }
    
    function setPass($pass) {
        $this->password = $pass;
    }
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function toArray() {
        $arr = [];
        foreach ($this as $field => $val) {
            $arr[$field] = $val;
        }
        return $arr;
    } 
    
    public function exchangeArray($data) {
        foreach ($data as $field => $val) {
            $this->$field = $val;
        }
    } 

}
