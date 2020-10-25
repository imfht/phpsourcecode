<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 0:15
 */
require_once('Functional/Core.php');

class Delete extends Core
{
	// TODO 加入删除开始序号
	public function start($loopTimes = 1, $deleteAll = 0)
	{
		//循环执行次数
		for ($i = 0, $failTimes = 0; $i < $loopTimes || $deleteAll;) {
			$content = $this->getContent();
			if (!$content) {
				if ($failTimes < 10 || $deleteAll) {
					$failTimes++;
					echo "获取页面失败，正在重试" . $failTimes . "...  " . PHP_EOL;
					sleep(10);
					continue;
				} else {
					die('获取微博内容失败，请访问用浏览器访问一下微博页面并刷新，重新设置cookie，或您的IP已被禁用');
				}
			}
			$failTimes = 0;
			$WeiboIds = $this->getAllWeiboId($content);
			if (empty($WeiboIds)) {
				die('已经没有微博了或获取微博失败，请重试');
				//echo '已经没有微博了或获取微博失败，请重试';
			}
			foreach ($WeiboIds as $wid) {
				$de = $this->delWeiboById($wid);
				if ($de) {
					echo '删除id为：' . $wid . '的微博' . PHP_EOL;
				} else {
					echo '删除id为：' . $wid . '的微博失败' . PHP_EOL;
				}
				sleep(1);
			}
			$i++;
			//防止操作太快
			sleep(10);
		}

	}

	/**
	 * 正则获取页面上所有微博的ID
	 */
	protected function getAllWeiboId($content)
	{
		$regx = '/,\"idstr\"\:\"([^\"]*)/';
		preg_match_all($regx, $content, $matchs);
		return $matchs[1];
	}

	/**
	 * 获取个人微博页面内容
	 */
	protected function getContent()
	{
		return $this->load->curl->request('GET', $this->load->config->get('self_page_url'), $this->load->config->get('sina_phone_header'));
	}

	/**
	 * 删除微博接口
	 * @param array $WeiboIds 正则获取到的微博id
	 */
	public function delWeiboById($WeiboId = '')
	{
		$result = $this->load->curl->request('POST', $this->load->config->get('del_weibo_api'), $this->load->config->get('sina_phone_delete_header'), array('id' => (string)$WeiboId));
		return $result;
	}
}

$delete = new Delete();
$delete->start(10, 1);