<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $email
 * @property integer $roomid
 * @property integer $projectid
 * @property integer $receive_email
 * @property integer $receive_remind
 *
 * The followings are the available model relations:
 * @property Dailyreport[] $dailyreports
 * @property Project $project
 * @property Room $room
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
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
			array('name, password, email', 'required'),
			array('roomid, projectid, receive_email, receive_remind,week_count,off,week_off_count,all_count', 'numerical', 'integerOnly'=>true),
			array('name, password', 'length', 'max'=>30),
			array('email', 'length', 'max'=>50),
			array('email','email'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,name,roomid, projectid', 'safe', 'on'=>'search'),
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
			'dailyreports' => array(self::HAS_MANY, 'Dailyreport', 'author_id'),
			'posts'=>array(self::HAS_MANY,'Post','author_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'projectid'),
			'room' => array(self::BELONGS_TO, 'Room', 'roomid'),
			'is_true'=>array(self::BELONGS_TO,'Bool','receive_email'),
			'is_true1'=>array(self::BELONGS_TO,'Bool','receive_remind'),
			'is_true2'=>array(self::BELONGS_TO,'Bool','off'),
		);
	}
	// public $url;
	// public function getUrl()
	// {
		// return Yii::app()->createUrl('user/view',array(
			// 'id'=>$this->id,
			// 'name'=>$this->name,
		// ));
	// }
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'password' => 'Password',
			'email' => 'Email',
			'roomid' => 'Roomid',
			'projectid' => 'Projectid',
			'receive_email' => 'Receive Email',
			'receive_remind' => 'Receive Remind',
			'week_count' => 'Week Count',
			'all_count' => 'All Count',
		);
	}
	//public static $_items=array();
	public function items()
	{
		$criteria=new CDbCriteria;
		$criteria->select=array('id','name');
		$models=self::model()->findAll($criteria);
		$_items=array();
		foreach($models as $model)
			$_items[$model->id]=$model->name;
		return $_items;
	}
	
	public function offUsers()
	{
		$criteria=new CDbCriteria;
		$criteria->condition='off=1';
		$criteria->select=array('id','name');
		$models=self::model()->findAll($criteria);
		$_items=array();
		foreach($models as $model)
			$_items[$model->id]=$model->name;
		return $_items;
	}
	
	public function roomitems()
	{
		$criteria=new CDbCriteria;
		$criteria->select=array('id','roomid');
		$rooms=array();
		$models=self::model()->findAll($criteria);
		foreach($models as $model)
			$rooms[$model->id]=$model->roomid;
		return $rooms;
	}
	
	public function getWeekCounts(){
		$criteria-new CDbCriteria;
		$criteria->select=array('id','week_count');
		$weekCounts=array();
		$models=self::model()->findAll($criteria);
		foreach($models as $model)
			$weekCounts[$model->id]=$model->week_count;
		return $weekCounts;
	}
	
	public function getCounts(){
		$messages='
			<html>
				<head>
					<title>日报发送情况统计</title>
				</head>
				<body>
					<h1 style="color:#CC6600">上周日报发送情况统计：'.date('Y-m-d',time()-7*24*3600).' 至 '.date('Y-m-d',time()-24*3600).'</h1>
					<table border="1">
						<tr style="background-color:#CCCCFF">
							<th>姓名</th>
							<th>上周未提交日报次数</th>
							<th>本学期累计未提交日报次数</th>
						</tr>
		';
		$message=mb_convert_encoding($message,"utf-8","gbk");
		$addmes='	</table>
				</body>
			</html>
			';
		$criteria=new CDbCriteria;
		$criteria->select=array('id','name','week_count','all_count');
		$criteria->condition='id!=:id';
		$criteria->params=array(':id'=>2);
		$models=self::model()->findAll($criteria);
		if($models==null)
		{
			$messages=null;
			return $messages;
		}
		$mesmodel="<tr style='color:#009966'><td align='center'>%s</td><td align='center'>%d</td><td align='center'>%d</td></tr>";
		foreach($models as $model)
		{
			if(($model->all_count+$model->week_count)==0)
				continue;
			$temp=sprintf($mesmodel,$model->name,$model->week_count,$model->all_count+$model->week_count);
			$messages.=$temp;
		}
		$messages.=$addmes;
		return $messages;
	}
	
	public function getMyWeekCount($author_id){
		$user=self::model()->findByPk($author_id);
		return $user->week_count;
	}
	public function checkUserOff($id){
		$model=self::model()->findByPk($id);
		return $model->off;
	}
	
	public function getMyAllCount($author_id){
		$user=self::model()->findByPk($author_id);
		return $user->all_count;
	}
	
	public function restoreWeekCount($author_id){
		$user=self::model()->findByPk($author_id);
		$user->week_count=0;
		$user->save();
	}
	
	public function updateWeekCount($author_id){
		$user=self::model()->findByPk($author_id);
		$user->week_count=$user->week_count+1;
		$user->save();
	}
	public function updateMyAllCount($author_id){
		$user=self::model()->findByPk($author_id);
		$user->all_count=$user->all_count+self::model()->getMyWeekCount($author_id);
		//$user->all_count=0;
		$user->save();
	}
	
	public function getUserIds(){
		$criteria=new CDbCriteria;
		$criteria->condition='off=0';
		$criteria->select=array('id');
		$ids=array();
		$models=self::model()->findAll($criteria);
		foreach($models as $model)
			$ids[$model->id]=$model->id;
		return $ids;
	}
	
	public function getAllUserIds(){
		$criteria=new CDbCriteria;
		$criteria->select=array('id');
		$ids=array();
		$models=self::model()->findAll($criteria);
		foreach($models as $model)
			$ids[$model->id]=$model->id;
		return $ids;
	}
	public function projectitems()
	{
		$criteria=new CDbCriteria;
		$criteria->select=array('id','projectid');
		$projects=array();
		$models=self::model()->findAll($criteria);
		foreach($models as $model)
			$projects[$model->id]=$model->projectid;
		return $projects;
	}
	public function item($author_id)
	{
		$criteria=new CDbCriteria;
		$criteria->condition='id=:id';
		$criteria->params=array(':id'=>$author_id);
		$model=self::model()->find($criteria);
		//self::$_items[$model->id]=$model->name;
		return $model->name;
	}
	public function validatePassword($password)
	{
		return $this->password===$password;
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
		$criteria->condition='name!=:name';
		$criteria->params=array(':name'=>'admin');
		//$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		//$criteria->compare('password',$this->password,true);
		//$criteria->compare('email',$this->email,true);
		//$criteria->compare('roomid',$this->roomid);
		//$criteria->compare('projectid',$this->projectid);
		//$criteria->compare('receive_email',$this->receive_email);
		//$criteria->compare('receive_remind',$this->receive_remind);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}