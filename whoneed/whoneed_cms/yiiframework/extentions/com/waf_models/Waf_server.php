<?php
class Waf_server extends CActiveRecord
{
    public function getDbConnection()
    {
        return Yii::app()->db_waf;
    }

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
	    return strtolower(get_class($this));
	}

    }
?>
