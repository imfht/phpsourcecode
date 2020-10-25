<?php

function gpc($name,$w = 'GPC',$default = ''){
	$i = 0;
	$w = strtoupper($w);
	for($i = 0; $i < strlen($w); $i++) {
		if($w[$i] == 'G' && isset($_GET[$name])) return $_GET[$name];
		if($w[$i] == 'P' && isset($_POST[$name])) return $_POST[$name];
		if($w[$i] == 'C' && isset($_COOKIE[$name])) return $_COOKIE[$name];
	}
	return $default;
}

/**
 * 密码加密函数
 *
 * @param string $password
 * @return string $encode_password
 * @author HumingXu E-mail:huming17@126.com
 */
function encode_password($password){
    $encode_password = '';
    $encode_password = sha1( md5( $password ) );
    return $encode_password;
}

/**
 * 获取分类名称函数
 *
 * @param int $info_cateid
 * @return string $cate_title
 * @author HumingXu E-mail:huming17@126.com
 */
function get_title_by_info_cateid($info_cateid){
    $cate_title = '';
    if(!empty($info_cateid)){
        $cate_title_sql = "SELECT title FROM ".DB::table('content_cate')." WHERE info_cateid='".$info_cateid."' LIMIT 1";
        $cate_title = DB::result_first($cate_title_sql);
    }
    return $cate_title;
}

/**
 * 页面跳转函数
 *
 * @param int $info_cateid
 * @return string $cate_title
 * @author HumingXu E-mail:huming17@126.com
 */
function zshowmessage($message, $url_forward = '', $values = array(), $extraparam = array(), $custom = 0) {
	@header("location: ".$url_forward);
}

//DEBUG 获取模版列表数组
function get_templates(){
    $tpldir_array = array();
    $tpldir_path = SITE_ROOT.'template';
    $tpldir_array = directoryToArray($tpldir_path, false,  true, false);
    foreach($tpldir_array AS $key => $value){
        $tpldir_array[$key] = str_ireplace($tpldir_path,'',$value);
        $tpldir_array[$key] = str_ireplace(array('\\','/'),'',$tpldir_array[$key]);
    }
    return $tpldir_array;
}

/*
 * HTML 中返回第一个图片
 * get_first_imgpath_from_html
 * @pram string $html 
 * @return string img src
 * @author xuhm
 */

function get_first_imgpath_from_html($html){
    $matches = array();
    preg_match_all("/<img(.*)(src=\"[^\"]+\")[^>]+>/isU", $html, $matches);
    return $matches[2][0];
}

/*
 * 后台信息分类数据数组树格式化函数
 * @pram 一维 array $array
 * @return 一维 $array tree
 */
function dbarr2tree($tree, $rootId = 0) {
    $return = array();
    foreach($tree as $leaf) {
        if($leaf['info_catepid'] == $rootId) {
            foreach($tree as $subleaf) {
                if($subleaf['info_catepid'] == $leaf['info_cateid']) {
                    $leaf['children'] = dbarr2tree($tree, $leaf['info_cateid']);
                    break;
                }
            }
            $return[] = $leaf;
        }
    }
    return $return;
}

/*
 * 后台分类列表选择树HTML格式化UL格式函数
 * @pram array $array tree
 * @return html ul
 */
function tree2htmlul($tree,$relate) {
    if($relate==1){
        $bringback_id = 'relate_info_cateid';
        $bringback_title = 'title2';
    }else{
        $bringback_id = 'info_cateid';
        $bringback_title = 'title';
    }
    $tree2htmlul .= '<ul>';
    foreach($tree as $leaf) {
        $tree2htmlul .= "<li><a href='javascript:' onclick=\"$.bringBack({".$bringback_id.":'".$leaf['info_cateid']."', ".$bringback_title.":'".$leaf['title']."'})\">" .$leaf['title']."</a>";
        if(!empty($leaf['children'])) {
            $tree2htmlul .= tree2htmlul($leaf['children'],$relate);
        }
        $tree2htmlul .= '</li>';
    }
    $tree2htmlul .= '</ul>';
    return $tree2htmlul;
}

/*
 * 后台分类列表选择树HTML格式化UL格式函数 用户信息分类检索
 * @pram array $array tree
 * @return html ul
 */
function tree2htmlul_infosearch($tree,$tree_class) {
    $tree2htmlul .= '<ul class="'.$tree_class.'">';
    foreach($tree as $leaf) {
        $tree2htmlul .= "<li><a href='admin.php?mod=info&action=index&do=info_ajax&info_cateid=".$leaf['info_cateid']."' target='ajax' rel='jbsxBox'>" .$leaf['title']."</a>";
        if(!empty($leaf['children'])) {
            $tree2htmlul .= tree2htmlul_infosearch($leaf['children']);
        }
        $tree2htmlul .= '</li>';
    }
    $tree2htmlul .= '</ul>';
    return $tree2htmlul;
}

/*
 * 后台分类列表选择树HTML格式化UL格式函数 用户信息分类检索
 * @pram array $array tree
 * @return html ul
 */
function tree2htmlul_catesearch($tree,$tree_class) {
    $tree2htmlul .= '<ul class="'.$tree_class.'">';
    foreach($tree as $leaf) {
        $tree2htmlul .= "<li><a href='admin.php?mod=info_cate&action=index&do=cate_ajax&info_cateid=".$leaf['info_cateid']."' target='ajax' rel='jbsxBox'>" .$leaf['title']."</a>";
        if(!empty($leaf['children'])) {
            $tree2htmlul .= tree2htmlul_infosearch($leaf['children']);
        }
        $tree2htmlul .= '</li>';
    }
    $tree2htmlul .= '</ul>';
    return $tree2htmlul;
}

//判断奇数，是返回TRUE，否返回FALSE
 function is_odd($num){
     return (is_numeric($num)&($num&1));
 }
 //判断偶数，是返回TRUE，否返回FALSE
 function is_even($num){
     return (is_numeric($num)&(!($num&1)));
 }

 /**
 * 判断当前系统是否类linux
 * @author xuhm
 * @return int 
 *  ex: 1 = 是类linux
 *      2 = 非类linux
 *      3 = 未知系统
 */
function is_unix(){
	$return_int =3;
	$os_str=php_uname();
	if(strpos($os_str,'Ubuntu')){
		$return_int = 1;
	}elseif(strpos($os_str,'inux')){
		$return_int = 1;
	}else{
		$return_int = 2;
	}
	return $return_int;
}

/**
 * 检查升级包是否有效
 * @author xuhm
 * @return array 
 *  ex: array(
 *          'status' => '1 成功 2 失败或无效',
 *          'data_cache_pku' => '' //更新包解压目录,
 *          'patch' => array() // 更新包XML信息 
 *      )
 */
function check_pku($update_package_path){
    $return_array = array(
        'status' => '2',
        'data_cache_pku' => '',
        'patch' => array()
    );
    //DEBUG 安装包缓存目录
    $data_cache_pku = SITE_ROOT.'./data/cache/pku_'.random(4);
    if(!empty($update_package_path) && file_exists($update_package_path)){
        if(is_dir($data_cache_pku)){
            $data_cache_pku = SITE_ROOT.'./data/cache/pku_'.random(8);
        }
        dmkdir($data_cache_pku);
        //DEBUG 执行解压
        if($data_cache_pku){
            //执行PHP ZipArchive 解压
            $zip = new ZipArchive;
            if ($zip->open($update_package_path) === true) { 
                $zip->extractTo($data_cache_pku); 
                $zip->close(); 
            }
            //解析更新包XML
            //DEBUG 1.获取XML版本信息 以及升级环境要求
            $upgrade_xml = realpath($data_cache_pku.'/upgrade.xml');
            $upgrade_upgradelist = realpath($data_cache_pku.'/patch_upgradelist.txt');
            //DEBUG 2.检查版本是否匹配
            if(file_exists($upgrade_xml) && file_exists($upgrade_upgradelist)){
                $upgrade_xml_content=file_get_contents($upgrade_xml);
                $zip_upgradelist_content=file_get_contents($upgrade_upgradelist);
                //DEBUG 3.获取文件列表 检测文件
                if(!empty($upgrade_xml_content) && !empty($zip_upgradelist_content)){
                    $upgrade_xml_array = xml2array($upgrade_xml_content);
                    //DEBUG 4.检测版本是否符合和升级包对应 检测 $project_id 和 $latestversion 和 $latestrelease 是当前系统是否比配 暂未加限制 以免混淆
                    
                    //DEBUG 5.检查更新文件是否完整(检测文件列表文件MD5是否有效 由于二进制 和 ASCII网络传输后,同样文件产生的MD5不一致,因此上传方式MD5检测机制暂未添加)
                    
                    //DEBUG 6.返回更新包信息
                    if(!empty($upgrade_xml_array['patch']['latestversion']) && !empty($upgrade_xml_array['patch']['latestrelease'])){
                        $return_array['status'] = 1;
                        $return_array['data_cache_pku'] = $data_cache_pku;
                        $return_array['patch'] = $upgrade_xml_array['patch'];
                    }
                }
            }
        }
    }
    return $return_array;
}

/**
 * 获取URL上的ID参数
 * @author xuhm
 * @return string ajax_url_href 
 */

function set_ajax_url_href($url = ''){
    //DEBUG 处理 index.php?mod=index&action=info&do=detail&id=7
    //DEBUG 处理 index.php?mod=index&action=cate&do=list&id=125
    $return_ajax_url_href = '';
    $url_array = explode('=',$url);
    $url_array_num = count($url_array);
    if($url_array[($url_array_num-2)] == 'detail&id'){
        $return_ajax_url_href = "javascript:info_detail('".$url_array[($url_array_num-1)]."')";
    }elseif($url_array[($url_array_num-2)] == 'list&id'){
        $return_ajax_url_href = "javascript:waterfall('ajax','".$url_array[($url_array_num-1)]."','',1);";
    }else{
        $return_ajax_url_href = $url;
    }
    return $return_ajax_url_href;
}

/*
 * 缓存用户权限菜单数据数组树格式化函数
 * @pram 一维 array $array
 * @return 一维 $array tree
 */
function menuarr2tree($tree, $rootId = 0) {
    $return = array();
    foreach($tree as $leaf) {
        if($leaf['menu_pid'] == $rootId) {
            foreach($tree as $subleaf) {
                if($subleaf['menu_pid'] == $leaf['menu_id']) {
                    $leaf['submenu'] = menuarr2tree($tree, $leaf['menu_id']);
                    break;
                }
            }
            $return[$leaf['menu_id']] = $leaf;
        }
    }
    return $return;
}

function writetojscache() {
    $dir = DZF_ROOT . 'static/js/';
    $dh = opendir($dir);
    $remove = array(
        array(
            '/(^|\r|\n)\/\*.+?\*\/(\r|\n)/is',
            "/([^\\\:]{1})\/\/.+?(\r|\n)/",
            '/\/\/note.+?(\r|\n)/i',
            '/\/\/debug.+?(\r|\n)/i',
            '/(^|\r|\n)(\s|\t)+/',
            '/(\r|\n)/',
        ), array(
            '',
            '\1',
            '',
            '',
            '',
            '',
    ));
    while (($entry = readdir($dh)) !== false) {
        if (fileext($entry) == 'js') {
            $jsfile = $dir . $entry;
            $fp = fopen($jsfile, 'r');
            $jsdata = @fread($fp, filesize($jsfile));
            fclose($fp);
            $jsdata = preg_replace($remove[0], $remove[1], $jsdata);
            if (@$fp = fopen(DZF_ROOT . './data/cache/' . $entry, 'w')) {
                fwrite($fp, $jsdata);
                fclose($fp);
            } else {
                exit('Can not write to cache files, please check directory ./data/ and ./data/cache/ .');
            }
        }
    }
}

/*
* 函数说明 URL登录功能函数
* @author xuhm
* @pram username 用户名
* @password 用户名密码 md5(32位)后的密文
* @return 无
*/
function url_login(){
	//DEBUG 模拟登录
	$user_name = isset($_GET['user_name']) ? $_GET['user_name']:'';
	$user_password = isset($_GET['user_password']) ? $_GET['user_password']:'';
	$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	$replace_pram = 'user_name='.$user_name.'&user_password='.$user_password;
	$location_url = str_replace($replace_pram,'',$url);
	if(!empty($user_name) && !empty($user_password)){
		/*
		* url pram add user_name=teacher1&user_password=e10adc3949ba59abbe56e057f20f883e
		* eg: home.php?user_name=teacher1&user_password=e10adc3949ba59abbe56e057f20f883e
		*/
		$member = DB::fetch_first("SELECT user_id,user_password from ".DB::table('users')." WHERE user_name='".$user_name."' LIMIT 1");
		if(empty($member)){
			$member = DB::fetch_first("SELECT user_id,user_password from ".DB::table('users')." WHERE user_id='".$user_name."' LIMIT 1");
		}
		if($member['user_password'] && $member['user_id']){
			$user_id=$member['user_id'];
			//校验密码 是否正确
			$uc_password = sha1($user_password);
			if($uc_password==$member['user_password']){
				//loaducenter();
				//$ucsynlogin = uc_user_synlogin($uid);
				$user['user_id']=$user_id;
				ext::synlogin($user,$user);
				header('location:'.$location_url.'');
				die;	
			}
		}
	}
}

/*
* 函数说明 ajax 通用返回函数
* @author xuhm
* @pram $data array
* @return string
*/
function format_data($data,$data_format_type='json'){
	$return = '';
	switch($data_format_type){
		case "json":
			$return = json_ext($data);
			break;
		
		case "xml":
			//@header("Content-type: application/xml");//头部输出
			$return = array2xml($data);
			//DEBUG XML 解析
			//$return_array = xml2array($return);
			//TODO 方法二 反向解析XML为数组 此方法位置DOM报错
			//$return = arrayxml::createXML('root', $data);
			//$result = arrayxml::xml2array($result,0);
			break;
	}
	return $return;
}

/*
* 函数说明 根据树形节点的position 获取该节点路径
* @author xuhm
* @pram $data array
* @return Array(
        [0] => Array(
                [menu_id] => 1021
                [menu_pid] => 1002
                    ......
                [submenu] => Array(
                        [1025] => Array(
                                [menu_id] => 1025
                                [menu_pid] => 1021
                                ......
                            )
                        [1026] => Array(
                                [menu_id] => 1026
                                [menu_pid] => 1021
                                ......
                            )
                        [1027] => Array(
                                [menu_id] => 1027
                                [menu_pid] => 1021
                                ......
                            )
                    )
            )
    )
*/
function get_treepath($tree, $level = 0, $current_position,$treepath=array('stop'=>0)) {
    foreach($tree as $leaf) {
        if($treepath['stop']==1){
            return $treepath;
            break;
        }
        if(empty($treepath['stop'])){
            $treepath[$level]=$leaf;
            if($current_position == $leaf['position']){
                $treepath['stop']=1;
                //TODO 优化 后续考虑数据库 menu 增加 level 或者把 创建节点的时候 把树路径写入到
                unset($treepath[$level+1]);
                unset($treepath[$level+2]);
                unset($treepath[$level+3]);
                unset($treepath[$level+4]);
                break;
            }else{
                $treepath = get_treepath($leaf['submenu'], $level + 1, $current_position, $treepath);
            }
        }
    }
    return $treepath;
}

/*
 * 缓存用户权限菜单数据数组树格式化函数
 * @pram 一维 array $array
 * @return 一维 $array tree
 */
function menuarr2tree2($tree, $rootId = 0) {
    $return = array();
    foreach($tree as $leaf) {
        if($leaf['menu_pid'] == $rootId) {
            foreach($tree as $subleaf) {
                if($subleaf['menu_pid'] == $leaf['menu_id']) {
                    $leaf['submenu'] = menuarr2tree($tree, $leaf['menu_id']);
                    break;
                }
            }
            $return[$leaf['menu_id']] = $leaf;
        }
    }
    return $return;
}
?>