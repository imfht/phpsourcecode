<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Route extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

	/**
     * 更新URL路由 主站单页路由 -> 模块路由 (模块栏目路由 -> 模块单页路由 -> 模块tag -> 模块搜索) -> 会员空间路由
     */
    public function index() {

		$name = $code = $note = '';
		$server = strtolower($_SERVER['SERVER_SOFTWARE']);

		if (strpos($server, 'apache') !== FALSE) {
			$name = 'Apache';
			$note = '<font color=red><b>将以下内容保存为.htaccess文件，放到网站根目录</b></font>';
			$code = 'RewriteEngine On'.PHP_EOL
			.'RewriteBase /'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
			.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ /index.php [NC,L]';
		} elseif (strpos($server, 'iis/7') !== FALSE || strpos($server, 'iis/8') !== FALSE) {
			$name = $server;
			$note = '<font color=red><b>将以下内容保存为Web.config文件，放到网站根目录</b></font>';
			$code = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
			.'<configuration>'.PHP_EOL
			.'    <system.webServer>'.PHP_EOL
			.'        <rewrite>'.PHP_EOL
			.'            <rules>'.PHP_EOL
			.'		<rule name="finecms" stopProcessing="true">'.PHP_EOL
			.'		    <match url="^(.*)$" />'.PHP_EOL
			.'		    <conditions logicalGrouping="MatchAll">'.PHP_EOL
			.'		        <add input="{HTTP_HOST}" pattern="^(.*)$" />'.PHP_EOL
			.'		        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />'.PHP_EOL
			.'		        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />'.PHP_EOL
			.'		    </conditions>'.PHP_EOL
			.'		    <action type="Rewrite" url="index.php" /> '.PHP_EOL
			.'                </rule>'.PHP_EOL
			.'            </rules>'.PHP_EOL
			.'        </rewrite>'.PHP_EOL
			.'    </system.webServer> '.PHP_EOL
			.'</configuration>';
		} elseif (strpos($server, 'iis/6') !== FALSE) {
			$name = $server;
			$note = '建议使用isapi_rewrite第三版,老版本的rewrite不支持RewriteCond语法<br><font color=red><b>将以下内容保存为.htaccess文件，放到网站根目录</b></font>';
			$code = 'RewriteEngine On'.PHP_EOL
			.'RewriteBase /'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
			.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ /index.php';
		} elseif (strpos($server, 'nginx') !== FALSE) {
			$name = $server;
			$note = '<font color=red><b>将以下代码放到Nginx配置文件中去（如果是绑定了域名，所绑定目录也要配置下面的代码），您懂得！</b></font>';
			$code = 'location / { '.PHP_EOL
			.'    if (-f $request_filename) {'.PHP_EOL
			.'           break;'.PHP_EOL
			.'    }'.PHP_EOL
			.'    if ($request_filename ~* "\.(js|ico|gif|jpe?g|bmp|png|css)$") {'.PHP_EOL
			.'        break;'.PHP_EOL
			.'    }'.PHP_EOL
			.'    if (!-e $request_filename) {'.PHP_EOL
			.'        rewrite . /index.php last;'.PHP_EOL
			.'    }'.PHP_EOL
			.'}';
		} else {
			$name = $server;
			$note = '<font color=red><b>当前服务器不提供伪静态规则，请自己将所有页面定向到index.php文件</b></font>';
		}

		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('URL规则') => array('admin/urlrule/index', 'magnet'),
			fc_lang('添加') => array('admin/urlrule/add', 'plus'),
			fc_lang('伪静态规则') => array('admin/route/index', 'safari'),
		)));
		$this->template->assign(array(
			'name' => $name,
			'code' => $code,
			'note' => $note,
			'count' => $code ? count(explode(PHP_EOL, $code)) : 0,
		));
		$this->template->display('route_index.html');
	}

	/**
     * 生成路由临时文件
     */
    public function todo() {

		$code = array();
	    $module = $this->get_cache('module');
		$urlrule = $this->get_cache('urlrule');

		echo '<pre>';

		// 会员部分
		$value = $this->get_cache('member', 'rule');
		if ($value) {
			$code[] = array(
				'name' => '会员部分URL规则',
				'preg' => '开始',
				'rule' => ''
			);
			if ($value['member']) {
				list($preg, $rname) = $this->_rule_preg_value($value['member']);
				$write = 'member/home/index';
				$code[] = array(
					'name' => '会员中心',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['member_reg']) {
				list($preg, $rname) = $this->_rule_preg_value($value['member_reg']);
				$write = 'member/register/index';
				$code[] = array(
					'name' => '会员注册',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['member_login']) {
				list($preg, $rname) = $this->_rule_preg_value($value['member_login']);
				$write = 'member/login/index';
				$code[] = array(
					'name' => '会员登录',
					'preg' => $preg,
					'rule' => $write
				);
			}
			$code[] = array(
				'name' => '会员部分URL规则',
				'preg' => '结束',
				'rule' => ''
			);
		}

		// 空间黄页
		$value = $this->get_cache('member', 'setting', 'space', 'rule');
		if ($value) {
			$code[] = array(
				'name' => '空间黄页URL规则',
				'preg' => '开始',
				'rule' => ''
			);
			if ($value['space']) {
				list($preg, $rname) = $this->_rule_preg_value($value['space']);
				$write = 'space/home/index';
				$code[] = array(
					'name' => '黄页首页',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['space_search_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['space_search_page']);
				$write = 'space/search/index/rewrite/$'.$rname['{param}'];
				$code[] = array(
					'name' => '频道搜索分页',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['space_search']) {
				list($preg, $rname) = $this->_rule_preg_value($value['space_search']);
				$write = 'space/search/index';
				$code[] = array(
					'name' => '频道搜索',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['uhome']) {
				list($preg, $rname) = $this->_rule_preg_value($value['uhome']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'];
				$code[] = array(
					'name' => '个人空间首页',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ulist_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ulist_page']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/category/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '个人空间栏目列表(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ulist']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ulist']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/category/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '个人空间栏目列表',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ushow_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ushow_page']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/show/id/$'.$rname['{id}'].'/mid/$'.$rname['{mid}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '个人空间内容(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ushow']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ushow']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/show/id/$'.$rname['{id}'].'/mid/$'.$rname['{mid}'];
				$code[] = array(
					'name' => '个人空间内容',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_show']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_show']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/sns/name/show/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博动态详情',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_topic_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_topic_page']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/sns/name/topic/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '微博话题列表(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_topic']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_topic']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/sns/name/topic/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博话题列表',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_page']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/sns/name/$'.$rname['{name}'].'/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '微博动态/粉丝/关注/列表(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns']);
				$write = 'space/home/index/uid/$'.$rname['{uid}'].'/action/sns/name/$'.$rname['{name}'].'/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博动态/粉丝/关注/列表',
					'preg' => $preg,
					'rule' => $write
				);
			}

			if ($value['ulist_domain_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ulist_domain_page']);
				$write = 'space/home/index/uid/0/action/category/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '个人空间栏目列表(自定义域名时分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ulist_domain']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ulist_domain']);
				$write = 'space/home/index/uid/0/action/category/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '个人空间栏目列表(自定义域名时)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ushow_domain_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ushow_domain_page']);
				$write = 'space/home/index/uid/0/action/show/id/$'.$rname['{id}'].'/mid/$'.$rname['{mid}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '个人空间内容(自定义域名时分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['ushow_domain']) {
				list($preg, $rname) = $this->_rule_preg_value($value['ushow_domain']);
				$write = 'space/home/index/uid/0/action/show/id/$'.$rname['{id}'].'/mid/$'.$rname['{mid}'];
				$code[] = array(
					'name' => '个人空间内容(自定义域名时)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_show_domain']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_show_domain']);
				$write = 'space/home/index/uid/0/action/sns/name/show/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博动态详情(自定义域名时)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_topic_domain_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_topic_domain_page']);
				$write = 'space/home/index/uid/0/action/sns/name/topic/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '微博话题列表(自定义域名时)(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_topic_domain']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_topic_domain']);
				$write = 'space/home/index/uid/0/action/sns/name/topic/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博话题列表(自定义域名时)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_domain_page']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_domain_page']);
				$write = 'space/home/index/uid/0/action/sns/name/$'.$rname['{name}'].'/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
				$code[] = array(
					'name' => '微博动态/粉丝/关注/列表(自定义域名时)(分页)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			if ($value['sns_domain']) {
				list($preg, $rname) = $this->_rule_preg_value($value['sns_domain']);
				$write = 'space/home/index/uid/0/action/sns/name/$'.$rname['{name}'].'/id/$'.$rname['{id}'];
				$code[] = array(
					'name' => '微博动态/粉丝/关注/列表(自定义域名时)',
					'preg' => $preg,
					'rule' => $write
				);
			}
			$code[] = array(
				'name' => '空间黄页URL规则',
				'preg' => '结束',
				'rule' => ''
			);
		}


		foreach ($this->site_info as $siteid => $site) {

			// 站点URL
			if ($site['SITE_REWRITE']) {

				$value = $urlrule[intval($site['SITE_REWRITE'])]['value'];
				if ($value) {
					$code[] = array(
						'name' => '站点[' . $siteid . '] 站点URL规则',
						'preg' => '开始',
						'rule' => ''
					);
					if ($value['sitemap']) {
						list($preg, $rname) = $this->_rule_preg_value($value['sitemap']);
						$write = 'sitemap/index';
						$code[] = array(
							'name' => '网站地图',
							'preg' => $preg,
							'rule' => $write
						);
					}
					if ($value['so_search_page']) {
						list($preg, $rname) = $this->_rule_preg_value($value['so_search_page']);
						$write = 'so/index/rewrite/$'.$rname['{param}'];
						$code[] = array(
							'name' => '全模块搜索分页',
							'preg' => $preg,
							'rule' => $write
						);
					}
					if ($value['so_search']) {
						list($preg, $rname) = $this->_rule_preg_value($value['so_search']);
						$write = 'so/index';
						$code[] = array(
							'name' => '全模块搜索',
							'preg' => $preg,
							'rule' => $write
						);
					}
					if ($value['share_search_page']) {
						list($preg, $rname) = $this->_rule_preg_value($value['share_search_page']);
						$write = 'search/index/rewrite/$'.$rname['{param}'];
						$code[] = array(
							'name' => '共享模块搜索分页',
							'preg' => $preg,
							'rule' => $write
						);
					}
					if ($value['share_search']) {
						list($preg, $rname) = $this->_rule_preg_value($value['share_search']);
						$write = 'search/index';
						$code[] = array(
							'name' => '共享模块搜索',
							'preg' => $preg,
							'rule' => $write
						);
					}
					$code[] = array(
						'name' => '站点[' . $siteid . '] 站点URL规则',
						'preg' => '结束',
						'rule' => ''
					);
				}
			}

			// 网站单页
			$code[] = array(
				'name' => '站点[' . $siteid . '] 网站单页规则',
				'preg' => '开始',
				'rule' => ''
			);
			$data = $this->get_cache('page-'.$siteid, 'data', 'index');
			if ($data) {
				$public = array();
				foreach ($data as $t) {
					$id = intval($t['urlrule']);
					$value = $urlrule[$id]['value'];
					if (!$value || $t['linkurl']) {
						continue;
					}
					if ($value['page_page']) {
						// 网站单页(分页)
						$rule = str_replace('{modname}', $t['dirname'], $value['page_page']);
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (isset($rname['{dirname}'])) {
							// 目录格式
							$write = 'page/index/dir/$'.$rname['{dirname}'].'/page/$'.$rname['{page}'];
						} elseif (isset($rname['{pdirname}'])) {
							// 层次目录格式
							$write = 'page/index/dir/$'.$rname['{pdirname}'].'/page/$'.$rname['{page}'];
						} else {
							// id模式
							$write = 'page/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
						}
						$code[] = array(
							'name' => '网站单页['.$t['name'].'](分页)',
							'preg' => $preg,
							'rule' => $write
						);
					}
					if ($value['page']) {
						// 网站单页
						$rule = str_replace('{modname}', $t['dirname'], $value['page']);
						list($preg, $rname) = $this->_rule_preg_value($rule);
						if (isset($rname['{dirname}'])) {
							// 目录格式
							$write = 'page/index/dir/$'.$rname['{dirname}'];
						} elseif (isset($rname['{pdirname}'])) {
							// 层次目录格式
							$write = 'page/index/dir/$'.$rname['{pdirname}'];
						} else {
							// id模式
							$write = 'page/index/id/$'.$rname['{id}'];
						}
						$code[] = array(
							'name' => '网站单页['.$t['name'].']',
							'preg' => $preg,
							'rule' => $write
						);
					}
				}
			}
			$code[] = array(
				'name' => '站点[' . $siteid . '] 网站单页规则',
				'preg' => '结束',
				'rule' => ''
			);

			// 共享栏目
			$m = $this->get_cache('module-'.$siteid.'-share');
			if ($m['category']) {
				foreach ($m['category'] as $t) {
					$dir = '';
					$value = $urlrule[intval($t['setting']['urlrule'])]['value'];
					if ($t['tid'] != 2 && $value && !$t['setting']['html']) {
						$code[] = array(
							'name' => '站点['.$siteid.'] 共享栏目['.$t['name'].' '.$t['dirname'].']',
							'preg' => '开始',
							'rule' => ''
						);
						if ($value['list_page']) {
							// 模块栏目列表(分页)
							$rule = str_replace('{modname}', $dir, $value['list_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							if (isset($rname['{dirname}'])) {
								// 目录格式
								$write = 'category/index/dir/$'.$rname['{dirname}'].'/page/$'.$rname['{page}'];
							} elseif (isset($rname['{pdirname}'])) {
								// 层次目录格式
								$write = 'category/index/dir/$'.$rname['{pdirname}'].'/page/$'.$rname['{page}'];
							} else {
								// id模式
								$write = 'category/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
							}
							$code[] = array(
								'name' => '栏目列表(分页)',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['list']) {
							// 模块栏目列表
							$rule = str_replace('{modname}', $dir, $value['list']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							if (isset($rname['{dirname}'])) {
								// 目录格式
								$write = 'category/index/dir/$'.$rname['{dirname}'];
							} elseif (isset($rname['{pdirname}'])) {
								// 层次目录格式
								$write = 'category/index/dir/$'.$rname['{pdirname}'];
							} else {
								// id模式
								$write = 'category/index/id/$'.$rname['{id}'];
							}
							$code[] = array(
								'name' => '栏目列表',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['show_page']) {
							// 模块内容页(分页)
							$rule = str_replace('{modname}', $dir, $value['show_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'show/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
							$code[] = array(
								'name' => '内容页(分页)',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['show']) {
							// 模块内容页
							$rule = str_replace('{modname}', $dir, $value['show']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'show/index/id/$'.$rname['{id}'];
							$code[] = array(
								'name' => '内容页',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['extend_page']) {
							// 模块扩展页(分页)
							$rule = str_replace('{modname}', $dir, $value['extend_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'extend/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
							$code[] = array(
								'name' => '扩展页(分页)',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['extend']) {
							// 模块扩展页
							$rule = str_replace('{modname}', $dir, $value['extend']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'extend/index/id/$'.$rname['{id}'];
							$code[] = array(
								'name' => '扩展页',
								'preg' => $preg,
								'rule' => $write
							);
						}
						$code[] = array(
							'name' => '站点['.$siteid.'] 共享栏目['.$t['name'].' '.$t['dirname'].']',
							'preg' => '结束',
							'rule' => ''
						);
					}
				}
			}

			$code[] = array(
				'name' => '站点['.$siteid.'] 共享栏目',
				'preg' => '全部结束',
				'rule' => ''
			);

			// 模块
			if (isset($module[$siteid]) && $module[$siteid]) {
				foreach ($module[$siteid] as $dir) {
				    $m = $this->get_cache('module-'.$siteid.'-'.$dir);
                    if (!$m) {
						continue;
					}

					// 网站单页
					$code[] = array(
						'name' => '站点[' . $siteid . '] 模块['.$dir.']单页规则',
						'preg' => '开始',
						'rule' => ''
					);
					$data = $this->get_cache('page-'.$siteid, 'data', $dir);
					if ($data) {
						foreach ($data as $t) {
							$value = $urlrule[intval($t['urlrule'])]['value'];
							if (!$value || $t['linkurl']) {
								continue;
							}
							if ($value['page_page']) {
								// 网站单页(分页)
								$rule = str_replace('{modname}', $t['dirname'], $value['page_page']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								if (isset($rname['{dirname}'])) {
									// 目录格式
									$write = $dir.'/page/index/dir/$'.$rname['{dirname}'].'/page/$'.$rname['{page}'];
								} elseif (isset($rname['{pdirname}'])) {
									// 层次目录格式
									$write = $dir.'/page/index/dir/$'.$rname['{pdirname}'].'/page/$'.$rname['{page}'];
								} else {
									// id模式
									$write = $dir.'/page/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
								}
								$code[] = array(
									'name' => '模块单页['.$t['name'].'](分页)',
									'preg' => $preg,
									'rule' => $write
								);
							}
							if ($value['page']) {
								// 网站单页
								$rule = str_replace('{modname}', $t['dirname'], $value['page']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								if (isset($rname['{dirname}'])) {
									// 目录格式
									$write = $dir.'/page/index/dir/$'.$rname['{dirname}'];
								} elseif (isset($rname['{pdirname}'])) {
									// 层次目录格式
									$write = $dir.'/page/index/dir/$'.$rname['{pdirname}'];
								} else {
									// id模式
									$write = $dir.'/page/index/id/$'.$rname['{id}'];
								}
								$code[] = array(
									'name' => '模块单页['.$t['name'].']',
									'preg' => $preg,
									'rule' => $write
								);
							}
						}
					}
					$code[] = array(
						'name' => '站点[' . $siteid . '] 模块['.$dir.']单页规则',
						'preg' => '结束',
						'rule' => ''
					);

					$value = $urlrule[intval($m['site'][$siteid]['urlrule'])]['value'];
					if ($value) {
						$code[] = array(
							'name' => '站点['.$siteid.'] 模块['.$m['name'].' '.$m['dirname'].']',
							'preg' => '开始',
							'rule' => ''
						);
						if ($m['share']) {
							// 共享模块
						} else {
							// 独立模块
							if ($value['module']) {
								// 模块首页
								$rule = str_replace('{modname}', $dir, $value['module']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								$code[] = array(
									'name' => $m['name'].'['.$dir.']模块首页',
									'preg' => $preg,
									'rule' => $dir.'/home/index/'
								);
							}
							if ($value['list_page']) {
								// 模块栏目列表(分页)
								$rule = str_replace('{modname}', $dir, $value['list_page']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								if (isset($rname['{dirname}'])) {
									// 目录格式
									$write = 'category/index/dir/$'.$rname['{dirname}'].'/page/$'.$rname['{page}'];
								} elseif (isset($rname['{pdirname}'])) {
									// 层次目录格式
									$write = 'category/index/dir/$'.$rname['{pdirname}'].'/page/$'.$rname['{page}'];
								} else {
									// id模式
									$write = 'category/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
								}
								$code[] = array(
									'name' => $m['name'].'['.$dir.']栏目列表(分页)',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
							if ($value['list']) {
								// 模块栏目列表
								$rule = str_replace('{modname}', $dir, $value['list']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								if (isset($rname['{dirname}'])) {
									// 目录格式
									$write = 'category/index/dir/$'.$rname['{dirname}'];
								} elseif (isset($rname['{pdirname}'])) {
									// 层次目录格式
									$write = 'category/index/dir/$'.$rname['{pdirname}'];
								} else {
									// id模式
									$write = 'category/index/id/$'.$rname['{id}'];
								}
								$code[] = array(
									'name' => $m['name'].'['.$dir.']栏目列表',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
							if ($value['show_page']) {
								// 模块内容页(分页)
								$rule = str_replace('{modname}', $dir, $value['show_page']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								$write = 'show/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
								$code[] = array(
									'name' => $m['name'].'['.$dir.']模块内容页(分页)',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
							if ($value['show']) {
								// 模块内容页
								$rule = str_replace('{modname}', $dir, $value['show']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								$write = 'show/index/id/$'.$rname['{id}'];
								$code[] = array(
									'name' => $m['name'].'['.$dir.']内容页',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
							if ($value['extend_page']) {
								// 模块扩展页(分页)
								$rule = str_replace('{modname}', $dir, $value['extend_page']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								$write = 'extend/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
								$code[] = array(
									'name' => $m['name'].'['.$dir.']模块扩展页(分页)',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
							if ($value['extend']) {
								// 模块扩展页
								$rule = str_replace('{modname}', $dir, $value['extend']);
								list($preg, $rname) = $this->_rule_preg_value($rule);
								$write = 'extend/index/id/$'.$rname['{id}'];
								$code[] = array(
									'name' => $m['name'].'['.$dir.']扩展页',
									'preg' => $preg,
									'rule' => $dir.'/'.$write
								);
							}
						}

						if ($value['search_page']) {
							// 模块搜索页(分页)
							$rule = str_replace('{modname}', $dir, $value['search_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'search/index/rewrite/$'.$rname['{param}'];
							$code[] = array(
								'name' => $m['name'].'['.$dir.']搜索页(分页)',
								'preg' => $preg,
								'rule' => $dir.'/'.$write
							);
						}
						if ($value['search']) {
							// 模块搜索页
							$rule = str_replace('{modname}', $dir, $value['search']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'search/index';
							$code[] = array(
								'name' => $m['name'].'['.$dir.']搜索页',
								'preg' => $preg,
								'rule' => $dir.'/'.$write
							);
						}
						if ($value['tag_page']) {
							// 模块TAG页(分页)
							$rule = str_replace('{modname}', $dir, $value['tag_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'tag/index/name/$'.$rname['{tag}'].'/page/$'.$rname['{page}'];
							$code[] = array(
								'name' => $m['name'].'['.$dir.']TAG页(分页)',
								'preg' => $preg,
								'rule' => $dir.'/'.$write
							);
						}
						if ($value['tag']) {
							// 模块TAG页
							$rule = str_replace('{modname}', $dir, $value['tag']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'tag/index/name/$'.$rname['{tag}'];
							$code[] = array(
								'name' => $m['name'].'['.$dir.']TAG页',
								'preg' => $preg,
								'rule' => $dir.'/'.$write
							);
						}
						$code[] = array(
							'name' => '站点['.$siteid.'] 模块['.$m['name'].' '.$m['dirname'].']',
							'preg' => '结束',
							'rule' => ''
						);
					}

				}
			}

		}

		if (!$code) {
			exit('没有设置伪静态');
		}

		$html = '';
		foreach ($code as $t) {
			if ($t['rule']) {
				$html.= '	// '.$t['name'].PHP_EOL;
				$html.= '	"'.$t['preg'].'"'.$this->_space($t['preg']).'=>	"'.$t['rule'].'",'.PHP_EOL;
			} else {
				$html.= PHP_EOL.'	/*-------------------'.$t['name'].' '.$t['preg'].'-----------------*/ '.PHP_EOL.PHP_EOL;
			}
		}

		echo '<textarea class="form-control" style="height:'.(count(explode(PHP_EOL, $html)) * 10).'px">'.$html.'</textarea>';
		exit;

		// 生成规则到配置文件中
		if ($route) {

            $public = $_note = array();
			foreach ($route as $name => $data) {
				if ($name == 'page') {
					$string.= "<tr><td align=\"left\"><font color=blue>单页路由生成完毕</font></td></tr>";
					foreach ($data as $rule => $t) {
						list($preg, $value) = $this->_rule_preg_value($rule);
						if (!$preg || !$value) {
							$string.= "<tr><td align=\"left\"><font color=red>单页URL（{$rule}）格式不正确</font></td></tr>";
						} elseif (isset($value['{dirname}'])) { // 目录格式
							if (isset($value['{page}'])) {
								// 分页规则
                                $public[$preg] = 'page/index/dir/$'.$value['{dirname}'].'/page/$'.$value['{page}'];
							} else {
                                $public[$preg] = 'page/index/dir/$'.$value['{dirname}'];
							}
						} elseif (isset($value['{pdirname}'])) { // 层次目录格式
							$dir = $value['{pdirname}'];
							if (isset($value['{page}'])) {
								// 分页规则
                                $public[$preg] = 'page/index/dir/$'.$dir.'/page/$'.$value['{page}'];
							} else {
                                $public[$preg] = 'page/index/dir/$'.$dir;
							}
						} else { // id模式
							if (isset($value['{page}'])) {
								// 分页规则
                                $public[$preg] = 'page/index/id/$'.$value['{id}'].'/page/$'.$value['{page}'];
							} else {
                                $public[$preg] = 'page/index/id/$'.$value['{id}'];
							}
						}
						$_note[$preg] = $rule;
					}
				} elseif ($name == 'member') {
					$string.= "<tr><td align=\"left\"><font color=blue>会员路由生成完毕</font></td></tr>";
				} else {
					// 模块规则需要判断是否有冲突
					$module = array();
					if ($data['list']) {
						foreach ($data['list'] as $rule => $t) {
                            $write = '';
							list($preg, $value) = $this->_rule_preg_value($rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】列表URL（{$rule}）格式不正确</font></td></tr>";
							} elseif (isset($value['{dirname}'])) { // 目录格式
								if (isset($value['{page}'])) {
									// 分页规则
                                    $write = 'category/index/dir/$'.$value['{dirname}'].'/page/$'.$value['{page}'];
								} else {
                                    $write = 'category/index/dir/$'.$value['{dirname}'];
								}
                                // 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                }
                                $module[$preg] = $write;
							} elseif (isset($value['{pdirname}'])) { // 层次目录格式
								$dir = $value['{pdirname}'];
								if (isset($value['{page}'])) {
									// 分页规则
									$write = 'category/index/dir/$'.$dir.'/page/$'.$value['{page}'];
								} else {
									$write = 'category/index/dir/$'.$dir;
								}
                                // 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                }
                                $module[$preg] = $write;
							} else { // id模式
								if (isset($value['{page}'])) {
									// 分页规则
									$write = 'category/index/id/$'.$value['{id}'].'/page/$'.$value['{page}'];
								} else {
									$write = 'category/index/id/$'.$value['{id}'];
								}
								// 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                }
                                $module[$preg] = $write;
							}

							$_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】栏目路由生成完毕</font></td></tr>";
					}

					if ($data['show']) {
						foreach ($data['show'] as $rule => $t) {
                            $write = '';
							list($preg, $value) = $this->_rule_preg_value($rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】内容URL（{$rule}）格式不正确</font></td></tr>";
							} else {
								if (isset($value['{page}'])) {
									// 分页规则
									if (isset($module[$preg])) {
										$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】内容分页规则（{$rule}）与其他规则有冲突</font></td></tr>";
									} else {
                                        $write = 'show/index/id/$'.$value['{id}'].'/page/$'.$value['{page}'];
									}
								} else {
									if (isset($module[$preg])) {
										$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】内容URL规则（{$rule}）与其他规则有冲突</font></td></tr>";
									} else {
                                        $write = 'show/index/id/$'.$value['{id}'];
									}
								}
                                // 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                }
                                $module[$preg] = $write;
							}
							$_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】内容路由生成完毕</font></td></tr>";
					}

					if ($data['extend']) {
						foreach ($data['extend'] as $rule => $t) {
                            $write = '';
							list($preg, $value) = $this->_rule_preg_value($rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】内容扩展URL格式（{$rule}）不正确</font></td></tr>";
							} else {
								if (isset($module[$preg])) {
									$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】内容扩展URL规则（{$rule}）与其他规则有冲突</font></td></tr>";
								} else {
									$write = 'extend/index/id/$'.$value['{id}'];
								}
							}
                            // 判断是否指向根目录
                            if (strpos($rule, '/') === 0) {
                                $public[$preg] = $name.'/'.$write;
                            }
                            $module[$preg] = $write;
							$_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】内容扩展路由生成完毕</font></td></tr>";
					}

					if ($data['tag']) {
						foreach ($data['tag'] as $rule => $t) {
                            $write = '';
							list($preg, $value) = $this->_rule_preg_value($rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】TAG URL格式（{$rule}）不正确</font></td></tr>";
							} else {
								if (isset($value['{page}'])) {
									// 分页规则
									if (isset($module[$preg])) {
										$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】TAG分页规则（{$rule}）与其他规则有冲突</font></td></tr>";
									} else {
										$write = 'tag/index/name/$'.$value['{tag}'].'/page/$'.$value['{page}'];
									}
								} else {
									if (isset($module[$preg])) {
										$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】TAG URL规则（{$rule}）与其他规则有冲突</font></td></tr>";
									} else {
										$write = 'tag/index/name/$'.$value['{tag}'];
									}
								}
							}
                            // 判断是否指向根目录
                            if (strpos($rule, '/') === 0) {
                                $public[$preg] = $name.'/'.$write;
                            }
                            $module[$preg] = $write;
							$_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】Tag路由生成完毕</font></td></tr>";
					}

					if ($data['search']) {
						foreach ($data['search'] as $rule => $t) {
                            $write = '';
							$_rule = str_replace('{id}', '{kw}', $rule);
							list($preg, $value) = $this->_rule_preg_value($_rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】搜索URL格式（{$rule}）不正确</font></td></tr>";
							} else {
								if (isset($module[$preg])) {
									$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】搜索分页规则（{$rule}）与其他规则有冲突</font></td></tr>";
								} else {
									$write = 'search/index/id/$'.$value['{kw}'].'/page/$'.$value['{page}'];
								}
							}
                            // 判断是否指向根目录
                            if (strpos($_rule, '/') === 0) {
                                $public[$preg] = $name.'/'.$write;
                            }
                            $module[$preg] = $write;
							$_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】搜索路由生成完毕</font></td></tr>";
					}

					if ($data['page']) {
						foreach ($data['page'] as $rule => $t) {
                            $write = '';
							list($preg, $value) = $this->_rule_preg_value($rule);
							if (!$preg || !$value) {
								$string.= "<tr><td align=\"left\"><font color=red>模块【{$name}】单页URL（{$rule}）格式不正确</font></td></tr>";
							} elseif (isset($value['{dirname}'])) { // 目录格式
								if (isset($value['{page}'])) {
									// 分页规则
									$write = 'page/index/dir/$'.$value['{dirname}'].'/page/$'.$value['{page}'];
								} else {
                                    $write = 'page/index/dir/$'.$value['{dirname}'];
								}
								// 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                } else {
                                    $module[$preg] = $write;
                                }
							} elseif (isset($value['{pdirname}'])) { // 层次目录格式
								$dir = $value['{pdirname}'];
								if (isset($value['{page}'])) {
									// 分页规则
                                    $write = 'page/index/dir/$'.$dir.'/page/$'.$value['{page}'];
								} else {
                                    $write = 'page/index/dir/$'.$dir;
								}
                                // 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                } else {
                                    $module[$preg] = $write;
                                }
							} else { // id模式
								if (isset($value['{page}'])) {
									// 分页规则
                                    $write = 'page/index/id/$'.$value['{id}'].'/page/$'.$value['{page}'];
								} else {
                                    $write = 'page/index/id/$'.$value['{id}'];
								}
                                // 判断是否指向根目录
                                if (strpos($rule, '/') === 0) {
                                    $public[$preg] = $name.'/'.$write;
                                }
                                $module[$preg] = $write;
							}

                            $_note[$preg] = $rule;
						}
						$string.= "<tr><td align=\"left\"><font color=blue>模块【{$name}】单页路由生成完毕</font></td></tr>";
					}

					// 生成文件到对应的模块目录
					$this->_to_file($name, $module, $_note);
				}
			}

            // 生成文件到主站点
            $this->_to_file('', $public, $_note);
			$string.= "<tr><td align=\"left\"><font color=red>规则生成完毕，主站及各个模块下的/config/rewrite.php即是规则文件，如果发生指向错误可以在此文件中排查</font></td></tr>";
		} else {
			$string.= "<tr><td align=\"left\"><font color=red>您尚未在整站点中设置URL规则，设置方法：进入某个模块-栏目分类-自定义URL-为栏目指定规则。</font></td></tr>";
		}

		echo $string;
    }

	// 正则解析
	private function _rule_preg_value($rule) {

		$rule = trim(trim($rule, '/'));

		if (preg_match_all('/\{(.*)\}/U', $rule, $match)) {

			$value = array();
			foreach ($match[0] as $k => $v) {
				$value[$v] = ($k + 1);
			}

			$preg = preg_replace(
				array(
					'#\{id\}#U',
					'#\{uid\}#U',
					'#\{mid\}#U',
					'#\{fid\}#U',
					'#\{page\}#U',

					'#\{pdirname\}#Ui',
					'#\{dirname\}#Ui',
					'#\{modname\}#Ui',
					'#\{name\}#Ui',

					'#\{tag\}#U',
					'#\{param\}#U',

					'#\{y\}#U',
					'#\{m\}#U',
					'#\{d\}#U',

					'#\{.+}#U',
					'#/#'
				),
				array(
					'(\d+)',
					'(\d+)',
					'(\d+)',
					'(\w+)',
					'(\d+)',

					'([\w\/]+)',
					'([a-z0-9]+)',
					'([a-z]+)',
					'([a-z]+)',

					'(\w+)',
					'(.+)',

					'(\d+)',
					'(\d+)',
					'(\d+)',

					'(.+)',
					'\/'
				),
				$rule
			);

            // 替换特殊的结果
            $preg = str_replace(
                array('(.+))}-'),
                array('(.+)-'),
                $preg
            );

			return array($preg, $value);
		}

		return array($rule, array());
	}

	// 将规则生成至文件
	private function _to_file($path, $data, $note) {

		$file = $path ? FCPATH.'module/'.$path.'/config/rewrite.php' : WEBPATH.'config/rewrite.php';

		$string = '<?php'.PHP_EOL.PHP_EOL;
		$string.= 'if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'.PHP_EOL.PHP_EOL;
		$string.= '// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。'.PHP_EOL.PHP_EOL;

		if ($data) {

			arsort($data);
            $end = array();
			foreach ($data as $key => $val) {
                if (strpos($key, '(.+)') === 0) {
                    $end[$key] = $val;
                } else {
				    $string.= '$route[\''.$key.'\']'.$this->_space($key).'= \''.$val.'\'; // '.$this->_get_name($val).' 对应规则：'.$note[$key].PHP_EOL;
                }
			}
            if ($end) {
                $string.= PHP_EOL.PHP_EOL.PHP_EOL;
                foreach ($end as $key => $val) {
                    $string.= '$route[\''.$key.'\']'.$this->_space($key).'= \''.$val.'\'; // '.$this->_get_name($val).' 对应规则：'.$note[$key].PHP_EOL;
                }
            }
		}

		file_put_contents($file, $string);
	}

	// 获取页面名称
	private function _get_name($rule) {
		if (strpos($rule, 'show/index') !== FALSE) {
			return '【内容页】';
		} elseif (strpos($rule, 'category/index') !== FALSE) {
			return '【栏目页】';
		} elseif (strpos($rule, 'extend/index') !== FALSE) {
			return '【扩展页】';
		} elseif (strpos($rule, 'search/index') !== FALSE) {
			return '【搜索页】';
		} elseif (strpos($rule, 'page/index') !== FALSE) {
			return '【单网页】';
		} elseif (strpos($rule, 'tag/index') !== FALSE) {
			return '【标签页】';
		}
	}

	/**
	 * 补空格
	 *
	 * @param	string	$name	变量名称
	 * @return	string
	 */
	private function _space($name) {
		$len = strlen($name) + 2;
	    $cha = 60 - $len;
	    $str = '';
	    for ($i = 0; $i < $cha; $i ++) $str .= ' ';
	    return $str;
	}
}