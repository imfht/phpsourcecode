<?php

namespace common\helpers;

use Aliyun\OSS\OSSClient;
use Aliyun\OSS\Models\PutObjectResult;

/**
 * AliOSS 服务 辅助类
 * 
 * Endpoint: (http://bbs.aliyun.com/read/149100.html?spm=5176.7189909.0.0.fCKFbH)
 * 青岛跟杭州节点的OSS内外网地址如下： 
 * 青岛节点外网地址： oss-cn-qingdao.aliyuncs.com  
 * 青岛节点内网地址： oss-cn-qingdao-internal.aliyuncs.com 
 * 杭州节点外网地址： oss-cn-hangzhou.aliyuncs.com 
 * 杭州节点内网地址:  oss-cn-hangzhou-internal.aliyuncs.com  
 * 原地址oss.aliyuncs.com 默认指向杭州节点外网地址。 
 * 原内网地址oss-internal.aliyuncs.com 默认指向杭州节点内网地址
 * 
 * Bucket的命名规范：(http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/bucket.html)
 * 
 * 只能包括小写字母，数字，短横线（-）
 * 必须以小写字母或者数字开头
 * 长度必须在3-63字节之间
 * 
 * 对象id的命名规范：(http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html)
 * 
 * 使用UTF-8编码
 * 长度必须在1-1023字节之间
 * 不能以“/”或者“\”字符开头
 * 不能含有“\r”或者“\n”的换行符
 * 
 * @author xuxh@jiapai.me
 */
class AliOss
{

	/**
	 * 是否为测试环境,在测试环境中,图片对象的操作均会对应到专有的测试目录中
	 * 
	 * @var bool
	 */
	private static $envTest = false;

	/**
	 * Endpoint
	 * 
	 * @var string
	 */
	private $endpoint = '';

	/**
	 * 访问key
	 * 
	 * @var string
	 */
	private $accessKey = '';

	/**
	 * 访问秘钥
	 * 
	 * @var string
	 */
	private $accessSecret = '';

	/**
	 * 默认的bucket
	 * @var string
	 */
	private $defaultBucket = '';

	/**
	 * OSS 客户端对象
	 * 
	 * @var \Aliyun\OSS\OSSClient
	 */
	private $client;

	private function __construct()
	{
		$alioss_config = \Yii::$app->params['alioss'];
		$this->endpoint = $alioss_config['endpoint'];
		$this->accessKey = $alioss_config['accessKey'];
		$this->accessSecret = $alioss_config['accessSecret'];
		$this->defaultBucket = $alioss_config['defaultBucket'];

		$this->client = OSSClient::factory([
					'Endpoint' => $this->endpoint,
					'AccessKeyId' => $this->accessKey,
					'AccessKeySecret' => $this->accessSecret,
		]);
	}

	/**
	 * 获取类的单例对象
	 * 
	 * @param $env_test 是否为测试环境
	 * 
	 * @return \yii\helpers\AliOss
	 */
	static function instance($env_test = false)
	{
		static $self = null;
		if (is_null($self))
			$self = new self();
		self::$envTest = $env_test;
		return $self;
	}

	/**
	 * 得到 OSSClient 实例
	 * 
	 * @return \Aliyun\OSS\OSSClient
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * 向OSS中新增数据内容对象
	 * 
	 * 成功返回OssPutObject
	 * 失败抛出异常
	 * 参数错误则返回false
	 * 
	 * @return \Aliyun\OSS\Models\PutObjectResult
	 * @thorw \Exception 
	 */
	private function putContent($bucket, $obj_id, $data, $meta = null)
	{
		if (empty($data) || !is_string($data) || empty($obj_id))
			return false;
		$args = array(
			'Bucket' => $bucket,
			'Key' => $obj_id,
			'Content' => $data,
		);

		if (!empty($meta) && is_array($meta))
		{
			$args['UserMetadata'] = $meta;
		}

		try
		{
			return $this->client->putObject($args);
		}
		catch (\Exception $ex)
		{
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}
	}

	/**
	 * 向OSS中新增文件对象
	 * 
	 * 成功返回OssPutObject
	 * 失败抛出异常
	 * 参数错误则返回false
	 * 
	 * @return \Aliyun\OSS\Models\PutObjectResult
	 * @thorw \Exception
	 */
	public function putFile($obj_id, $file, $bucket = null, $meta = null)
	{
		$bucket = $bucket ? $bucket : $this->defaultBucket;
		if (is_readable($file) && !empty($obj_id))
		{
			$args = array(
				'Bucket' => $bucket,
				'Key' => $obj_id,
				'Content' => fopen($file, 'r'),
				'ContentLength' => filesize($file),
//				'ContentType' => FileHelper::getMimeType($file)
			);

			if (!empty($meta) && is_array($meta))
			{
				$args['UserMetadata'] = $meta;
			}

			try
			{
				return $this->client->putObject($args);
			}
			catch (\Exception $ex)
			{
				throw new \Exception($ex->getMessage(), $ex->getCode());
			}
		}
		return false;
	}

	/**
	 * 从OSS中删除对象
	 */
	private function deleteObject($bucket, $obj_id)
	{
		if (empty($obj_id))
			return false;
		$args = array(
			'Bucket' => $bucket,
			'Key' => $obj_id,
		);
		try
		{
			$this->client->deleteObject($args);
		}
		catch (\Exception $ex)
		{
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}
		return true;
	}

	/**
	 * 删除图片对象
	 * 
	 * 删除失败会抛出异常
	 * 
	 * @param string $obj_id 图片对象id
	 * 
	 * @return bool
	 * @thorw \Exception
	 */
	public function deleteImage($obj_id, $bucket = null)
	{
		$bucket = $bucket ? $bucket : $this->defaultBucket;
		return $this->deleteObject($bucket, $obj_id);
	}

	private function getImageIds($prefix = '', $maxKeys = 100, $bucket = null)
	{
		$bucket = $bucket ? $bucket : $this->defaultBucket;
		$result = array();

		$args = array(
			'Bucket' => $bucket,
		);

		if (!empty($prefix))
		{
			$args['Prefix'] = $prefix;
		}

		$maxKeys = intval($maxKeys);
		if ($maxKeys < 100)
			$maxKeys = 100;
		$args['MaxKeys'] = $maxKeys;

		try
		{
			$objectListing = $this->client->listObjects($args);

			foreach ($objectListing->getObjectSummarys() as $objectSummary)
			{
				$result[] = $objectSummary->getKey();
			}
		}
		catch (\Exception $ex)
		{
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}

		return $result;
	}

	/**
	 * 获取oss链接
	 * @param type $url 原url
	 * @param type $width 宽度
	 * @param type $height 高度
	 * @param type $rotate 角度
	 */
	public static function getUrl($url, $width, $height, $rotate)
	{
		if ($height == 0 && in_array($rotate, [90, 270]))
		{
			$url = "{$url}@{$width}h_1c_1e_{$rotate}r.jpg";
			return $url;
		}
		elseif ($height == 0)
		{
			$url = "{$url}@{$width}w_1c_1e.jpg";
			return $url;
		}
		elseif (in_array($rotate, [90, 270]))
		{
			list($width, $height) = [$height, $width];
		}
		$url = "{$url}@{$width}w_{$height}h_1c_1e_{$rotate}r.jpg";

		return $url;
	}

}
