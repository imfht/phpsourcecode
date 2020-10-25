<?php

/**
 * This is the model class for table "{{member_games}}".
 *
 * The followings are the available columns in table '{{member_games}}':
 * @property integer $mid
 * @property string $gid1
 * @property string $gid2
 * @property string $gid3
 * @property string $gid4
 * @property string $gid5
 * @property string $gid6
 * @property integer $gnum
 */
class MemberGames extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MemberGames the static model class
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
		return '{{member_games}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mid, gnum', 'required'),
			array('mid, gnum', 'numerical', 'integerOnly'=>true),
			array('gid1, gid2, gid3, gid4, gid5, gid6', 'length', 'max'=>100),
			array('mid, gid1, gid2, gid3, gid4, gid5, gid6, gnum', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'mid' => 'Mid',
			'gid1' => 'Gid1',
			'gid2' => 'Gid2',
			'gid3' => 'Gid3',
			'gid4' => 'Gid4',
			'gid5' => 'Gid5',
			'gid6' => 'Gid6',
			'gnum' => 'Gnum',
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

		$criteria->compare('mid',$this->mid);
		$criteria->compare('gid1',$this->gid1,true);
		$criteria->compare('gid2',$this->gid2,true);
		$criteria->compare('gid3',$this->gid3,true);
		$criteria->compare('gid4',$this->gid4,true);
		$criteria->compare('gid5',$this->gid5,true);
		$criteria->compare('gid6',$this->gid6,true);
		$criteria->compare('gnum',$this->gnum);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	public function getMemberGames($id){
		$MemberGames=MemberGames::model()->findByAttributes(array('mid'=>$id));
		if($MemberGames){
			for($i=1;$i<7;$i++){
				$value="gid".$i;
				if($MemberGames->$value){
					$MemberGamesArr[]=$MemberGames->$value;
				}
			
			}
			return $MemberGamesArr;
		}
		
		
	}
}