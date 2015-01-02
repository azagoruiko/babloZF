<?php
namespace bablo\model;

use Doctrine\ORM\Mapping as ORM;
/** 
 * @ORM\Entity(repositoryClass="Bablo\Service\CurrencyServiceImpl")
 * @ORM\Table(name="bablo.currency") 
 */
class Currency {
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
     * @ORM\OneToMany(targetEntity="\bablo\model\Rate", mappedBy="currency")
     **/
    protected $rates;
    
    public function getRates() {
        return $this->rates;
    }

    public function setRates($rates) {
        $this->rates = $rates;
    }

        public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
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
    
    public function __toString() {
        return $this->name;
    }

}
