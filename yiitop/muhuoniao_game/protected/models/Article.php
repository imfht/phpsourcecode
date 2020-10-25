<?php

/**
 * This is the model class for table "{{article}}".
 *
 * The followings are the available columns in table '{{article}}':
 * @property integer $id
 * @property string $tilte
 * @property integer $gid
 * @property integer $tid
 * @property string $keywords
 * @property string $description
 * @property string $imgurl
 * @property integer $content
 * @property integer $create_time
 * @property integer $create_author_id
 * @property integer $up_time
 * @property integer $up_author_id
 * @property integer $display
 */
class Article extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Article the static model class
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
		return '{{article}}';
	}
	
	public function getArticleDisplay($dispaly)
	{
		if($dispaly==0)
		{
			return '草稿';
		}else{
			return '发布';
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
			array('gid, tid, tilte,content', 'required'),
			//array('gid, tid, content, create_time, create_author_id, up_time, up_author_id, display', 'numerical', 'integerOnly'=>true),
			array('tilte, description, imgurl', 'length', 'max'=>255,'encoding'=>'utf-8'),
			array('gid','validatorDefaultId','strength'=>'gid'),
			array('tid','validatorDefaultId','strength'=>'tid'),
			array('keywords', 'length', 'max'=>50,'encoding'=>'utf-8'),
			array('sort_time','validatorStrDate'),
			array('imgurl', 'file','allowEmpty'=>true,
				'types'=>'jpg,gif,png',
				'maxSize'=>1024 * 1024 * 1, // 1MB
				'tooLarge'=>'请上传小于1MB的图片.',
				'on'=>'insert,update'
				),
			//array('imgurl','file','types'=>'jpg,gif,png','on'=>'insert'),
			array('gid,tid,imgurl, content, create_time, create_author_id, up_time, up_author_id, display', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tilte, gid, tid, keywords, description, imgurl, content, create_time, create_author_id, up_time, up_author_id, display', 'safe', 'on'=>'search'),
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
			'articleType'=>array(self::BELONGS_TO,'ArticleType','tid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tilte' => '标题',
			'gid' => '选择游戏',
			'tid' => '选择栏目',
			'keywords' => '关键字',
			'description' => '描述',
			'imgurl' => '缩略图',
			'content' => '内容',
			'create_time' => '创建时间',
			'create_author_id' => '创建人',
			'up_time' => '更新时间',
			'sort_time' => '发布时间',
			'up_author_id' => '更新人',
			'display' => '是否发布',
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
		$criteria->compare('tilte',$this->tilte,true);
		$criteria->compare('gid',$this->gid);
		$criteria->compare('tid',$this->tid);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('imgurl',$this->imgurl,true);
		$criteria->compare('content',$this->content);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('create_author_id',$this->create_author_id);
		$criteria->compare('up_time',$this->up_time);
		$criteria->compare('sort_time',$this->sort_time);
		$criteria->compare('up_author_id',$this->up_author_id);
		$criteria->compare('display',$this->display);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}
	/*
	*验证规则游戏跟栏目
	*/
	public function validatorDefaultId($attribute,$params){
		if($params['strength'] === 'gid'){
			if($this->gid<1){
				$this->addError($attribute, '请选择游戏!');
			}
		}
		if($params['strength'] ===  'tid'){
			if($this->tid<1){
				$this->addError($attribute, '请选择栏目!');
			}
		}			
	}
	/*
	 * 验证规则日期并格式化成时间戳
	 */
	public function validatorStrDate($attribute,$params){
		$match='/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
		if(preg_match($match,$this->sort_time)){
			$this->sort_time=strtotime($this->sort_time);
		}else{
			$this->addError($attribute, '日期格式不正确!');
		}
	}
	/*
	*默认插入更新操作用户及其时间
	*/
	protected  function beforeSave() {
		if(parent::beforeSave()){
			
			if($this->isNewRecord){
				$this->create_author_id=Yii::app()->user->id;
				$this->create_time=time();
			}else{
				$this->up_author_id=Yii::app()->user->id;
				$this->up_time=time();
			}
			return true;
		}else{
			return false;
		}
		
	}
}