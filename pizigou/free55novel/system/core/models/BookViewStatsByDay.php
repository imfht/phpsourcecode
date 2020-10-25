<?php
/**
 *
 * @author pizigou <pizigou@yeah.net>
 */
/**
 * This is the model class for table "book_view_stats_by_day".
 *
 * The followings are the available columns in table 'book_view_stats_by_day':
 * @property string $id
 * @property string $title
 * @property integer $bookid
 * @property integer $hits
 * @property string $day
 */
class BookViewStatsByDay extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BookViewStatsByDay the static model class
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
		return 'book_view_stats_by_day';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, bookid, hits, day', 'required'),
			array('bookid, hits', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, bookid, hits, day', 'safe', 'on'=>'search'),
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
            'book' => array(CActiveRecord::BELONGS_TO, 'Book', 'bookid'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '编号',
			'title' => '小说标题',
			'bookid' => '小说编号',
			'hits' => '点击数',
			'day' => '日期',
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
		$criteria->compare('hits',$this->hits);
		$criteria->compare('day',$this->day,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 某日浏览最多小说
     * @param date $day
     * @param int $cid
     * @param int $limit
     * @return CActiveDataProvider
     */
    public function getTopHitsDataProvider($day, $cid = 0, $limit = 10)
    {
        $criteria = new CDbCriteria(array(
            'order' => 'hits desc',
        ));

//        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
        if ($cid > 0) $criteria->compare('cid', $cid);
        $criteria->compare('day', $day);
//        $criteria->compare('recommendlevel', $commendLevel);

        return new CActiveDataProvider($this,array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize'=> $limit,
            ),
        ));
    }
}