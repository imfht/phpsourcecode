<?php
namespace Modules\Entity\Entity\Fields;

use Core\Config;

class Kvgroup extends Field
{
    public function setValue($value)
    {
        if (is_array($value)) {
            $value = serialize($value);
        }
        $this->value = $value;
    }
    public function ex(){
        return self::filterValue($this->value,null);
    }
    public static function filterValue($value, $option)
    {
        if(!is_array($value)){
            return unserialize($value);
        }
        return $value;
    }
}