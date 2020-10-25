<?php

/**
 * This is the model class for table "{{room}}".
 *
 * The followings are the available columns in table '{{room}}':
 * @property integer $id
 * @property string $roomname
 *
 * The followings are the available model relations:
 * @property User[] $users
 */
class Room extends CActiveRecord
{
	private static $_items=array();
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Room the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{room}}';
	}
	
	public function items()
	{
		$models=self::model()->findAll();
		foreach($models as $model)
			self::$_items[$model->id]=$model->roomname;
		return self::$_items;
	}
	public function item($id)
	{
		$rooms=self::items();
		$roomids=User::roomitems();
		$room=$rooms[$roomids[$id]];
		return $room;
	}
	public function roomname($roomid)
	{
		$rooms=self::items();
		$roomname=$rooms[$roomid];
		return $roomname;
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('roomname', 'required'),
			array('roomname', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, roomname', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'users' => array(self::HAS_MANY, 'User', 'roomid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'roomname' => 'Roomname',
		);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('roomname',$this->roomname,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}