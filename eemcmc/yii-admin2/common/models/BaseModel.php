<?php

namespace common\models;

use yii\db\ActiveRecord;
use common\db\BaseActiveQuery;

/**
 * 模型基类对象
 *
 * @author ken <vb2005xu@qq.com>
 */
abstract class BaseModel extends ActiveRecord
{

	/**
	 * 创建对象
	 * @param array $attributes
	 * @return \self
	 */
	public static function create($attributes, $scenario = 'default')
	{
		$model = new static();
		$model->scenario = $scenario;
		$model->attributes = $attributes;
		$model->save();
		return $model;
	}

	/**
	 * @inheritdoc
	 * @return \common\db\BaseActiveQuery
	 */
	public static function find()
	{
		return \Yii::createObject(BaseActiveQuery::className(), [get_called_class()]);
	}

	/**
	 * 获取第一个错误
	 * @return string
	 */
	public function getError()
	{
		if ($errors = current($this->getErrors()))
		{
			return $errors[0];
		}
		return null;
	}
}
