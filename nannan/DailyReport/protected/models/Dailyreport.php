<?php

/**
 * This is the model class for table "{{dailyreport}}".
 *
 * The followings are the available columns in table '{{dailyreport}}':
 * @property integer $id
 * @property string $content
 * @property string $create_time
 * @property integer $author_id
 *
 * The followings are the available model relations:
 * @property User $author
 */
class Dailyreport extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Dailyreport the static model class
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
		return '{{dailyreport}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'required'),
			array('content','length','min'=>43),
			array('author_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('author_id', 'safe', 'on'=>'search'),
		);
	}

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
			'create_time' => 'Create Time',
			'author_id' => 'Author',
		);
	}
	public function loadMyTodyModels()
	{
		$models=self::model()->findAllBySql("select *from tbl_dailyreport where author_id=:id and datediff(create_time,curdate())=0 order by create_time desc",array(':id'=>Yii::app()->user->id));
		return $models;
	}
	public function beforeSave()
	{
		if(parent::beforeSave())
		{
			if($this->isNewRecord)
			{
				$models=$this->loadMyTodyModels();
				if($models!=null){
					foreach($models as $model)
						$model->delete();
				}
				$this->author_id=Yii::app()->user->id;
			}
			return true;
		}
		else
			return false;
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

		//$criteria->compare('create_time',$this->create_time);
		$criteria->compare('author_id',$this->author_id);
		$criteria->condition='datediff(create_time,curdate())=0';
		$criteria->join='left join tbl_user on author_id=tbl_user.id';
		$criteria->order='tbl_user.roomid';
		return new CActiveDataProvider('Dailyreport', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'create_time DESC',
			),
		));
	}
	public function searchByAuthor()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$criteria->condition='datediff(create_time,curdate())=0';
		//$criteria->compare('create_time',$this->create_time);
		$criteria->compare('author_id',$this->author_id);
		//$criteria->condition='datediff(create_time,curdate())=0';
		return new CActiveDataProvider('Dailyreport', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'create_time DESC',
			),
		));
	}
	
	public function getNoReportNames(){
		$criteria=new CDbCriteria;
		$criteria->condition='datediff(create_time,curdate())=0';
		$criteria->select=array('author_id');
		$models=self::model()->findAll($criteria);
		$allNames=User::model()->items();
		$offNames=User::model()->offUsers();
		foreach($models as $model){
			unset($allNames[$model->author_id]);
		}
		unset($allNames[2]);
		// $lists="No report today:".mb_convert_encoding(join(" ",$allNames),"utf-8","gbk").",".count($allNames)."person";
		$lists="<p style='color:#0099CC;font-size:170%'>No report today:<span style='color:#FF6666'>".join(" ",$allNames)."</span> ".count($allNames)." person</p><p style='color:#0099CC;font-size:170%'>今天请假人员：".join(" ",$offNames)."</p>";
		return $lists;
	}
	
	public function getNoReportList(){
		$criteria=new CDbCriteria;
		$criteria->condition='datediff(create_time,curdate())=0';
		$criteria->select=array('author_id');
		$models=self::model()->findAll($criteria);
		$allIds=User::model()->getUserIds();
		foreach($models as $model){
			unset($allIds[$model->author_id]);
		}
		unset($allIds[2]);
		return $allIds;
	}
	
	
	public function getReports()
	{
		$messages='
			<html>
				<head>
					<title>日报</title>
				</head>
				<body>
					<h1 style="color:#CC6600">今天的日报,日报日期：'.date('Y-m-d',time()).'</h1>
					<table border="1">
						<tr style="background-color:#CCCCFF">
							<th style="width:50px">作者</th>
							<th style="width:75%">日报内容</th>
							<th style="width:135px">项目</th>
							<th style="width:65px">教研室</th>
						</tr>
		';
		$message=mb_convert_encoding($message,"utf-8","gbk");
		$addmes='	</table>
				</body>
			</html>
			';
		$author=User::model()->items();
		$project=Project::model()->items();
		$room=Room::model()->items();
		$roomids=User::model()->roomitems();
		$projectids=User::model()->projectitems();
		$criteria=new CDbCriteria;
		$criteria->condition='datediff(create_time,curdate())=0';
		$models=self::model()->findAll($criteria);
		if($models==null)
		{
			$messages=null;
			return $messages;
		}
		$mesmodel="<tr><td align='center'>%s</td><td>%s</td><td align='center'>%s</td><td align='center'>%s</td></tr>";
		foreach($models as $model)
		{
			$temp=sprintf($mesmodel,$author[$model->author_id],$model->content,$project[$projectids[$model->author_id]],$room[$roomids[$model->author_id]]);
			$messages.=$temp;
			// $messages.='<tr><td>'.$author[$model->author_id].'</td><td>'.$model->content.'</td>';
			// $messages.='<td>'.$project[$projectids[$model->author_id]].'</td>';
			// $messages.='<td>'.$room[$roomids[$model->author_id]].'</td></tr>';
		}
		$messages.=$addmes;
		return $messages;
	}
	public function searchMyinfo()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->condition='author_id=:id';
		$criteria->params=array(':id'=>Yii::app()->user->id);
		return new CActiveDataProvider('Dailyreport', array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'create_time DESC',
			),
		));
	}
}