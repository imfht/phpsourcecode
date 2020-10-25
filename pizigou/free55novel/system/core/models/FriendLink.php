<?php

/**
 * This is the model class for table "friend_link".
 *
 * The followings are the available columns in table 'friend_link':
 * @property string $id
 * @property string $title
 * @property string $imgurl
 * @property string $linkurl
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 */
class FriendLink extends BaseModel
{
    public $imagefile;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FriendLink the static model class
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
		return 'friend_link';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('createtime,sort, updatetime, status', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('imgurl', 'length', 'max'=>200),
			array('linkurl', 'length', 'max'=>500),
            array('imagefile', 'file', 'allowEmpty'=>true,
                'types'=>'jpg, jpeg, gif, png',
                'maxSize' => 1024 * 1024 * 1, // 1MB
                'tooLarge'=>'上传文件超过 1MB，无法上传',
            ),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, imgurl, linkurl, createtime, updatetime, status', 'safe', 'on'=>'search'),
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
			'title' => '站点标题',
			'imgurl' => '站点LOGO',
			'imagefile' => '站点LOGO',
			'linkurl' => '站点地址',
			'sort' => '排序',
			'createtime' => '发布时间',
			'updatetime' => '更新时间',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('imgurl',$this->imgurl,true);
		$criteria->compare('linkurl',$this->linkurl,true);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}