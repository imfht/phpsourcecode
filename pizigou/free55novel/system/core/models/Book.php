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
 * @property string $linkurl
 * @property string $summary
 * @property string $keywords
 * @property integer $createtime
 * @property integer $updatetime
 * @property string $recommend
 * @property integer $recommendlevel
 * @property integer $hits
 * @property integer $likenum
 * @property integer $wordcount
 * @property integer $sections
 * @property integer $status
 */
class Book extends BaseModel
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
		return '{{book}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title,author,cid', 'required'),
			array('cid,type,lastchapterid,lastchaptertime,createtime,updatetime,recommendlevel, hits, likenum, favoritenum, wordcount, status', 'numerical', 'integerOnly'=>true),
			array('title,lastchaptertitle', 'length', 'max'=>100),
			array('author', 'length', 'max'=>32),
			array('sections', 'length', 'max'=>500),
            array('imagefile', 'file', 'allowEmpty'=>true,
                'types'=>'jpg, jpeg, gif, png',
                'maxSize' => 1024 * 1024 * 1, // 1MB
                'tooLarge'=>'上传文件超过 1MB，无法上传',
            ),
			array('imgurl, linkurl', 'length', 'max'=>200),
			array('summary', 'length', 'max'=>500),
			array('keywords', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, author, cid, imgurl, linkurl, summary, tags, seotitle, keywords, createtime, updatetime, recommend, recommendlevel, hits, likenum, wordcount, sections, status', 'safe', 'on'=>'search'),
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
            'category' => array(CActiveRecord::BELONGS_TO, 'Category', 'cid'),
            'chapter' => array(CActiveRecord::HAS_MANY, 'Article', 'bookid', 'order'=>'chapter.chapter ASC, chapter.id asc', 'on' => 'chapter.status=' . Yii::app()->params['status']['ischecked']),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '小说编号',
			'title' => '小说名',
			'author' => '小说作者',
			'cid' => '栏目',
			'type' => '类型',
			'imgurl' => '封面图',
			'imagefile' => '封面图',
			'linkurl' => 'Linkurl',
			'summary' => '简介',
//			'tags' => '标签',
//			'seotitle' => '搜索优化标题',
			'keywords' => '关键字',
			'createtime' => '发布时间',
			'updatetime' => '更新时间',
//			'recommend' => '编辑推荐',
			'recommendlevel' => '推荐类型',
			'hits' => '点击数',
			'likenum' => '用户推荐数',
			'favoritenum' => '用户收藏数',
			'wordcount' => '字数',
			'sections' => '分卷',
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

    /**
     * 推荐小说
     * @param int $commendLevel
     * @param int $cid
     * @param int $limit
     * @return CActiveDataProvider
     */
    public function getRecommendDataProvider($commendLevel, $cid = 0, $limit = 10)
    {
        $criteria = new CDbCriteria(array(
            'order' => 'createtime desc',
        ));

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
        $criteria->compare('recommendlevel', $commendLevel);
        if ($cid > 0) $criteria->compare('cid', $cid);

        return new CActiveDataProvider($this,array(
            'criteria'=> $criteria,
            'pagination'=>array(
                'pageSize'=> $limit,
            ),
        ));
    }

    /**
     * 最新发布小说
     * @param int $cid
     * @param int $limit
     * @return CActiveDataProvider
     */
    public function getNewestDataProvider($cid = 0, $limit = 10)
    {
        $criteria = new CDbCriteria(array(
            'order' => 'createtime,updatetime desc',
        ));

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
        if ($cid > 0) $criteria->compare('cid', $cid);
//        $criteria->compare('recommendlevel', $commendLevel);

        return new CActiveDataProvider($this,array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize'=> $limit,
            ),
        ));
    }

    /**
     * 最新更新小说
     * @param int $cid
     * @param int $limit
     * @return CActiveDataProvider
     */
    public function getLastUpdateDataProvider($cid = 0, $limit = 10)
    {
        $criteria = new CDbCriteria(array(
            'order' => 'lastchaptertime desc',
        ));

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
        if ($cid > 0) $criteria->compare('cid', $cid);
//        $criteria->compare('recommendlevel', $commendLevel);

        return new CActiveDataProvider($this,array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize'=> $limit,
            ),
        ));
    }

    /**
     * 更新小说统计信息：小说总点击量，小说日点击量，小说周点击量，小说月点击量
     */
    public function updateStats()
    {
        $this->hits += 1;
        $this->save();

        // 按日统计
        $m = BookViewStatsByDay::model()->find(
            'day=:day and bookid=:bookid',
            array(
                ':day' => date('Y-m-d'),
                ':bookid' => $this->id,
            )
        );
        if (!$m) {
            $m = new BookViewStatsByDay();
            $m->bookid = $this->id;
            $m->cid = $this->cid;
            $m->title = $this->title;
            $m->day = date('Y-m-d');
        }
        $m->hits += 1;
        $m->save();

        // 按周统计
        $m = BookViewStatsByWeek::model()->find(
            'year=:year and week=:week and bookid=:bookid',
            array(
                ':year' => date('Y'),
                ':week' => date('W'),
                ':bookid' => $this->id,
            )
        );
        if (!$m) {
            $m = new BookViewStatsByWeek();
            $m->bookid = $this->id;
            $m->cid = $this->cid;
            $m->title = $this->title;
            $m->year = date('Y');
            $m->week = date('W');
        }
        $m->hits += 1;
        $m->save();

        // 按月统计
        $t = strtotime(date('Y-m'));
        $month = date('Y-m-d', $t);
        $m = BookViewStatsByMonth::model()->find(
            'month=:month and bookid=:bookid',
            array(
                ':month' => $month,
                ':bookid' => $this->id,
            )
        );
        if (!$m) {
            $m = new BookViewStatsByMonth();
            $m->bookid = $this->id;
            $m->cid = $this->cid;
            $m->title = $this->title;
            $m->month = $month;
        }
        $m->hits += 1;
        $m->save();
    }

    /**
     * 更新推荐数
     * @param $num
     */
    public function updateLikeNum($num)
    {
        $this->likenum += $num;
        $this->save();
    }

    /**
     * 更新小说收藏数
     * @param $num
     */
    public function updateFavoriteNum($num)
    {
        $this->favoritenum += $num;
        $this->save();
    }

    /**
     * 更新小说最后章节信息
     * @param $chapter Article
     */
    public function updateLastChapter($chapter)
    {
        $this->lastchapterid = $chapter->id;
        $this->lastchaptertitle = $chapter->title;
        $this->lastchaptertime = $chapter->createtime;
        $this->save();
    }
}