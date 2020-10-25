<?php
class Whoneed_debug_log extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
	    return strtolower(get_class($this));
	}

    public static function newLog($key, $value)
    {
        $objDB = Whoneed_debug_log::model()->find("ope_key = '{$key}'");
        if(!$objDB){
            $objDB = new Whoneed_debug_log();
            $objDB->ope_key = $key;
            $objDB->ope_value = serialize($value);
            $objDB->save();
        }
    }
}
?>
