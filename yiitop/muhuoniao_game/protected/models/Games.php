<?php

/**
 * This is the model class for table "{{games}}".
 *
 * The followings are the available columns in table '{{games}}':
 * @property integer $id
 * @property string $gname
 * @property string $server_id
 * @property integer $create_time
 * @property integer $display
 */
class Games extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Games the static model class
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
		return '{{games}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gname', 'required'),
			array('gname', 'validatorGname'),
			array('alias', 'validatorAlias'),
			array('server_id', 'validatorServerId'),
			array('logo', 'file','allowEmpty'=>true,
				'types'=>'jpg,gif,png',
				'maxSize'=>1024 * 1024 * 1, // 1MB
				'tooLarge'=>'请上传小于1MB的图片.',
				'on'=>'insert,update'
				),
			array('imgurl', 'file','allowEmpty'=>true,
				'types'=>'jpg,gif,png',
				'maxSize'=>1024 * 1024 * 1, // 1MB
				'tooLarge'=>'请上传小于1MB的图片.',
				'on'=>'insert,update'
				),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('create_time,display,flag,type', 'safe'),
			array('id, gname, alias,server_id, create_time, display,imgurl,logo,flag,type', 'safe', 'on'=>'search'),
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
			'gname' => '游戏名称',
			'server_id' => '大区',
			'create_time' => '创建时间',
			'display' => '是否发布',
			'logo' => '游戏logo',
			'imgurl' => '游戏缩略图',
			'alias' => '游戏别名',
			'flag'=>'游戏属性',
			'type'=>'游戏类别',
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
		$criteria->compare('gname',$this->gname,true);
		$criteria->compare('server_id',$this->server_id,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('display',$this->display);
		$criteria->compare('logo',$this->logo);
		$criteria->compare('imgurl',$this->imgurl);
		$criteria->compare('alias',$this->alias);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
	/*
	 * 默认插入创建时间
	 */
	protected function beforeSave() {
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->create_time=time();
			}
			return true;
		}else{
			return false;
		};
	}
	/*
	 * server_id的格式化插入更新
	 */
	public function validatorServerId($attribute,$params){
		$i=0;
		$serverIdTrue=0;
		foreach ($this->server_id as $serverId){
			if($serverId){
				$serverIdTrue++;
			}
			$i++;
		}
		if($i>1){
			if($serverIdTrue>0){
				if(in_array('', $this->server_id)){
					$this->server_id=serialize(array_filter($this->server_id));
				}else{
					$this->server_id=serialize($this->server_id);
				}
			}else{
				$this->server_id='';
			}
		}else{
			$this->server_id='';
		}	
	}
	/*
	 * 验证游戏名称是否被占用
	 */
	public function validatorGname($attribute,$params){
		$this->gname=  trim($this->gname);
		$gamesGname=Games::model()->findByAttributes(array('gname'=>$this->gname));
		if($gamesGname && ($gamesGname->id!=$this->id)){
			$this->addError($attribute, '已有此游戏名称！');
		}
		
	}
	/*
	 * 验证游戏别名是否被占用
	 */
	public function validatorAlias($attribute,$params){
		$this->alias=  trim($this->alias);
		$gamesAlias=Games::model()->findByAttributes(array('alias'=>$this->alias));
		if($this->alias){
			if($gamesAlias && ($gamesAlias->id!=$this->id)){
				$this->addError($attribute, '游戏别名已被占用请选择其他的别名！');
			}
		}
		
	}
	
	/*
	 * 获取全部游戏名称
	 */
	public function getGamesAll(){
		$gamesAll= Games::model()->findAll();
		$gamesAll=CHtml::listData($gamesAll, 'id', 'gname');
		return $gamesAll;
	}
	/*
	 * 用于前台获取全部游戏名称
	 */
	public function getGamesAllShow(){
		$gamesAll= Games::model()->findAll("display=:display",array('display'=>'1'));
		$gamesAll=CHtml::listData($gamesAll, 'id', 'gname');
		return $gamesAll;
	}
	/*
	 * 获取游戏名称
	 */
	public function getGamesName($id){
		$gamesName=Games::model()->findByAttributes(array('id'=>$id));
		return array($gamesName->gname,$gamesName->alias);
	}
	/*
	 * 获取游戏服务区
	 */
	public function getGamesServerId($id){
		$gamesServerId=Games::model()->findByAttributes(array('id'=>$id));
		$gamesServerId=unserialize($gamesServerId->server_id);
		return $gamesServerId;
	}
	/*
	 * 获取游戏图片
	 */
	public function getGamesImage($id){
		$gamesName=Games::model()->findByAttributes(array('id'=>$id));
		return array($gamesName->logo,$gamesName->imgurl);
	}
	/*
	 * 获取游戏区
	 */
	public function getGamesServerValue($gid,$gidvalue){
		$gamesServerId=Games::model()->getGamesServerId($gid);
		$returnValue=  array_search($gidvalue,$gamesServerId);
		if($returnValue===false){
			return 0;
		}else{
			return $returnValue+1;	
		}
			
	}
	/*
	 * 检查是否有这个游戏区
	 */
	public function getGamesServerTrue($gid,$gidvalue){
		$gamesServerId=Games::model()->getGamesServerId($gid);
		$returnValue= in_array($gidvalue,$gamesServerId);
		return $returnValue;
	}
}