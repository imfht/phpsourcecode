<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $realname
 * @property integer $roleid
 * @property string $telephone
 * @property string $qq
 * @property string $email
 * @property string $address
 * @property integer $createtime
 * @property integer $lastlogintime
 * @property integer $status
 * @property integer $loginhits
 */
class AdminUser extends BaseModel
{
    // 确认密码字段
    public $passwordAgain;

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
		return '{{admin}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password, roleid','required'),
			array('username','unique', 'message' => '账号已存在'),
			array('roleid, createtime, lastlogintime, status, loginhits', 'numerical', 'integerOnly'=>true),
			array('username, password, realname, telephone, qq, email', 'length', 'max'=>32),
			array('address', 'length', 'max'=>200),
            array('passwordAgain','compare', 'compareAttribute' => 'password', 'message' => '两次密码不一致'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, realname, roleid, telephone, qq, email, address, createtime, lastlogintime, status, loginhits', 'safe', 'on'=>'search'),
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
			'username' => '用户名',
			'password' => '密码',
			'passwordAgain' => '确认密码',
			'realname' => '真实姓名',
			'roleid' => '用户角色',
			'telephone' => '手机',
			'qq' => 'QQ',
			'email' => '电子邮件',
			'address' => '地址',
			'createtime' => '注册时间',
			'lastlogintime' => '最近登录时间',
			'status' => '状态',
			'loginhits' => '登录次数',
		);
	}

	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
//				$this->createtime = $this->lastlogintime=time();
//				$this->status = Yii::app()->params['status']['ischecked'];
				$this->loginhits = 0;
                $this->password = User::encrpyt($this->password);
			}
			return true;
		}
		else
			return false;
	}

    /**
     * 更新登陆统计信息
     */
    public function updateLoginInfo()
    {
        $this->lastlogintime = time();
        $this->loginhits += 1;
        $this->save();
    }
}