<?php

/**
 * This is the model class for table "user_book_favorites".
 *
 * The followings are the available columns in table 'user_book_favorites':
 * @property string $id
 * @property string $title
 * @property integer $bookid
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 */
class UserBookFavorites extends BaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserBookFavorites the static model class
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
		return 'user_book_favorites';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, bookid', 'required'),
			array('bookid,type, createtime, updatetime, status', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
//			array('bookid', 'unique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, bookid, createtime, updatetime, status', 'safe', 'on'=>'search'),
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
            'book'=>array(self::BELONGS_TO, 'Book', 'bookid'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => '小说名称',
			'bookid' => '小说编号',
			'type' => '动作类型',
			'createtime' => '收藏时间',
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
		$criteria->compare('bookid',$this->bookid);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}