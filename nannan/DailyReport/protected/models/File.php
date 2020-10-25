<?php

/**
 * This is the model class for table "{{file}}".
 *
 * The followings are the available columns in table '{{file}}':
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $size
 * @property string $type
 * @property integer $author_id
 * @property string $info
 * @property string $upload_time
 *
 * The followings are the available model relations:
 * @property User $author
 */
class File extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return File the static model class
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
		return '{{file}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('info', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>36),
			array('url', 'length', 'max'=>80),
			array('type', 'length', 'max'=>10),
			array('url','file','allowEmpty'=>true,
				'types'=>'jpg,jpeg,png,gif,doc,docx,pdf,ppt,pptx,txt,zip,rar,xls,xlsx',
				'maxSize'=>1024*1024*10,
				'tooLarge'=>'sorry，文件不能大于10M，谢谢。',
				'maxFiles'=>1,
				'tooMany'=>'sorry，一次上传文件不能超过1个，谢谢。'
			),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('author_id', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'User', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'url' => 'Url',
			'size' => 'Size',
			'type' => 'Type',
			'author_id' => 'Author',
			'info' => 'Info',
			'upload_time' => 'Upload Time',
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
		$criteria->compare('author_id',$this->author_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'upload_time desc',
			),
		));
	}
}