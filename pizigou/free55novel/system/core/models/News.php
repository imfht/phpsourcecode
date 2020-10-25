<?php

/**
 * This is the model class for table "book".
 *
 * The followings are the available columns in table 'book':
 * @property string $id
 * @property string $title
 * @property string $author
 * @property integer $cid
 * @property string $imgurl
 * @property string $summary
 * @property integer $createtime
 * @property integer $updatetime
 * @property integer $status
 */
class News extends BaseModel
{
    public $imagefile;

    public $url;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Book the static model class
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
		return '{{news}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title,cid', 'required'),
			array('cid, createtime,updatetime,status', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('keywords', 'length', 'max'=>100),
			array('author', 'length', 'max'=>32),
            array('imagefile', 'file', 'allowEmpty'=>true,
                'types'=>'jpg, jpeg, gif, png',
                'maxSize' => 1024 * 1024 * 1, // 1MB
                'tooLarge'=>'上传文件超过 1MB，无法上传',
            ),
			array('imgurl', 'length', 'max'=>200),
			array('summary', 'length', 'max'=>500),
			array('content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, author, cid, imgurl, summary,  createtime, updatetime, recommend, status', 'safe', 'on'=>'search'),
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
            'category' => array(CActiveRecord::BELONGS_TO, 'NewsCategory', 'cid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '新闻编号',
			'title' => '标题',
			'author' => '作者',
			'keywords' => '关键字',
			'cid' => '分类',
			'imgurl' => '封面图',
			'imagefile' => '封面图',
			'summary' => '简介',
			'content' => '内容',
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
		$criteria->compare('author',$this->author,true);
		$criteria->compare('cid',$this->cid);
		$criteria->compare('imgurl',$this->imgurl,true);
		$criteria->compare('linkurl',$this->linkurl,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('tags',$this->tags,true);
		$criteria->compare('seotitle',$this->seotitle,true);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('createtime',$this->createtime);
		$criteria->compare('updatetime',$this->updatetime);
		$criteria->compare('recommend',$this->recommend,true);
		$criteria->compare('recommendlevel',$this->recommendlevel);
		$criteria->compare('hits',$this->hits);
		$criteria->compare('likenum',$this->likenum);
		$criteria->compare('wordcount',$this->wordcount);
		$criteria->compare('sections',$this->sections);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}