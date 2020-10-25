<?php

/**
 * 打印输出数据到文件
 * @param type $data 需要打印的数据
 * @param type $replace 是否要替换打印
 * @param string $pathname 打印输出文件位置
 * @author Anyon Zou <cxphp@qq.com>
 */
function p($data, $replace = false, $pathname = NULL) {
	is_null($pathname) && $pathname = RUNTIME_PATH . date('Ymd') . '_print.txt';
	$model = $replace ? FILE_APPEND : FILE_USE_INCLUDE_PATH;
	if (is_array($data)) {
		file_put_contents($pathname, print_r($data, TRUE), $model);
	} else {
		file_put_contents($pathname, $data, $model);
	}
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 * @author Anyon Zou <cxphp@qq.com>
 */
function hook($hook, $params = array()) {
	\Think\Hook::listen($hook, $params);
}

/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @return String 加密后的字符串
 * @author Anyon Zou <cxphp@qq.com>
 */
function encode($string = '', $skey = 'ThinkCMF') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(base64_encode($string));
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		$key < $strCount && $strArr[$key].=$value;
	}
	return str_replace('=', 'ThinkCMF', join('', $strArr));
}

/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String 解密后的字符串
 * @author Anyon Zou <cxphp@qq.com>
 */
function decode($string = '', $skey = 'ThinkCMF') {
	$skey = str_split(base64_encode($skey));
	$strArr = str_split(str_replace('ThinkCMF', '=', $string), 2);
	$strCount = count($strArr);
	foreach ($skey as $key => $value) {
		if ($key < $strCount && $strArr[$key][1] === $value) {
			$strArr[$key] = $strArr[$key][0];
		} else {
			break;
		}
	}
	return base64_decode(join('', $strArr));
}

/**
 * 检测用户是否登录
 * @return boolean false-未登录, Array-登录
 * @author Anyon Zou <cxphp@qq.com>
 */
function is_login() {
	$user = session('user');
	return empty($user) ? false : $user;
}

/**
 * 快速时间格式生成
 * @param type $time 时间载
 * @param type $format 时间格式
 * @return type 格式化后的时间
 */
function toDate($time = null, $format = 'Y-m-d H:i:s') {
	is_null($time) && $time = time();
	return date($format, $time);
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author Anyon Zou <cxphp@qq.com>
 */
function check_verify($code, $id = 1) {
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 清空缓存
 */
function clear_cache() {
	$dirs = array();
	$noneed_clear = array(".", "..");
	$rootdirs = array_diff(scandir(RUNTIME_PATH), $noneed_clear);
	foreach ($rootdirs as $dir) {
		if ($dir != "." && $dir != "..") {
			$dir = RUNTIME_PATH . $dir;
			if (is_dir($dir)) {
				array_push($dirs, $dir);
				$tmprootdirs = scandir($dir);
				foreach ($tmprootdirs as $tdir) {
					if ($tdir != "." && $tdir != "..") {
						$tdir = $dir . '/' . $tdir;
						if (is_dir($tdir)) {
							array_push($dirs, $tdir);
						}
					}
				}
			}
		}
	}
	$dirtool = new Common\Lib\Util\Dir();
	foreach ($dirs as $dir) {
		$dirtool->del($dir);
	}
}

/**
 * 生成参数列表,以数组形式返回
 */
function sp_param_lable($tag = '') {
	$param = array();
	$array = explode(';', $tag);
	foreach ($array as $v) {
		list($key, $val) = explode(':', trim($v));
		$param[trim($key)] = trim($val);
	}
	return $param;
}

/**
 * 
 */
function get_site_options() {
	$options_obj = new Admin\Model\OptionsModel();
	$option = $options_obj->where("option_name='site_options'")->find();
	if ($option) {
		return (array) json_decode($option['option_value']);
	} else {
		return array();
	}
}

/**
 * 全局获取验证码图片 生成的是个HTML的img标签
 * length=4&size=20&width=238&height=50
 * length:字符长度
 * size:字体大小
 * width:生成图片宽度
 * heigh:生成图片高度
 * @param type $imgparam 图片的属性设置
 * @param type $imgattrs IMG标签
 * @return type
 */
function show_verify_img($imgparam = 'length=4&size=15&width=238&height=50', $imgattrs = 'style="cursor: pointer;" title="点击获取"') {
	$src = U('Api/Index/show_verify', $imgparam);
	return $img = <<<hello
<img onclick='this.src+="?"'  src="$src" $imgattrs/>
hello;
}

/**
 * 10
 * 返回指定id的菜单
 * 同上一类方法，jquery treeview 风格，可伸缩样式
 * @param $myid 表示获得这个ID下的所有子级
 * @param $effected_id 需要生成treeview目录数的id
 * @param $str 末级样式
 * @param $str2 目录级别样式
 * @param $showlevel 直接显示层级数，其余为异步显示，0为全部限制
 * @param $ul_class 内部ul样式 默认空  可增加其他样式如'sub-menu'
 * @param $li_class 内部li样式 默认空  可增加其他样式如'menu-item'
 * @param $style 目录样式 默认 filetree 可增加其他样式如'filetree treeview-famfamfam'
 * @param $dropdown 有子元素时li的class
 * $id="main";
  $effected_id="mainmenu";
  $filetpl="<a href='\$href'><span class='file'>\$label</span></a>";
  $foldertpl="<span class='folder'>\$label</span>";
  $ul_class="" ;
  $li_class="" ;
  $style="filetree";
  $showlevel=6;
  sp_get_menu($id,$effected_id,$filetpl,$foldertpl,$ul_class,$li_class,$style,$showlevel);
 * such as
 * <ul id="example" class="filetree ">
  <li class="hasChildren" id='1'>
  <span class='folder'>test</span>
  <ul>
  <li class="hasChildren" id='4'>
  <span class='folder'>caidan2</span>
  <ul>
  <li class="hasChildren" id='5'>
  <span class='folder'>sss</span>
  <ul>
  <li id='3'><span class='folder'>test2</span></li>
  </ul>
  </li>
  </ul>
  </li>
  </ul>
  </li>
  <li class="hasChildren" id='6'><span class='file'>ss</span></li>
  </ul>
 */
function sp_get_menu($id = "main", $effected_id = "mainmenu", $filetpl = "<span class='file'>\$label</span>", $foldertpl = "<span class='folder'>\$label</span>", $ul_class = "", $li_class = "", $style = "filetree", $showlevel = 6, $dropdown = 'hasChild') {
	$site_nav = F("site_nav_" . $id);
	if (empty($site_nav)) {
		$nav_obj = new \Admin\Model\NavModel();
		if ($id == "main") {
			$navcat_obj = new \Admin\Model\NavCatModel();
			$main = $navcat_obj->where("active=1")->find();
			$id = $main['navcid'];
		}
		$navs = $nav_obj->where("cid=$id")->order(array("listorder" => "ASC"))->select();
		foreach ($navs as $key => $nav) {
			$href = $nav['href'];
			$hrefold = $href;
			$href = unserialize(stripslashes($nav['href']));
			if (empty($href)) {
				if ($hrefold == "home") {
					$href = __ROOT__ . "/";
				} else {
					$href = $hrefold;
				}
			} else {
				$default_app = strtolower(C("DEFAULT_GROUP"));
				$href = U($href['action'], $href['param']);
				$g = C("VAR_GROUP");
				$href = preg_replace("/\/$default_app\//", "/", $href);
				$href = preg_replace("/$g=$default_app&/", "", $href);
			}
			$nav['href'] = $href;
			$navs[$key] = $nav;
		}
		F("site_nav", $navs);
	}

	$tree = new \Common\Lib\Util\Tree();
	$tree->init($navs);
	return $tree->get_treeview_menu(0, $effected_id, $filetpl, $foldertpl, $showlevel, $ul_class, $li_class, $style, 1, FALSE, $dropdown);
}

/*
 * 作用：写入新消息
 * 参数：$from	发送者id
 * 		$to		消息接受者id
 * 		$content  消息内容
 * 		$targetid 相应数据表中的id的值
 * 		$mestype可选值：topic_comment(话题评论)、topic_answer(话题回复)、topic_collect(话题收藏)、topic_love(喜欢)
 */

function insertMes($from, $to, $content, $targetid, $mestype) {
	$data = array(
		'mes_from'		 => $from,
		'mes_to'		 => $to,
		'mes_content'	 => $content,
		'post_time'		 => time(),
		'target_id'		 => $targetid,
		'mes_type'		 => $mestype,
		'mes_status'	 => '2', //未读
	);
	return M('Message')->add($data);
}

/*
 * 作用：查看用户消息
 * 参数：$uid	查询用户id
 * 		$status		消息接受者id
 * 		$mestype可选值：topic_comment(话题评论)、topic_answer(话题回复)、topic_collect(话题收藏)、topic_love(喜欢)
 * 注意：查询时仅限于members,message,topic三张表，因此只能查询三张表中的信息
 */

function getMes($uid, $type, $status = 2) {
	$DbPre = C('DB_PREFIX');
	$sql = 'select a.*,b.user_login_name,b.ID,c.topic_id,c.topic_cid,c.title
    		from ' . $DbPre . 'message a left join ' . $DbPre . 'members b
    		on a.mes_from=b.ID left join ' . $DbPre . 'topic c on a.target_id=c.topic_id
    		where a.mes_status=' . $status . ' and mes_type=\'' . $type . '\' and a.mes_to=' . $uid
		. ' order by a.post_time desc';
	return $topic_comment = M()->query($sql);
}

//获取站内消息数量
function getMesNum() {
	if (!isset($_SESSION["MEMBER_id"]))
		return;
	return M('Message')->where('mes_status=2 and mes_to=' . $_SESSION["MEMBER_id"])->count();
}

//面包屑导航
function sp_bread_nav($nav_id) {
	$navTable = M('Nav');
	$path = $navTable->where("id=$nav_id")->getField('path');
	if (!$path) {
		return array();
	}
	$path = str_replace('-', ',', $path);
	return $navTable->where("id in ({$path})")->order('id')->select();
}
