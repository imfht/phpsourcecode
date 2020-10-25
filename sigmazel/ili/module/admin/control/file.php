<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\control;

use admin\model\_file;
use admin\model\_ilinei;
use admin\model\_setting;
use ilinei\template;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/admin/lang.php';

//模板
class file{
	//默认
	public function index(){
		global $_var;

		$_file = new _file();
        $_ilinei = new _ilinei();

		$_var['gp_path'] = $_file->get_path($_var['gp_path']);
		
		$real_path = $_file->get_real_path($_var['gp_path']);
		$path_crubms = $_file->get_crumbs($_var['gp_path']);

		//取出可使用的模板
		$tpls = $_file->get_tpls();

		//获取当前模板的页面+块
		$pages = $_ilinei->fetch($_var['gp_path']);

		//目录及文件列表
		$file_list = array();
		
		$dirs = scandir($real_path);

		//加入数据库及调试日志文件夹
		if(!$_var['gp_path']){
			$file_list[] = array('name' => '_DATA', 'type' => 'dir', 'info' => array());
			$file_list[] = array('name' => '_DEBUG', 'type' => 'dir', 'info' => array());
            $file_list[] = array('name' => '_BLOCK', 'type' => 'dir', 'info' => array());
		}

		//当前目录文件列表
		foreach ($dirs as $file){
			$temp_file = $real_path.'/'.$file;
			if(is_dir($temp_file)){
				if($file != '.' && $file != '..') $file_list[] = array('name' => $file, 'type' => 'dir', 'info' => array());
			}
			
			unset($temp_file);
		}

		//提出文件头信息！
		foreach ($dirs as $file){
			$temp_file = $real_path.'/'.$file;
			
			if(!is_dir($temp_file)) {
				$temp_arr = array('name' => $file, 'ext' => get_file_ext($file), 'type' => 'file', 'info' => stat($temp_file), 'desc' => '');
				$temp_arr['update'] = in_array($temp_arr['ext'], array('htm', 'css', 'js', 'txt', 'php', 'json', 'xml'));
				
				if($temp_arr['ext'] == 'php' || $temp_arr['ext'] == 'htm' || $temp_arr['ext'] == 'css'){
					/**
					 * PHP代码注解为 第2行\/**@name 名称*\/
					 * CSS代码注解为 第1行\/**@name 名称*\/
					 * HTML代码注解为 第1行<!--{@name 名称}-->
                     * PAGE代码注解为 第1行<!--{@page 描述}-->
                     * BLOCK代码注解为 第1行<!--{@block 描述}-->
					 */
					$lines = get_file_lines($temp_file, 2);
					if($temp_arr['ext'] == 'php' && substr($lines[1], 0, 8) == '/**@name') $temp_arr['desc'] = substr(str_replace('*/', '', $lines[1]), 8);
					elseif($temp_arr['ext'] == 'css' && substr($lines[0], 0, 8) == '/**@name') $temp_arr['desc'] = substr(str_replace('*/', '', $lines[0]), 8);
					elseif($temp_arr['ext'] == 'htm'){
					    if(substr($lines[0], 0, 10) == '<!--{@name') $temp_arr['desc'] = substr(str_replace('}-->', '', $lines[0]), 10);
					    elseif(substr($lines[0], 0, 10) == '<!--{@page'){
					        //页面
                            $temp_arr['page'] = true;
					        $temp_arr['desc'] = $pages[str_replace('.'.$temp_arr['ext'], '', $temp_arr['name'])]['name'].' '.substr(str_replace('}-->', '', $lines[0]), 10);
                        }elseif(substr($lines[0], 0, 11) == '<!--{@block'){
					        //块
                            $temp_arr['block'] = true;
					        $temp_arr['desc'] = $pages[str_replace('.'.$temp_arr['ext'], '', $temp_arr['name'])]['name'].' '.substr(str_replace('}-->', '', $lines[0]), 12);
                        }
                    }
				}elseif($file == '_info.jpg') $temp_arr['desc'] = $GLOBALS['lang']['admin.file.desc.info.jpg'];
				elseif($file == '_info.xml') $temp_arr['desc'] = $GLOBALS['lang']['admin.file.desc.info.xml'];
				
				$file_list[] = $temp_arr;
			}
			
			unset($lines);
			unset($temp_file);
			unset($temp_arr);
		}

		//加入.htaccess,.rewrite,config.php的支持
		if(empty($_var['gp_path'])){
			if(is_file(ROOTPATH.'/.htaccess')){
				$temp_arr = array('name' => '.htaccess', 'ext' => 'htccess', 'type' => 'file', 'info' => stat(ROOTPATH.'/.htaccess'), 'desc' => $GLOBALS['lang']['admin.file.desc.htaccess']);
				$temp_arr['rewrite'] = true;
				$file_list[] = $temp_arr;
			}
			
			if(is_file(ROOTPATH.'/.rewrite')){
				$temp_arr = array('name' => '.rewrite', 'ext' => 'rewrite', 'type' => 'file', 'info' => stat(ROOTPATH.'/.rewrite'), 'desc' => $GLOBALS['lang']['admin.file.desc.rewrite']);
				$temp_arr['rewrite'] = true;
				$file_list[] = $temp_arr;
			}
			
			$temp_arr = array('name' => 'config.php', 'ext' => 'php', 'type' => 'file', 'info' => stat(ROOTPATH.'/source/config.php'), 'desc' => $GLOBALS['lang']['admin.file.desc.config']);
			$temp_arr['config'] = true;
			$file_list[] = $temp_arr;
		}
		
		include_once view('/module/admin/view/file');
	}
	
	//上传
	public function _upload(){
		global $_var, $dispatches, $setting, $ADMIN_SCRIPT;
		
		$_file = new _file();
		
		$_var['gp_path'] = $_file->get_path($_var['gp_path']);
		
		if($dispatches['operations']['import']) show_message($GLOBALS['lang']['error.operation']);
		
		if(!$_FILES['flePath'] || !$_FILES['flePath']['size']) show_message($GLOBALS['lang']['admin.operation.empty.file'], "{$ADMIN_SCRIPT}/admin/file&path={$_var[gp_path]}");
		
		$newfile = ROOTPATH."/{$setting[SiteTheme]}/{$_var[gp_path]}/{$_FILES[flePath][name]}";
		
		if(is_file($newfile)) unlink($newfile);
		
		$fp_s = fopen($_FILES['flePath']['tmp_name'], 'rb');
		$fp_t = fopen($newfile, 'wb');
		
		while (!feof($fp_s)) {
			$s = @fread($fp_s, 1024 * 512);
			@fwrite($fp_t, $s);
		}
		
		fclose($fp_s);
		fclose($fp_t);
		
		header("location:{$ADMIN_SCRIPT}/admin/file&path={$_var[gp_path]}");
	}
	
	//修改
	public function _update(){
		global $_var, $dispatches;
		
		$_file = new _file();
		
		$_var['gp_path'] = $_file->get_path($_var['gp_path']);
		
		$real_path = $_file->get_real_path($_var['gp_path']);
		$path_crumbs = $_file->get_crumbs($_var['gp_path']);
		
		if($dispatches['operations']['edit']) show_message($GLOBALS['lang']['error.operation']);
		
		if(!is_file($real_path.'/'.$_var['gp_file'])) show_message($GLOBALS['lang']['error']);
		
		$temp_file = $real_path.'/'.$_var['gp_file'];

		$iscache = false;//如果是日志文件，不能编辑
		if(substr($_var['gp_path'], 0, 6) == '/_DATA' || substr($_var['gp_path'], 0, 7) == '/_DEBUG') $iscache = true;
		
		$file = array('name' => $_var['gp_file'], 'ext' => get_file_ext($_var['gp_file']), 'info' => stat($temp_file));

		if($_var['gp_formsubmit']){
			file_put_contents($temp_file, stripslashes($_POST['txtContent']));
			rename($temp_file, $real_path.'/'.$_POST['txtFileName']);

			//如果是标签定义文件，清空缓存
			if($_var['gp_path'] == '/_BLOCK' && $_var['gp_file'] == '_info.xml') cache_delete('blocks');

            show_message($GLOBALS['lang']['admin.file.message.save'], "{ADMIN_SCRIPT}/admin/file&path={$_var[gp_path]}");
		}
		
		$file['content'] = file_get_contents($temp_file);
		$file['content'] = htmlspecialchars($file['content']);
		
		include_once view('/module/admin/view/file_edit');
	}
	
	//修改rewrite文件
	public function _rewrite(){
		global $_var;
		
		if(!is_file(ROOTPATH.'/'.$_var['gp_file'])) show_message($GLOBALS['lang']['error']);
	
		$temp_file = ROOTPATH.'/'.$_var['gp_file'];
		
		$file = array('name' => $_var['gp_file'], 'ext' => get_file_ext($_var['gp_file']), 'info' => stat($temp_file));
		
		if($_var['gp_formsubmit']){
			file_put_contents($temp_file, stripslashes($_POST['txtContent']));
			rename($temp_file, ROOTPATH.'/'.$_POST['txtFileName']);
            show_message($GLOBALS['lang']['admin.file.message.save'], "{ADMIN_SCRIPT}/admin/file");
		}
		
		$file['content'] = file_get_contents($temp_file);
		$file['content'] = htmlspecialchars($file['content']);
		
		include_once view('/module/admin/view/file_edit');
	}
	
	//修改config文件
	public function _config(){
		global $_var;
		
		if(!is_file(ROOTPATH.'/source/'.$_var['gp_file'])) show_message($GLOBALS['lang']['error']);
		
		$temp_file = ROOTPATH.'/source/'.$_var['gp_file'];
		
		$file = array('name' => $_var['gp_file'], 'ext' => get_file_ext($_var['gp_file']), 'info' => stat($temp_file));
		
		if($_var['gp_formsubmit']){
			file_put_contents($temp_file, stripslashes($_POST['txtContent']));
			rename($temp_file, ROOTPATH.'/source/'.$_POST['txtFileName']);
            show_message($GLOBALS['lang']['admin.file.message.save'], "{ADMIN_SCRIPT}/admin/file");
		}
		
		$file['content'] = file_get_contents($temp_file);
		$file['content'] = htmlspecialchars($file['content']);
		
		include_once view('/module/admin/view/file_edit');
	}
	
	//删除
	public function _delete(){
		global $_var, $dispatches, $setting, $ADMIN_SCRIPT;
		
		$_file = new _file();
        $_ilinei = new _ilinei();

        if($dispatches['operations']['delete']) show_message($GLOBALS['lang']['error.operation']);

        $_var['gp_path'] = $_file->get_path($_var['gp_path']);
		$real_path = $_file->get_real_path($_var['gp_path']);

		$file_path = $real_path.'/'.$_var['gp_file'];
		if(!is_file($file_path)) show_message($GLOBALS['lang']['error']);
		
		unlink($file_path);

        $pages = $_ilinei->fetch($_var['gp_path']);
        if(count($pages) > 0){
            $file = array('name' => $_var['gp_file'], 'ext' => get_file_ext($_var['gp_file']));
            $page_file = str_replace('.'.$file['ext'], '', $_var['gp_file']);

            $_ilinei->delete($pages, $page_file);
        }

		header("location:{$ADMIN_SCRIPT}/admin/file&path={$_var[gp_path]}");
	}
	
	//设置模板
	public function _setup(){
		global $_var, $setting, $ADMIN_SCRIPT;
		
		$_setting = new _setting();
		
		if(!is_dir(ROOTPATH.'/tpl/'.$_var['gp_tpl'].'/') || !is_file(ROOTPATH.'/tpl/'.$_var['gp_tpl'].'/_info.xml')) show_message($GLOBALS['lang']['error']);
		
		$info_xml = (array)simplexml_load_file(ROOTPATH.'/tpl/'.$_var['gp_tpl'].'/_info.xml');
		if(!is_array($info_xml) || $info_xml['application'] != $setting['Application']) show_message($GLOBALS['lang']['error']);
		
		$_setting->set('SiteTheme',"/tpl/{$_var[gp_tpl]}/");
		$_setting->set('SiteIndex', 'index');

        cache_delete('setting');
		
		header("location:{$ADMIN_SCRIPT}/admin/file");
	}

	//添加页面
    public function _page(){
        global $_var, $dispatches, $setting, $ADMIN_SCRIPT;

        $_file = new _file();
        $_ilinei = new _ilinei();

        //非指定页面+块文件夹，错了。
        $_var['gp_path'] = $_file->get_path($_var['gp_path']);
        if($_var['gp_path'] != '/page') show_message($GLOBALS['lang']['error']);

        //没有修改权限
        if($dispatches['operations']['add']) show_message($GLOBALS['lang']['error.operation']);

        if(empty($_var['gp_txtFile'])) show_message($GLOBALS['lang']['error']);
        if(empty($_var['gp_txtName'])) show_message($GLOBALS['lang']['error']);

        $pages = $_ilinei->fetch($_var['gp_path']);
        if(count($pages) == 0) show_message($GLOBALS['lang']['error']);

        foreach($pages as $key => $item){
            if($item['file'] == $_var['gp_txtFile']){
                show_message($GLOBALS['lang']['admin.file.error.page']);
                exit(0);
            }
        }

        $page_file = ROOTPATH."/{$setting[SiteTheme]}/page/{$_var[gp_txtFile]}.htm";
        if(!@$fp = fopen($page_file, 'w')){
            show_message($GLOBALS['lang']['error']);
            exit(0);
        }

        if($_var['gp_rdoType'] == 'page'){
            $page_content = "<!--{@page {$_var[gp_txtDescription]}}-->
<!DOCTYPE html>
<html>
<head>
<!--{block file=\"meta\"/}-->
</head>
<body>
<!--{block file=\"\"/}-->
</body>
</html>";
        }elseif($_var['gp_rdoType'] == 'block'){
            $page_content = "<!--{@block {$_var[gp_txtDescription]}}-->";
        }else $page_content = '';

        flock($fp, 2);
        fwrite($fp, $page_content);
        fclose($fp);

        $pages = $_ilinei->fetch($_var['gp_path']);
        $_ilinei->append($pages, array('type' => $_var['gp_rdoType'], 'file' => $_var['gp_txtFile'], 'name' => $_var['gp_txtName']));

        //返回当前目录
        header("location:{$ADMIN_SCRIPT}/admin/file&path={$_var[gp_path]}");
        exit(0);
    }

	//解析页面+块
    public function _ilinei(){
        global $_var, $dispatches, $setting;

        $_file = new _file();
        $_ilinei = new _ilinei();

        //非指定页面+块文件夹，错了。
        $_var['gp_path'] = $_file->get_path($_var['gp_path']);
        if($_var['gp_path'] != '/page') show_message($GLOBALS['lang']['error']);

        $real_path = $_file->get_real_path($_var['gp_path']);
        $path_crumbs = $_file->get_crumbs($_var['gp_path']);

        //没有修改权限
        if($dispatches['operations']['edit']) show_message($GLOBALS['lang']['error.operation']);

        //没有文件也错。
        if(!is_file($real_path.'/'.$_var['gp_file'])) show_message($GLOBALS['lang']['error']);

        $file = array('name' => $_var['gp_file'], 'ext' => get_file_ext($_var['gp_file']));

        //可视化编辑分两部分，头部为操作按钮，下部为编辑内容
        if($_var['gp_frame'] == 'top') include_once view('/module/admin/view/ilinei_frame_top');
        elseif($_var['gp_frame'] == 'content'){
            //去掉文件名后缀
            $view_name = str_replace('.'.$file['ext'], '', $_var['gp_file']);

            //默认前台的页面为当前文件
            $dispatches['page'] = $view_name;

            //如果是预览，直接显示
            if($_var['gp_do'] == 'view'){
                include_once view("/{$setting[SiteTheme]}/page/{$view_name}");
                exit(0);
            }

            //页面或块文件真实的路径
            $view_file = ROOTPATH."/{$setting[SiteTheme]}/page/{$_var[gp_file]}";

            //默认源代码编辑模式
            $pages = $_ilinei->fetch($_var['gp_path']);
            $blocks = $_ilinei->blocks();

            if($_var['gp_do'] == 'design'){
                //每个模板必需有个META块，引用块不可选取！
                unset($pages['meta']);

                $GLOBALS['_ILINEI_ID'] = 0;
                $GLOBALS['_ILINEI_TYPE'] = $pages[$view_name]['type'];

                if($GLOBALS['_ILINEI_TYPE'] == 'block'){
                    echo '<p class="-ilinei-block-prev"></p>';
                }

                //如果提交了
                if($_var['gp_formsubmit']){
                    file_put_contents($view_file, $_ilinei->save($view_file));
                    show_message($GLOBALS['lang']['admin.file.message.save'], "{ADMIN_SCRIPT}/admin/file/_ilinei&frame=content&do=design&file={$_var[gp_file]}&path={$_var[gp_path]}");
                }

                $template = new template("/{$setting[SiteTheme]}/page/{$view_name}", 1);
                include_once $template->parsed();

                if($GLOBALS['_ILINEI_TYPE'] == 'block'){
                    echo '<p class="-ilinei-block-next"></p>';
                }

                include_once view("/module/admin/view/ilinei_frame_design");
            }else{
                //如果提交了
                if($_var['gp_formsubmit']){
                    file_put_contents($view_file, stripslashes($_POST['txtContent']));
                    show_message($GLOBALS['lang']['admin.file.message.save'], "{ADMIN_SCRIPT}/admin/file/_ilinei&frame=content&file={$_var[gp_file]}&path={$_var[gp_path]}");
                }

                $file['content'] = file_get_contents($view_file);
                $file['content'] = htmlspecialchars($file['content']);

                include_once view("/module/admin/view/ilinei_frame_code");
            }
        }else include_once view('/module/admin/view/ilinei_frame');
    }

    //解析TAG的模板内容
    public function _parse(){
        global $_var, $setting;

        $_file = new _file();
        $_ilinei = new _ilinei();

        //非指定页面+块文件夹，错了。
        $_var['gp_path'] = $_file->get_path($_var['gp_path']);
        if($_var['gp_path'] != '/page') exit_echo($GLOBALS['lang']['error']);

        $real_path = $_file->get_real_path($_var['gp_path']);

        //没有文件也错。
        if(!is_file($real_path.'/'.$_var['gp_file'])) exit_echo($GLOBALS['lang']['error']);

        $content = stripcslashes($_var['gp_content']);
        $content = substring($content, 4, strpos($content, '>'));

        $content = str_replace(array('"', '\''), '', $content);
        $content = explode(' ', $content);

        $params = array();
        foreach($content as $key => $v){
            $v = trim($v);
            if(!$v) continue;

            $arr = explode('=', $v);
            $params[$arr[0]] = $arr[1];
        }

        //如果无模板
        if(empty($params['theme']) && empty($params['file'])) return '';

        $parse_file = ROOTPATH."/_cache/debug/_parse_{$_var[current][USERID]}.htm";
        if(!@$fp = fopen($parse_file, 'w')) exit_echo('');

        $content = $_ilinei->parse($params);

        flock($fp, 2);
        fwrite($fp, $content);
        fclose($fp);

        $template = new template("/_cache/debug/_parse_{$_var[current][USERID]}");
        include_once $template->parsed();
    }
}
?>