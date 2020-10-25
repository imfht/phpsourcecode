<?php

/**
 * This is the model class for table "{{article}}".
 *
 * The followings are the available columns in table '{{article}}':
 * @property integer $id
 * @property string $title
 * @property integer $cid
 * @property string $summary
 * @property string $content
 * @property string $tags
 * @property string $keywords
 * @property string $description
 * @property integer $userid
 * @property integer $createtime
 * @property integer $updatetime
 * @property string $recommend
 * @property integer $recommendlevel
 * @property integer $status
 * @property integer $hits
 */
class Article extends BaseModel
{
	public $imagefile;

    public $content = null;

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

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bookid,title', 'required'),
			array('imgurl','file','allowEmpty'=>true,'types'=>'jpg, gif, png','maxSize'=>1024 * 1024 * 10,'tooLarge'=>'上传图片已超过10M'),
			array('id,chapter,createtime, updatetime, recommendlevel, status, hits', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=> 100),
			array('imgurl, linkurl', 'length', 'max'=>255),
//			array('summary', 'length', 'max'=>500),
			array('tags, keywords, seotitle', 'length', 'max'=>100),
			array('content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, content, seotitle, keywords, description, userid, createtime, updatetime, recommendlevel, status, hits', 'safe', 'on'=>'search'),
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
            'id' => '编号',
            'bookid' => '所属小说',
            'chapter' => '章节',
			'title' => '章节标题',
			'content' => '内容',
			'imgurl' => '封面图',
			'seotitle'=>'页面标题',
			'keywords' => '关键词',
//			'chapternum' => '实际章节号',
			'createtime' => '发布时间',
			'updatetime' => '更新时间',
//			'recommend' => '推荐类型',
			'recommendlevel' => '排序级别',
			'status' => '状态',
			'hits' => '点击数',
		);
	}
//	protected function beforeSave()
//	{
//		if(parent::beforeSave())
//		{
//			if($this->isNewRecord)
//			{
//				$this->createtime=$this->updatetime=time();
//				$this->status=Yii::app()->params['status']['ischecked'];
//				$this->hits=0;
//			}
//			else
//			{
//				$this->updatetime=time();
//			}
//			return true;
//		}
//		else
//			return false;
//	}

    /**
     * 小说章节插入前，更新章节数和字数
     * @return bool|void
     */
//    protected function beforeSave()
//    {
//        if (parent::beforeSave()) {
//            if ($this->isNewRecord) {
//                $this->book->chaptercount += 1;
//                $this->book->wordcount += mb_strlen($this->content);
//                $this->book->lastchapterid = $this->id;
//                $this->book->lastchaptertitle = $this->title;
//                $this->book->save();
//            }
//
//            return true;
//        }
//        return false;
//    }

    /**
     * 小说章节插入后，更新章节数和字数，更新最后章节编号、章节名、章节更新时间
     * @return bool|void
     */
    protected function afterSave()
    {

        if ($this->isNewRecord) {
            $this->book->chaptercount += 1;
            $this->book->wordcount += mb_strlen($this->content);
            $this->book->lastchapterid = $this->id;
            $this->book->lastchaptertime = $this->createtime;
            $this->book->save();
        } else {
            $this->book->lastchapterid = $this->id;
            $this->book->lastchaptertitle = $this->title;
            $this->book->lastchaptertime = $this->createtime;
            $this->book->save();
        }
        // 保存小说章节内容
        $this->saveContentToFile();

        return parent::afterSave();
    }

    /**
     * 填充 content 字段
     */
    public function afterFind()
    {
        $this->content = $this->getContentFromFile();

        return parent::afterFind();
    }

    /**
     * 删除前处理章节数、字符素
     * @return bool|void
     */
    protected function beforeDelete()
    {
        $this->deleteContentFile();

        if ($this->book->chaptercount >= 1) {
            $this->book->chaptercount -= 1;
        }

        $len = mb_strlen($this->content);
        if ($this->book->wordcount >= $len) {
            $this->book->wordcount -= mb_strlen($this->content);
        }

        $this->book->save();

        return parent::beforeDelete();
    }

    /**
     * 删除小说章节内容文件
     * @return bool
     */
    protected function deleteContentFile()
    {
        $p = $this->getArticleDataPath();

        if (file_exists($p)) return @unlink($p);

        return false;
    }

    /**
     * 保存小说章节内容
     * @return bool|int
     */
    protected function saveContentToFile()
    {
        if (null != $this->content) {
            $p = $this->getArticleDataPath();

            if (null == p) return false;

            if (!$this->makeArticleDataDir($p)) return false;

            return @file_put_contents($p, $this->content);
        }

        return false;
    }

    /**
     * 从文件中读到小说章节内容
     * @return null|string
     */
    protected function getContentFromFile()
    {
        $p = $this->getArticleDataPath();
        if (!file_exists($p)) return null;

        return @file_get_contents($p);
    }

    /**
     * 获得小说章节内容路径
     * @return null|string
     */
    protected function getArticleDataPath()
    {
        $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR. ".." . DIRECTORY_SEPARATOR  . ".." . DIRECTORY_SEPARATOR  . "txt";
        if (null != $this->bookid && $this->bookid > 0) {
            $dir .= DIRECTORY_SEPARATOR . ($this->bookid % 500) . DIRECTORY_SEPARATOR . $this->bookid;

            return $dir . DIRECTORY_SEPARATOR . $this->id . ".txt";
        }

        return null;
    }

    /**
     * 创建小说章节内容目录
     * @param $path
     * @return bool
     */
    protected function makeArticleDataDir($path)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            return @mkdir($dir, 0777, true);
        }

        return true;
    }
}