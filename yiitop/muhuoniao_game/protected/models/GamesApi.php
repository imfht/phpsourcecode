<?php

/**
 * This is the model class for table "{{games_api}}".
 *
 * The followings are the available columns in table '{{games_api}}':
 * @property integer $id
 * @property integer $gid
 * @property integer $userid
 * @property string $username
 * @property string $password
 */
class GamesApi extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GamesApi the static model class
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
		return '{{games_api}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gid', 'required'),
			array('gid, userid', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>50),
			array('password', 'length', 'max'=>255),
			array('id, gid, userid, username, password', 'safe', 'on'=>'search'),
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
			'gameName'=>array(self::BELONGS_TO,'Games','gid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gid' => '游戏',
			'userid' => '平台服务器的注册用户编号',
			'username' => '平台服务器的通行证帐号',
			'password' => '密钥',
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
		$criteria->compare('gid',$this->gid);
		$criteria->compare('userid',$this->userid);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
}