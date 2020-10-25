<?php

class GitWebhook
{

	/**
	 * 支持的平台 - 自建
	 */
	const REPO_PLATFORM_SELF = 3;

	/**
	 * 支持的平台 - 码云
	 */
	const REPO_PLATFORM_GITEE = 1;
	
	/**
	 * 支持的平台 - github
	 */
	const REPO_PLATFORM_GITHUB = 2;

	/**
	 * 启用
	 */
	const XSTATUS_ENABLE = 1;

	/**
	 * 禁用
	 */
	const XSTATUS_DISABLE = 0;

	/**
	 * 执行状态 - 未开始
	 */
	const DO_STATUS_NO = 0;
	
	/**
	 * 执行状态 - 执行中
	 */
	const DO_STATUS_START = 1;
	
	/**
	 * 执行状态 - 结束
	 */
	const DO_STATUS_END = 2;
	
	/**
	 * 执行状态 - 失败
	 */
	const DO_STATUS_FAILED = 3;

	/**
	 * 执行状态 - 忽略
	 *
	 * 同一分支仅执行最新提交记录的pull请求
	 * 并对其之前未执行的 hookrecod 做忽略处理
	 */
	const DO_STATUS_IGNORE = 4;

	/**
	 * 执行状态 - 无效
	 *
	 * 未通过 检测匹配的 hookrecod 记录
	 */
	const DO_STATUS_INVALID = 5;

	/**
	 * webhook 类型 - 未知
	 */
	const WEBHOOK_TYPE_NO = 0;
	
	/**
	 * webhook 类型 - push
	 */
	const WEBHOOK_TYPE_PUSH = 1;
	
	/**
	 * webhook 类型 - tagpush
	 */
	const WEBHOOK_TYPE_TAGPUSH = 2;
	
	/**
	 * webhook 类型 - issue
	 */
	const WEBHOOK_TYPE_ISSUE = 3;
	
	/**
	 * webhook 类型 - pullRequest
	 */
	const WEBHOOK_TYPE_PULLREQUEST = 4;
	
	/**
	 * webhook 类型 - comment
	 */
	const WEBHOOK_TYPE_COMMENT = 5;

	/**
	 * 部署模式 - 本地
	 */
	const DEPLOY_MODE_LOCAL = 1;
	
	/**
	 * 部署模式 - 远程
	 */
	const DEPLOY_MODE_REMOTE = 2;

	static function deployModeText($mode)
	{
		$text = '';
		switch (intval($mode)){
			case self::DEPLOY_MODE_LOCAL:
				$text = 'localhost';
				break;
			case self::DEPLOY_MODE_REMOTE:
				$text = 'remote';
				break;
			default:
				$text = "localhost";
				break;
		}
		return $text;
	}

	static function repoPlatformText($platform)
	{
		$text = '';
		switch (intval($platform)){
			case self::REPO_PLATFORM_GITHUB:
				$text = 'Github';
				break;
			case self::REPO_PLATFORM_GITEE:
				$text = '码云';
				break;
			case self::REPO_PLATFORM_SELF:
				$text = '自建';
				break;
		}
		return $text;
	}


	static function xstatusText($t)
	{
		$text = '';
		switch (intval($t)){
			case self::XSTATUS_DISABLE:
				$text = '禁用';
				break;
			case self::XSTATUS_ENABLE:
				$text = '启用';
				break;
		}
		return $text;
	}


	static function dostatusText($t)
	{
		$text = '';
		switch (intval($t)){
			case self::DO_STATUS_NO:
				$text = '待执行';
				break;
			case self::DO_STATUS_START:
				$text = '执行中';
				break;
			case self::DO_STATUS_END:
				$text = '已完成';
				break;
			case self::DO_STATUS_FAILED:
				$text = '执行失败';
				break;
			case self::DO_STATUS_IGNORE:
				$text = '忽略执行';
				break;
			case self::DO_STATUS_INVALID:
				$text = '无效记录';
				break;
		}
		return $text;
	}


	static function getCommitsInfo($commits_info, $repo_platform)
	{
		$commits = array();
		switch (intval($repo_platform)){
			case GitWebhook::REPO_PLATFORM_GITEE:
				$commits_info = json_decode($commits_info, true);
				if (is_array($commits_info['commits'])){
					foreach ($commits_info['commits'] as $commit){
						$commits[] = array(
							'id'	=> $commit['id'],
							'message'	=> $commit['message'],
							'timestamp'	=> SqlHelper::timestamp( strtotime($commit['timestamp']) ),
							'author'	=> $commit['author']['email']
						);
					}
				}
				break;
			case GitWebhook::REPO_PLATFORM_GITHUB:
				$commits_info = json_decode($commits_info, true);
				if (is_array($commits_info['commits'])){
					foreach ($commits_info['commits'] as $commit){
						$commits[] = array(
							'id'	=> $commit['id'],
							'message'	=> $commit['message'],
							'timestamp'	=> SqlHelper::timestamp( strtotime($commit['timestamp']) ),
							'author'	=> $commit['author']['email']
						);
					}
				}
				break;
			case GitWebhook::REPO_PLATFORM_SELF:
				$commits_info = json_decode($commits_info, true);
				if (is_array($commits_info['commits'])){
					foreach ($commits_info['commits'] as $commit){
						$commits[] = array(
							'id'	=> $commit['id'],
							'message'	=> $commit['message'],
							'timestamp'	=> SqlHelper::timestamp( strtotime($commit['timestamp']) ),
							'author'	=> is_array($commit['author']) ? $commit['author']['email'] : $commit['author']
						);
					}
				}
				break;
		}

		return $commits;
	}


}


class GitPushData
{

	public $data = array();

	/**
	 * @return GitPushData
	 */
	static function parse_str($content)
	{
		if (!is_string($content))
		{
			throw new Exception('无效内容类型');
		}

		$data = json_decode($content, true);
		if (json_last_error() !== JSON_ERROR_NONE)
		{
			throw new Exception(json_last_error_msg());
		}

		$obj = new self;

		$obj->data = $data;

		return $obj;
	}

}

class RConsume
{

	private static $endpoint;
	private static $deploy_ids;

	static function init()
	{
		self::$endpoint = G::$configs['r-consume']['endpoint'];
		self::$deploy_ids = G::$configs['r-consume']['deploy_ids'];
	}

	/**
	 * 数据下发
	 */
	static function dealone($upstatus)
	{
		$url = self::$endpoint . '&q=remote.dealone';

		$rs = self::api('GET', $url, array(
				'deploy_ids' => self::$deploy_ids,
				'upstatus' => (int) $upstatus,
			), array(), 180);

		G::dump($rs, 'api.dealone');

		if (!empty($rs)) {
			$rs = json_decode($rs, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception(json_last_error_msg());
			}

			if ($rs['code'] > 0) {
				throw new Exception($rs['msg']);
			}

			return $rs['data'];
		}
	}

	/**
	 * 结果上报
	 */
	static function report($record_id, $do_status, $do_msg)
	{
		$url = self::$endpoint . '&q=remote.report';
		$rs = self::api('POST', $url, array(
				'record_id'	=> (int) $record_id,
				'do_status'	=> (int) $do_status,
				'do_msg'	=> $do_msg,

			), array(), 180);

		G::dump($rs, 'api.report');
		
		if (!empty($rs)) {
			$rs = json_decode($rs, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception(json_last_error_msg());
			}

			if ($rs['code'] > 0) {
				throw new Exception($rs['msg']);
			}

			return true;
		}

		return false;
	}

    /**
     * 发送请求
     *
     * @param  string $method 请求的方法类型
     * @param  string $url 请求的api
     * @param  array $params 请求参数
     * @param  array $headers 请求头
     * @param  array $headers 超时时间(秒)
     * @return mixed
     */
    private static function api($method, $url, $params = array(), $headers = array(), $timeout = 60)
    {
        Unirest::verifyPeer(false);
        if ($timeout) {
            Unirest::timeout($timeout);// 修正缺省超时时间
        }
        $method = trim(strtolower($method));

        switch ($method) {
            case 'post':
                $response = Unirest::post($url, $headers, $params);
                break;
            default:
                $response = Unirest::get($url, $headers, $params);
                break;
        }

        if (is_object($response)) {
        	return $response->raw_body;
        }

        return null;
    }

}
