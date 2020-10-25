<?php

/**
 * This is the model class for table "{{post}}".
 *
 * The followings are the available columns in table '{{post}}':
 * @property integer $id
 * @property string $content
 * @property integer $author_id
 * @property string $post_time
 *
 * The followings are the available model relations:
 * @property User $author
 */
class Post extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Post the static model class
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
		return '{{post}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content,title', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('title,author_id', 'safe', 'on'=>'search'),
		);
	}

	public function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->author_id=Yii::app()->user->id;
			}
			// else
				// $this->post_time=date("Y:m:d H:i:s",time());
			return true;
		}
		return false;
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
			'content' => 'Content',
			'title'=>'Title',
			'author_id' => 'Author',
			'post_time' => 'Post Time',
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
		$criteria->compare('title',$this->title,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'post_time desc',
				),
		));
	}
	
	public function searchMyInfo()
	{
		$criteria=new CDbCriteria;
		$criteria->condition='author_id=:id';
		$criteria->params=array(':id'=>Yii::app()->user->id);
		return new CActiveDataProvider($this,array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'post_time desc',
			),
		));
	}
}