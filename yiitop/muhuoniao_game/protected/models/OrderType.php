<?php

/**
 * This is the model class for table "{{order_type}}".
 *
 * The followings are the available columns in table '{{order_type}}':
 * @property integer $id
 * @property string $name
 * @property integer $status
 */
class OrderType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return OrderType the static model class
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
		return '{{order_type}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
			array('id, name, status', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'status' => 'Status',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function getOrderTypeAll(){
		$orderTypeAll= OrderType::model()->findAll("status=:status",array('status'=>'1'));
		$orderTypeAll=CHtml::listData($orderTypeAll, 'id', 'name');
		return $orderTypeAll;
	}
}