<?php

/**
 * This is the model class for table "subscribe".
 *
 * The followings are the available columns in table 'subscribe':
 * @property integer $id
 * @property string $vid
 * @property integer $status
 * @property string $content
 * @property integer $user_id
 * @property string $createtime
 * @property string $updatetime
 */
class Subscribe extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subscribe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vid, status, content, user_id, createtime, updatetime', 'required'),
			array('status, user_id', 'numerical', 'integerOnly'=>true),
			array('vid, createtime, updatetime', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vid, status, content, user_id, createtime, updatetime', 'safe', 'on'=>'search'),
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
			'vid' => 'Vid',
			'status' => 'Status',
			'content' => 'Content',
			'user_id' => 'User',
			'createtime' => 'Createtime',
			'updatetime' => 'Updatetime',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('vid',$this->vid,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('createtime',$this->createtime,true);
		$criteria->compare('updatetime',$this->updatetime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Subscribe the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
