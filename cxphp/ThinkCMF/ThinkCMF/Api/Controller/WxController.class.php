<?php

namespace Api\Controller;

use Common\Controller\ApiController;

class WxController extends ApiController {

	public function index() {
		/* 实例微信SDK */
		$weixin = new \Common\Lib\Api\WxChat();

		/* 获取请求信息 */
		$data = $weixin->request();

		/* 获取回复信息 */
		list($content, $type ) = $this->reply($data);

		// 接收到的信息入不同的库
		$this->weichatlog($data);

		/* 响应当前请求 */
		$weixin->response($content, $type);
	}

	public function test() {
		$data = array();
		$data['MsgType'] = empty($_GET['type']) ? 'text' : $_GET['type'];
		$data['Content'] = $_GET['content'];
		$data['Event'] = $_GET['event'];
		return $this->reply($data);
	}

	/**
	 * 定制响应信息
	 * @param array $data 接收的数据
	 * @return array; 响应的数据
	 */
	private function reply($data) {
		// 消息类型
		switch ($data['MsgType']) {
			case 'text': // 类型是文本的
				return $this->getContent($data['Content']);
			case 'event' : // 类型是事件的			              
				// 事件类型
				switch ($data ['Event']) {
					case 'subscribe': // 刚刚关注的
						return array(C('WECHAT_AUTO_REPLY'), 'text');
					default :
						return array(C('WECHAT_AUTO_DEFAULT'), 'text');
				}
				break;
			default :
				return array(C('WECHAT_AUTO_DEFAULT'), 'text');
		}
	}

	/**
	 * 文件文章列表及描述
	 * @param type $content
	 * @return type
	 */
	private function getContent($content) {
		$cache_key = 'WXCONTENT_' . md5($content);
		$string = S($cache_key);
		if (empty($string)) {
			$map = array();
			$map['b.wx_status'] = 1;
			$map['a.status'] = 1;
			$map['b.post_keywords|b.post_title'] = array('like', "%{$content}%");
			$list = M('TermRelationships')
							->alias("a")
							->field('b.*')
							->join(C('DB_PREFIX') . "posts b ON a.object_id = b.ID")
							->where($map)
							->limit(5)
							->order("b.post_date desc")->select();
			if (!empty($list)) {
				$string = "相关的内容：\n";
				foreach ($list as $key => $row) {
					$url = C('site_host') . U('portal/article/index', array('id' => $row['ID']));
					$title = \Org\Util\String::msubstr($row['post_title'], 0, 15, 'utf-8');
					$string.=($key + 1) . ". <a href='{$url}'>{$title}</a>\n";
				}
				S($cache_key, $string, 300);
			} else {
				$string = "抱歉！没有找到关于\"{$content}\"的内容...";
			}
		}
		return array($string, 'text');
	}

	/**
	 * 记录请求信息
	 * @param array $data 接收的数据
	 */
	private function weichatlog($data) {
		if ($data ['MsgType'] == 'event') {
			M('WxEvent')->data($data)->add();
		} else {
			M('WxInfo')->data($data)->add();
		}
	}

}
