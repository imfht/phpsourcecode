<?php

/**
 * This is the model class for table "{{bool}}".
 *
 * The followings are the available columns in table '{{bool}}':
 * @property integer $id
 * @property string $is_true
 */
class Bool extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Bool the static model class
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
		return '{{bool}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('is_true', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, is_true', 'safe', 'on'=>'search'),
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
			'users' => array(self::HAS_MANY, 'User', 'receive_email'),
			'users'=>array(self::HAS_MANY,'User','receive_remind'),
		);
	}
	
	public static function items()
	{
		$bools=array();
		$models=self::model()->findAll();
		foreach($models as $model)
			$bools[$model->id]=$model->is_true;
		return $bools;
	}
	public function item($id)
	{
		$bools=self::items();
		$is_no=$bools[$id];
		return $is_no;
	}
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'is_true' => 'Is True',
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
		$criteria->compare('is_true',$this->is_true,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}