<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%admin_historys}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $domain
 * @property string $url
 * @property string $params
 * @property string $controller
 * @property string $action
 * @property string $created_at
 */
class AdminHistory extends \common\models\BaseModel
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%admin_historys}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'type', 'domain', 'url', 'params', 'controller', 'action', 'created_at'], 'required'],
			[['user_id'], 'integer'],
			[['params'], 'string'],
			[['created_at'], 'safe'],
			[['type', 'ip'], 'string', 'max' => 50],
			[['domain'], 'string', 'max' => 200],
			[['url'], 'string', 'max' => 255],
			[['controller', 'action'], 'string', 'max' => 100]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => '自增主键ID',
			'user_id' => '操作管理员',
			'type' => '请求类型',
			'domain' => '域名',
			'url' => '请求url地址',
			'params' => '请求参数',
			'controller' => '控制器',
			'action' => '动作',
			'created_at' => '请求时间',
		];
	}

	/**
	 * 添加操作日志
	 * @param type $user_id
	 * @param type $type
	 * @param type $domain
	 * @param type $url
	 * @param type $params
	 * @param type $controller
	 * @param type $action
	 * @param type $ip
	 */
	public static function addHistory($user_id, $type, $domain, $url, $params, $controller, $action, $ip)
	{
		$attrs = [
			'user_id' => $user_id,
			'type' => $type,
			'domain' => $domain,
			'url' => $url,
			'params' => $params,
			'controller' => $controller,
			'action' => $action,
			'ip' => $ip,
			'created_at' => date('Y-m-d H:i:s'),
		];
		static::create($attrs);
	}

}
