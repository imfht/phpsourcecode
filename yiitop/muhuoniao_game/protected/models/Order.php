<?php

/**
 * This is the model class for table "{{order}}".
 *
 * The followings are the available columns in table '{{order}}':
 * @property integer $id
 * @property integer $order_number
 * @property integer $mid
 * @property integer $gid
 * @property string $gid_server_id
 * @property integer $price
 * @property string $pay_type
 * @property integer $pay_time
 * @property string $pay_ip
 */
class Order extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Order the static model class
	 */
	
	//自定义查询字段
	public $mname;
	public $server;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{order}}';
	}
	
	public function getPayName($pay)
	{
		if($pay=='1')
		{
			return '已支付';
		}else{
			return '未支付';
		}
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_number, gid, price', 'required'),
			array('order_number, mid, gid, pay_time', 'numerical', 'integerOnly'=>true),
			array('gid_server_id', 'length', 'max'=>6),
			array('pay_type', 'length', 'max'=>10),
			array('pay_ip', 'length', 'max'=>20),
			array('id, mname, server, order_number, mid, gid, gid_server_id, price, pay_type, pay_time, pay_ip, pay', 'safe', 'on'=>'search'),
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
			'orderType'=>array(self::BELONGS_TO,'OrderType','pay_type'),
			'memberName'=>array(self::BELONGS_TO,'Member','mid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'order_number' => 'Order Number',
			'mid' => 'Mid',
			'gid' => '请选择游戏',
			'gid_server_id' => '请选择大区',
			'price' => '充值金额',
			'pay_type' => 'Pay Type',
			'pay_time' => 'Pay Time',
			'pay_ip' => 'Pay Ip',
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
		$criteria->compare('order_number',$this->order_number);
		$criteria->compare('mid',$this->mid);
		$criteria->compare('gid',$this->gid);
		$criteria->compare('gid_server_id',$this->gid_server_id,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('pay_type',$this->pay_type,true);
		$criteria->compare('pay_time',$this->pay_time);
		$criteria->compare('pay_ip',$this->pay_ip,true);
		$criteria->compare('pay',$this->pay);
		$criteria->with = 'memberName';
		$criteria->compare('mname', $this->mname,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
	
	protected function afterFind()
	{
		$this->mname=$this->memberName->mname;
	}
	/*
	*计算金钱数
	*/
	public function getNumOrder($value){
		return array_sum($value);
	}
	/*
	 * 默认的插入和更新
	 */
	protected function beforeSave() {
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->mid=  Yii::app()->user->id;
				$this->pay_time=  time();
				$this->pay_ip=Yii::app()->request->userHostAddress;
			}else{
				$this->pay_time=  time();
				$this->pay_ip=Yii::app()->request->userHostAddress;
			}
			return true;
		}else{
			return false;
		}
	}
	
}