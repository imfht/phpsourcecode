<?php

/**
 * This is the model class for table "modules".
 *
 * The followings are the available columns in table 'modules':
 * @property integer $id
 * @property string $title
 * @property string $name
 * @property string $author
 * @property string $version
 * @property string $fwversion
 * @property string $description
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 */
class Modules extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Modules the static model class
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
		return 'modules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, name, author, version, fwversion', 'required'),
			array('createtime, updatetime, status', 'numerical', 'integerOnly'=>true),
			array('title, name', 'length', 'max'=>50),
			array('author, version, fwversion', 'length', 'max'=>10),
			array('description', 'length', 'max'=>500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, name, author, version, fwversion, description, createtime, updatetime, status', 'safe', 'on'=>'search'),
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
			'title' => '名称',
			'name' => '模块目录',
			'author' => '作者',
			'version' => '当前版本',
			'fwversion' => '最低飞舞版本',
			'description' => '模块介绍',
			'createtime' => '发布时间',
			'updatetime' => '更改时间',
			'status' => '状态',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('fwversion',$this->fwversion,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}