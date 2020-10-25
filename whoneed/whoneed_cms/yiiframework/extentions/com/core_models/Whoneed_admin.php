<?php
class Whoneed_admin extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
	    return strtolower(get_class($this));
	}

    public static function getAllAdmin(){
        $arrR = array();
        $arrR['-100'] = '请选择用户';

        $cdb = new CDbCriteria();
        $cdb->select    = 'id,user_name';
        $cdb->order     = "id asc";

        $objDB = self::model()->findAll($cdb);
        if($objDB){
            foreach($objDB as $obj){
                $arrR[$obj->id] = $obj->user_name;
            }
        }
        return $arrR; 
    }
}
?>