<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%releases}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $changes
 * @property integer $dostatus
 * @property integer $client_type
 * @property string $client_version
 * @property string $api_gateway
 * @property string $api_version
 * @property string $api_version_s
 * @property string $download_url
 */
class Release extends \common\models\BaseModel
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%releases}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name', 'changes', 'dostatus', 'client_version', 'api_version', 'download_url', 'api_gateway'], 'required'],
			[['changes'], 'string'],
			[['dostatus', 'client_type'], 'integer'],
			[['name', 'client_version', 'api_version'], 'string', 'max' => 50],
			[['download_url', 'api_gateway'], 'string', 'max' => 255]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => '主健ID',
			'name' => '版本名称',
			'changes' => '版本说明',
			'dostatus' => '应用状态, 1开发中, 2测试中, 3预发布, 4已发布',
			'client_type' => '客户端类型, 1IOS, 2安卓',
			'client_version' => '客户端版本号',
			'api_gateway' => '接口网关',
			'api_version' => '接口版本号',
			'download_url' => '下载地址',
		];
	}

	/**
	 * 根据客户端版本号获取版本发布信息
	 * @param type $client_type 1为ios, 2为安卓
	 * @param type $client_version
	 * @return \self
	 */
	public static function findByClientVersion($client_type, $client_version)
	{
		$release = static::findOne(['client_type' => $client_type, 'client_version' => $client_version]);
		return $release;
	}

	/**
	 * 获取最新版本
	 * @param int $client_type 1为ios, 2为安卓
	 * @return int
	 */
	public static function findLatestVersion($client_type)
	{
		$release = static::find()->where(['client_type' => $client_type, 'dostatus' => 4])->max('client_version');
		return $release;
	}

	/**
	 * 获取最低支持的版本
	 * @param int $client_type 1为ios, 2为安卓
	 * @return int
	 */
	public static function findLowestVersion($client_type)
	{
		$release = static::find()->where(['client_type' => $client_type, 'dostatus' => 4])->min('client_version');
		return $release;
	}

}
