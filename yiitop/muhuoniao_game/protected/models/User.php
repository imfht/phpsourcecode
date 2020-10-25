<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $cteate_time
 * @property integer $status
 * @property integer $login_time
 * @property string $ip
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password,status', 'required'),
			array('username', 'length', 'max'=>30),
			array('id, username, password, cteate_time, status, login_time, ip', 'safe', 'on'=>'search'),
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
			'username' => '用户名',
			'password' => '密码',
			'cteate_time' => 'Cteate Time',
			'status' => '权限',
			'login_time' => 'Login Time',
			'ip' => 'Ip',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('cteate_time',$this->cteate_time);
		$criteria->compare('status',$this->status);
		$criteria->compare('login_time',$this->login_time);
		$criteria->compare('ip',$this->ip,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
	
	public function getUserStatus($status)
	{
		return Yii::app()->params['role'][$status];
	}
	
	public function encrypt($pass,$type='insert')
	{
		if($type=='insert'){
			return substr(md5($pass), 8, -8);
		}else{
			$updatePass= User::model()->findByAttributes(array('id'=>$type));
			if($updatePass->password==$pass){
				return $pass;
			}else{
				return substr(md5($pass), 8, -8);
			}
		}
	}
	/*
	 * 获取所有管理员名称
	 */
	public function getAuthor(){
		$AuthorList= User::model()->findAll();
		$AuthorList=CHtml::listData($AuthorList, 'id', 'username');	
		return $AuthorList ;
	}
	/*
	 * 权限管理员名称
	 */
	public function getAuthorStatus($statusId){
		$AuthorList= User::model()->findAll("status<=:status",array(':status'=>$statusId)); 
		$AuthorList=CHtml::listData($AuthorList, 'id', 'username');
		return $AuthorList;
	}
	/*
	 * 获取管理员名称
	 */
	public function getAuthorName($id){
		$authorName=  User::model()->findByAttributes(array('id'=>$id));
		return $authorName->username;
	}
	/*
	*获取对应的权限名称
	*/
	public function getStatusName($id){
		$user=  User::model()->findByAttributes(array('id'=>$id));
		$StatusName=Yii::app()->params['role'];
		return $StatusName[$user->status];
	}
	/*
	 * 插入数据
	 */
	protected function beforeSave(){
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->cteate_time=  time();
				$this->password=  $this->encrypt($this->password);
				$this->ip=Yii::app()->request->userHostAddress;
			}else{
				$this->password=  $this->encrypt($this->password,$this->id);
			}
			return true;
		}else{
			return false;
		}
	}
	
}