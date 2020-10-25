<?php

/**
 * This is the model class for table "{{category}}".
 *
 * The followings are the available columns in table '{{category}}':
 * @property integer $id
 * @property string $title
 * @property integer $parentid
 * @property integer $module
 * @property integer $type
 * @property string $keywords
 * @property string $description
 * @property integer $sort
 * @property integer $status
 */
class NewsCategory extends BaseModel
{
	const SHOW_SELECT='————请选择————';
	const SHOW_TOPCATGORY='————顶级分类————';
	const SHOW_ALLCATGORY='————查看全部分类————';

	public $imagefile;

    public $url;
	/**
	 * Returns the static model of the specified AR class.
	 * @return NewsCategory the static model class
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
		return '{{news_category}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title,parentid', 'required'),
			array('imagefile','file','allowEmpty'=>true,'types'=>'jpg, gif, png','maxSize'=>1024 * 1024 * 10,'tooLarge'=>'上传图片已超过10M'),
			array('id, parentid,sort,status,createtime,updatetime', 'numerical', 'integerOnly'=>true),
			array('title,shorttitle', 'length', 'max'=>100),
			array('keywords,seotitle', 'length', 'max'=>100),
			array('imgurl', 'length', 'max'=>200),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, parentid, seotitle, keywords, description, sort, status', 'safe', 'on'=>'search'),
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
            'parent' => array(CActiveRecord::BELONGS_TO, 'NewsCategory', 'parentid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => '栏目',
			'shorttitle' => '栏目名缩写',
			'parentid' => '上级栏目',
			'imagefile' => '标签图片',
			'seotitle' => 'SEO标题',
			'keywords' => 'SEO关键词',
			'description' => 'SEO描述',
			'sort' => '排序',
            'createtime' => '创建时间',
            'updatetime' => '更新时间',
			'status' => '状态',
//			'mark' => '标识',
		);
	}

	protected function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$this->status=Yii::app()->params['status']['ischecked'];
			}
			return true;
		}
		else
			return false;
	}
	
	/**
	 * 分类目录递归列表
	 * 返回父类的所有子类
	 * &$categoryList 引用传值返回
	 */
	public function getAllCategory(&$categoryList,$category,$parentid=0){
			foreach($category as $k=>$v){
			if($v['parentid']==$parentid){
				$categoryList[]=$v;
				$this->getAllCategory($categoryList,$category,$v['id']);
			}
		}
	}
	public function getAllCategoryIds(&$categoryList,$category,$parentid=0){
			foreach($category as $k=>$v){
			if($v['parentid']==$parentid){
				$categoryList[]=$v['id'];
				$this->getAllCategoryIds($categoryList,$category,$v['id']);
			}
		}
	}
	
	/**
	 * 级联显示
	 * 分类目录递归列表
	 * 返回父类的所有子类
	 * &$categoryList 引用传值返回
	 */
	public function showAllCategory(&$categoryList,$category,$parentid=0,$separate="")
	{
		foreach($category as $k=>$v){
			if($v['parentid']==$parentid){
				$v['title']=$separate.$v[title];
				$categoryList[]=$v;
				$this->showAllCategory($categoryList,$category,$v['id'],$separate."——");
			}
		}
	}       
	/**
	 * 分类目录下拉列表
	 */       
	public function showAllSelectCategory($selectText='')
	{
        // 确保只取出未删除的分类
        $criteria = new CDbCriteria();
        $criteria->compare('status', Yii::app()->params['status']['ischecked']);

		$category=NewsCategory::model()->select('id,title,parentid')->order('sort desc')->findAll($criteria);
		$categoryList=array();
		$this->showAllCategory($categoryList,$category);
		if(!empty($selectText))
			$categorys=array('0'=>$selectText);
		else 
			$categorys=array(''=>NewsCategory::SHOW_SELECT);
		foreach($categoryList as $v){
			$categorys[$v[id]]=$v[title];
		}
		return $categorys;
	}
}