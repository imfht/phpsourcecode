<?php

/**
 * This is the model class for table "{{member}}".
 *
 * The followings are the available columns in table '{{member}}'=>
 * @property integer $id
 * @property string $mname
 * @property string $password
 * @property string $headimg
 * @property string $email
 * @property integer $qq
 * @property string $telephone
 * @property string $address
 * @property string $real_name
 * @property string $id_card
 * @property string $ip
 * @property integer $login_time
 */
class Member extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Member the static model class
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
		return '{{member}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE=> you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mname,password', 'required'),
			array('password', 'validatorPassword'),
			array('email', 'email','allowEmpty'=>false,),
			array('qq', 'match', 'allowEmpty'=>false, 'pattern'=>'/^[1-9][0-9]{3,12}$/', 'message'=>'请填写正确的QQ号'),
			array('telephone', 'match', 'allowEmpty'=>false, 'pattern'=>'/^0{0,1}1(3|5|8)[0-9]{9}$/','message'=>'请输入正确的手机号'),
			array('address','required','message'=>'请输入正确的地址'),
			array('real_name', 'validatorRegistration','type'=>'realName'),
			array('id_card', 'validatorRegistration','type'=>'idCard'),
			array('headimg', 'file','allowEmpty'=>true,
				'types'=>'jpg,gif,png',
				'wrongType'=>'本站只允许上传jpg png gif头像.',
				'maxSize'=>1024 * 1024 * 2, // 2MB
				'tooLarge'=>'请上传小于2MB的图片.',
				),
			array('qq,address,login_time,ip', 'safe'),
			array('id, mname, password, headimg, email, qq, telephone, address, real_name, id_card, ip, login_time, email_validate', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE=> you may need to adjust the relation name and the related
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
			'mname' => '昵称',
			'password' => '密码',
			'headimg' => '头像',
			'email' => '邮箱',
			'qq' => 'QQ',
			'telephone' => '电话',
			'address' => '地址',
			'real_name' => '真实姓名',
			'id_card' => '身份证号',
			'ip' => 'Ip',
			'login_time' => '登陆时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning=> Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('mname',$this->mname,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('headimg',$this->headimg,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('qq',$this->qq);
		$criteria->compare('telephone',$this->telephone,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('login_time',$this->login_time);
		$criteria->compare('email_validate',$this->email_validate);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
	/*
	 * 用户的密码加密
	 */
	public function encrypt($pass,$type='insert')
	{
		if($type=='insert'){
			return substr(md5($pass), 8, -8);
		}else{
			$updatePass= Member::model()->findByAttributes(array('id'=>$type));
			if($updatePass->password==$pass){
				return $pass;
			}else{
				return substr(md5($pass), 8, -8);
			}
		}
		
	}
	/*
	 * 验证用户名只能是中文
	 */
	
	public function validatorRegistration($attribute,$params) {
		$this->real_name=trim($this->real_name);
		$this->id_card=trim($this->id_card);

		if($params['type']==='realName'){
			if($this->real_name != ""){
				$match='/^[\x{4e00}-\x{9fa5}]{2,4}$/u';
				if(preg_match($match,$this->real_name)){
					$this->real_name=$this->real_name;
				}else{
					$this->addError($attribute, '真实姓名格式不正确!');
					return false;
				}
			}else{
				$this->addError($attribute, '真实姓名不能为空');
				return false;
			}
		}
		if($params['type']==='idCard'){
			if($this->id_card=="")
			{
				$this->addError($attribute, '身份证号不能为空');
				return false;
			}
			if(strlen($this->id_card)!=18)
			{
				$this->addError($attribute, '请输入18位有效身份证号');
				return false;
			}
			$arr = array(11=>"北京",12=>"天津",13=>"河北",14=>"山西",15=>"内蒙古",21=>"辽宁",22=>"吉林",23=>"黑龙江",31=>"上海",32=>"江苏",33=>"浙江",34=>"安徽",35=>"福建",36=>"江西",37=>"山东",41=>"河南",42=>"湖北",43=>"湖南",44=>"广东",45=>"广西",46=>"海南",50=>"重庆",51=>"四川",52=>"贵州",53=>"云南",54=>"西藏",61=>"陕西",62=>"甘肃",63=>"青海",64=>"宁夏",65=>"新疆",71=>"台湾",81=>"香港",82=>"澳门",91=>"国外");
			if($arr[substr($this->id_card, 0,2)]==null)
			{
				$this->addError($attribute, '身份证号地区有误');
				return false;
			}
			if(substr($this->id_card,6,4)%4==0 || (substr($this->id_card,6,4)%100==0 && substr($this->id_card,6,4)%4==0))
			{
				//瑞年出生的正则
				$match = '/^[1-9][0-9]{5}(19|20|21)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/';
			}else{
				//平年出生的正则
				$match = '/^[1-9][0-9]{5}(19|20|21)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/';
			}
			if(!preg_match($match,$this->id_card))
			{
				$this->addError($attribute, '请检查出生日期是否正确');
				return false;
			}
			$idcard_array = str_split($this->id_card);
			$S = (($idcard_array[0]) + ($idcard_array[10])) * 7+ (($idcard_array[1]) + ($idcard_array[11])) * 9+ (($idcard_array[2]) + ($idcard_array[12])) * 10+ (($idcard_array[3]) + ($idcard_array[13])) * 5+ (($idcard_array[4]) + ($idcard_array[14])) * 8+ (($idcard_array[5]) + ($idcard_array[15])) * 4+ (($idcard_array[6]) + ($idcard_array[16])) * 2+ ($idcard_array[7]) * 1 + ($idcard_array[8]) * 6+ ($idcard_array[9]) * 3 ;
			$Y = $S % 11;
			$M = "F";
			$JYM = "10X98765432";
			$M = substr($JYM,$Y,1);
			if($M != $idcard_array[17])
			{
				$this->addError($attribute, '请检查身份证效验码');
				return false;
			}
			
			return true;
		}
	
	}
	/*
	 * 验证密码
	 */
	public function validatorPassword($attribute,$params){
		if($params=='register'){
			$this->password=$attribute;
			$match='/^[a-zA-Z0-9]{6,16}$/';
			if(preg_match($match,$this->password)){	
				return true;
			}else{
				return false;
			}
		}
		$match='/^[a-zA-Z0-9]{6,16}$/';
		if(!preg_match($match,$this->password)){
			$this->addError($attribute, '密码不符合规则(6-16位只能包含数字跟字母)！');
		}
	}


	
	public function getMemberMessage($id){
		$Member=Member::model()->findByAttributes(array('id'=>$id));
		$Memberarr['name']=$Member->mname;
		$Memberarr['headimg']=$Member->headimg;
		$Memberarr['email']=$Member->email;
		$Memberarr['qq']=$Member->qq;
		$Memberarr['telephone']=$Member->telephone;
		$Memberarr['address']=$Member->address;
		$Memberarr['real_name']=$Member->real_name;
		$Memberarr['login_time']=$Member->login_time;
		$Memberarr['email_validate']=$Member->email_validate;
		if(empty($Memberarr['headimg'])){
			$Memberarr['headimg']="headimg-default.jpg";
		}
		return $Memberarr;
	}
	/*
	 * 默认的插入和更新
	 */
	protected function beforeSave() {
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->password=  $this->encrypt($this->password);
			}else{
				$this->password=  $this->encrypt($this->password,$this->id);
			}
			return true;
		}else{
			return false;
		}
	}
}