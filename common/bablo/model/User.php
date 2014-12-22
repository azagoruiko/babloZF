<?php
namespace bablo\model;
use Doctrine\ORM\Mapping as ORM;
//use Doctrine\ORM\Mapping\Ta
/**
 * Description of User
 * @ORM\Entity 
 * @ORM\Table(name="bablo.user")
 * @author andrii
 */
class User {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;
    
    /**
     * @ORM\Column(type="string") 
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string") 
     */
    protected $password;
    /** @ORM\Column(type="string") */
    protected $email;
    
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
