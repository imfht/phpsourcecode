<?php
/**
 * Wechat 类
 */
class Wechat {

	function __construct () {
		# code...
	}

	/**
	 * 一次将一篇或多篇文章以图文列表形式推送到微信订阅号
	 * 文章会按照post_id在微信图文列表中排序，按照id大小从上到下降序排列
	 *
	 * @param Array $post_ids
	 * @return boolean true
	 */
	public function push_to_wechat ( $post_ids ) {
		// 获取对应的token
		$opts = get_option( 'ptw_opts' );
		$token = self::get_mp_token( $opts['mp_app_id'], $opts['mp_app_key'] );

		// 上传图文消息
		$raw = Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='
			. $token, self::json_article( $post_ids, $token ), false );
		$res = json_decode( $raw, true );
		if ( isset( $res['errcode'] ) && $res['errcode'] != 0 ) {
			// 报告错误
			error_log( 'Error: 上传图文至订阅号出错: ' . $raw );
			switch ($res['errcode']) {
				case 40001:
					exit('access_token无效，请联系网站管理员处理');
				default:
					exit($raw);
			}
		}

		// 删除订阅号里的封面图片thumb_media_id
		// $del_thumb = '{"media_id":"' . $thumb_res['media_id'] . '"}';
		// Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=' . $token,
		// 	$del_thumb, false );

		if ($opts['push_type'] == 'upload') {
			// 只上传到素材库，不推送
			return true;
		} elseif ($opts['push_type'] == 'push') {
			// 发送消息至订阅号
			return self::send_to_wechat( $res['media_id'], $token, $opts['wx_wxid'] );
		}
	}

	/**
	 * 获取微信订阅号token
	 * @param string $id CorpID
	 * @param string $secret CorpSecret
	 * @return string AccessToken
	 */
	private function get_mp_token($id, $key) {
		//缓存文件名
		$file = "./access_token.txt";
		if (file_exists($file)) {
			$data = json_decode(file_get_contents($file), true);
		}

		if ($data['expire_time'] < time() or !$data['expire_time']) {
			$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $id
				. '&secret=' . $key;
			$res = Curl::curl_get_json($url);
			$token = $res['access_token'];
			if($token) {
				$data['expire_time']  = time() + $res['expires_in'];
				$data['access_token'] = $token;
				file_put_contents($file, json_encode($data), LOCK_EX);
			}
		} else {
			$token = $data['access_token'];
		}
		return $token;
	}

	/**
	 * 匹配文章内容中img的src值
	 *
	 * @param string $post_content
	 */
	private function preg_match_src($post_content) {
		$p = '/<img.*\ssrc=[\"|\'](.*)[\"|\'].*>/iU';
		$n = preg_match_all($p, $post_content, $m);

		if ($n) {
			return $m[1];
		} else {
			return false;
		}
	}

	/**
	 * 获取文章缩略图的url地址
	 *
	 * @param int $post_id
	 * @return string $thumb_url
	 */
	private function get_thumb_url( $post_id, $thumb_id ) {
		// 如果缩略图存在
		$thumb_post = get_post( $thumb_id );
		if ( !is_null( $thumb_post ) ) {
			// 默认不开启百度云插件，缩略图为posts表中的guid值
			$thumb_url = $thumb_post->guid;

			if ( is_plugin_active( 'wp-bos/wp-bos.php' ) ) {
				// 如果插件开启则取缩略图的bos_info构造url
				$bos_options   = get_option( 'bos_options', true );
				if ( $bos_options ) {
					$bos_info = get_post_meta( $thumb_id, 'bos_info', true );
					if (!empty( $bos_info )) {
						// 如果缩略图有bos_info则说明图片在百度云上
						if ( !empty( $bos_options['domain'] ) ) {
							$thumb_url = $bos_options['domain'] . '/' . $bos_info;
						} elseif ( !empty( $bos_options['host'] ) ) {
							$thumb_url = $bos_options['host'] . '/' . $bos_info;
						}
					}
				}
			}

			return $thumb_url;
		}
	}

	/**
	 * 发送图文消息到微信
	 *
	 * @param string wechat $media_id
	 * @param string wechat $token
	 * @param string wechat $wx_id
	 * @return boolean true || error exit
	 */
	private function send_to_wechat( $media_id, $token, $wx_id ) {
		if ( 'all' == $wx_id ) {
			//群发订阅号
			$postparams = '{
				"filter":{
					"is_to_all":true
				},
				"mpnews":{
					"media_id":"' . $media_id . '"
				},
				"msgtype":"mpnews"
			}';
			$raw = Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='
				. $token, $postparams, false );

			error_log( $raw );
			$res = json_decode( $raw );
			if (isset($res['errcode']) && $res['errcode'] != 0) {
				error_log('Error: 群发消息至订阅号出错 - ' . $raw);
				exit;
			}
		} else {
			//这是一条测试消息
			$postparams = '{
				"towxname":"' . $wx_id . '",
				"mpnews":{
					"media_id":"' . $media_id . '"
				},
				"msgtype":"mpnews"
			}';
			$raw = Curl::curl_post_wx('https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=' . $token,
				$postparams, false);

			error_log($raw);
			$res = json_decode($raw, true);

			if (isset($res['errcode']) && $res['errcode'] != 0) {
				error_log('Error: 发送测试消息至订阅号出错 - ' . $raw);
				exit;
			} elseif ('all' != $wx_id) {
				error_log('发送测试消息至订阅号成功!<br>');
			}
		}
		return true;
	}

	/**
	 * 将文章内容构造成json字符串
	 *
	 * @param array $post_ids
	 * @param string $token
	 * @return string json
	 */
	private function json_article( $post_ids, $token ) {
		$mp_article = '{"articles":[';
		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			$post_urls = self::preg_match_src( $post->post_content );
			if ( $post_urls ) {
				// 如果文章中有图片，将文章中的图片上传至微信素材库
				foreach ( $post_urls as $post_url ) {
					if ( strpos( $post_url, 'qpic.cn' ) === false ) {
						// 图片没有上传到微信服务器
						$res = Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/material/add_material?'
							. 'access_token=' . $token . '&type=image', false, Curl::curl_get_img( $post_url ) );
						//替换文章内容img中的src地址
						$post->post_content = str_replace( $post_url, $res['url'], $post->post_content );
					}
				}
			}

			$thumb_id = get_post_meta( $post_id, '_thumbnail_id', true );
			if ( !empty( $thumb_id ) ) {
				// 如果文章有缩略图则获取文章的缩略图url地址
				$thumb_url = self::get_thumb_url( $post_id, $thumb_id);
				$thumb_res = Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/material/add_material?'
					. 'access_token=' . $token . '&type=image', false, Curl::curl_get_img( $thumb_url ) );
				// 将缩略图插入到文章的头部
				$post->post_content = '<img src="'. $thumb_res['url'] .'">' . $post->post_content;
			} elseif ( $post_urls ) {
				// 如果没有缩略图，将文章中的第一张图片作为封面图片上传至微信服务器获得thumb_media_id
				$thumb_res = Curl::curl_post_wx( 'https://api.weixin.qq.com/cgi-bin/material/add_material?'
					. 'access_token=' . $token . '&type=image', false, Curl::curl_get_img( $post_urls[0] ) );
			}
			unset( $post_urls );

			$post->post_content = preg_replace('/(<li.*>)/iU', '${1}&#8226;&nbsp;', $post->post_content);

			$opts = get_option('ptw_opts');
			// 将公众号的二维码图片插入到文章的尾部
			$post->post_content .=
				'<p style=" margin-bottom: 1em; color: rgb(51, 51, 51); ;  white-space: normal; line-height: 1em; background-color: rgb(255, 255, 255); "></p>
				<p style="text-align: center;"><img data-s="300,640" data-type="png"
				data-src="' . $opts['qr_url'] . '"
				data-ratio="1.2320143884892085" data-w="" src="' . $opts['qr_url'] . '"&amp;tp=webp&amp;wxfrom=5&amp;wx_lazy=1"
				style="width: auto !important; visibility: visible !important; height: auto !important;"><br></p>';
			// 将文章内容中的换行替换为<p>标签
			$post->post_content = wpautop($post->post_content);
			// 下面的正则替换可以根据自己的需要进行修改
			$post->post_content = preg_replace('/(<img .*class=".*aligncenter.*".*style=")/iU', '${1} display:block; margin: 0 auto 1.5em auto;"', $post->post_content);
			$post->post_content = preg_replace('/(<img .*class=".*aligncenter.*")/iU', '${1} style="display:block; margin: 0 auto 1.5em auto;"', $post->post_content);
			$post->post_content = preg_replace('/(<p .*style=")/iU', '${1}margin-bottom:1.4em;', $post->post_content);
			$post->post_content = preg_replace('/<p>/i', '<p style="margin-bottom:1.4em;">', $post->post_content);
			$post->post_content = '<div style="line-height: 2;">' . $post->post_content . '</div>';

			// 如果不替换，html标签不起作用，如果文章中有 " '字符浏览器解析会出错
			$post->post_content = str_replace( '"', '\"', $post->post_content );
			$post->post_content = str_replace( "'", "\'", $post->post_content );

			$mp_article .= '{
				"title": "' . $post->post_title . '",
				"content_source_url": "' . get_permalink( $post_id, false ) . '",
				"thumb_media_id": "' . $thumb_res['media_id'] . '",
				"content": "' . htmlspecialchars_decode( $post->post_content ) . '",
				"show_cover_pic": 0,
			';
			if ( !empty($post->post_excerpt ) ) {
				$mp_article .= '"digest": "' . $post->post_excerpt . '",';
			}
			$mp_article .= '},';
		}
		$article = rtrim($mp_article, ',') . ']}';
		return $article;
	}

}
