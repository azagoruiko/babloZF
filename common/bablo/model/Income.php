<?php
namespace bablo\model;
use Doctrine\ORM\Mapping as ORM;
/** 
 * @ORM\Entity
 * @ORM\Table(name="bablo.income")
 */
class Income implements \JsonSerializable {
    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    */
    protected $id;
    /**
     * @ORM\Column(type="float") 
     */
    protected $amount;
    /**
     * @ORM\ManyToOne(targetEntity="\bablo\model\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     **/
    protected $currency;
    /**
     * @ORM\Column(type="integer") 
     */
    protected $user_id;
    /**
     * @ORM\ManyToOne(targetEntity="\bablo\model\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;
    //protected $source;
    /**
     * @ORM\Column(type="datetime") 
     */
    protected $date;
    /**
     * @ORM\Column(type="integer") 
     */
    protected $currency_id;


    public function getCurrencyId() {
        return $this->currency_id;
    }

    public function setCurrencyId($currency_id) {
        $this->currency_id = $currency_id;
    }

        
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getCurrency() {
        return $this->currency->getName();
    }

    public function getUserid() {
        return $this->user_id;
    }

    public function getSource() {
        return $this->source;
    }

    public function getDate() {
        return $this->date->format('Y-m-d');
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function setUserid($userid) {
        $this->user_id = $userid;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setDate($date) {
        $this->date = $date;
    }
    
    public function getUsdAmount() {
        $rates = $this->currency->getRates();
        $rate = $rates[0];
        return $rate->getRate() * $this->amount;
    }

    public function jsonSerialize() {
        $obj = [];
        foreach ($this as $prop => $val) {
            $obj[$prop] = $val;
        }
        return $obj;
    }
    
    public function exchangeArray($data) {
        foreach ($data as $field => $val) {
            $this->$field = $val;
        }
    } 
    
    public function toArray($withId = true, $withCustomFields = true) {
        $customFields = ['currency', 'source', 'usdAmount'];
        $array = [];
        foreach ($this as $key => $value) {
            if (($key !== 'id' || $withId) && (!in_array($key, $customFields) || $withCustomFields)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }

}
