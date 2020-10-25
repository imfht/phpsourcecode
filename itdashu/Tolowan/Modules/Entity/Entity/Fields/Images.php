<?php
namespace Modules\Entity\Entity\Fields;

class Images extends Field{
    
    protected $value;
    protected $_array = false;
    public function setValue($value){
        if(is_array($value)){
            $value = serialize($value);
        }
        $this->value = $value;
    }

    public function valueToArray(){
        if($this->_array !== false){
            return $this->_array;
        }
        $this->_array = explode(';',$this->value);
        return $this->_array;
    }

    public function countNum(){
        if(!$this->value){
            return 0;
        }
        return count($this->valueToArray());
    }
}