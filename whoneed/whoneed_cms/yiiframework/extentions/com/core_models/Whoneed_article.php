<?php
class Whoneed_article extends CActiveRecord
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
	    return strtolower(get_class($this));
	}

	public function relations()
	{
		return array(
			'slave'=> array(self::HAS_ONE, 'Whoneed_article_content', 'id'),
		);
	}
}
?>