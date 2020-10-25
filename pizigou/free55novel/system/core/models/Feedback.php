<?php

/**
 * This is the model class for table "{{feedback}}".
 *
 * The followings are the available columns in table '{{feedback}}':
 * @property integer $id
 * @property string $title
 * @property integer $type
 * @property string $realname
 * @property string $telephone
 * @property string $email
 * @property string $qq
 * @property string $content
 * @property integer $create_time
 * @property integer $status
 */
class Feedback extends BaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Feedback the static model class
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
		return '{{feedback}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title,content,realname,telephone', 'required'),
			array('type, create_time,update_time, status', 'numerical', 'integerOnly'=>true),
			array('title, realname, telephone, email, qq', 'length', 'max'=>32),
			array('content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, type, realname, telephone, email, qq, content, create_time, status', 'safe', 'on'=>'search'),
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
			'title' => '标题',
			'realname' => '联系人',
			'telephone' => '联系电话',
			'email' => '电子邮件',
			'qq' => 'QQ',
			'type'=>'留言类型',
			'content' => '内容',
			'create_time' => '创建时间',
			'update_time' => '更新时间',
		);
	}


	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->create_time=$this->update_time=time();
				$this->status=Yii::app()->params['status']['ischecked'];
			}
			else
			{
				$this->update_time=time();
			}
			return true;
		}
		else
			return false;
	}
}