<?php

/**
 * This is the model class for table "{{article_type}}".
 *
 * The followings are the available columns in table '{{article_type}}':
 * @property integer $id
 * @property integer $tid
 * @property string $typename
 * @property string $create_author_id
 * @property string $up_author_id
 * @property integer $create_time
 * @property integer $up_time
 */
class ArticleType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return ArticleType the static model class
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
		return '{{article_type}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('typename', 'required'),
			array('typename', 'length', 'max'=>50 ,'encoding'=>'utf-8'),
			array('tid,  create_author_id, up_author_id, create_time, up_time', 'safe'),
			array('id, tid, typename, create_author_id, up_author_id, create_time, up_time', 'safe', 'on'=>'search'),
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
			'create_author'=>array(self::BELONGS_TO,'User','create_author_id'),
			'up_author'=>array(self::BELONGS_TO,'User','up_author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tid' => '父级栏目',
			'typename' => '栏目类型',
			'create_author_id' => '创建人',
			'up_author_id' => '更新人',
			'create_time' => '创建时间',
			'up_time' => '更新时间',
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
		$criteria->compare('tid',$this->tid);
		$criteria->compare('typename',$this->typename,true);
		$criteria->compare('create_author_id',$this->create_author_id,true);
		$criteria->compare('up_author_id',$this->up_author_id,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('up_time',$this->up_time);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function getArticleType(){
		$criterial = new CDbCriteria();
		$criterial->condition='tid=0';
		$articleTypeList=  ArticleType::model()->findAll($criterial);
		
		$articleTypeList=CHtml::listData($articleTypeList, 'id', 'typename');
		$articleTypeList[0]='顶级栏目';
		ksort ($articleTypeList);
		reset($articleTypeList);	
		return $articleTypeList ;
	}
	
	public function getParentArticleType($tid)
	{
		if($tid==0)
			return '顶级栏目';
		$model = self::model()->findByAttributes(array('id'=>$tid));
		return $model->typename;
	}
	
	public function getArticleTypeName($id){
		$ArticleTypeName=  ArticleType::model()->findByAttributes(array('id'=>$id));
		return $ArticleTypeName->typename;
	}
	protected function beforeSave() {
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