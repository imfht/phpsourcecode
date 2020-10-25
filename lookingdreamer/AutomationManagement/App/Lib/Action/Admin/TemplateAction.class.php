<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class TemplateAction extends CommonAction {
    public function _before_insert() {
        $this->upload();
        $this->newtheme_tplfile();
    }
    
    public function _before_update() {
        $this->upload();
    }

		public function _tigger_insert($model) {
	        $this->saveTag($model->tags,$model->id);
		}
	
		public function _tigger_update($model) {
	        $this->saveTag($model->tags,$model->id);
		}

    public function upload() {
    		if($_POST["tpls"]){
    			$temp = explode(",", $_POST["tpls"]);
    			$_POST["tplid"] = $temp[0];
    			$_POST["tpldir"] = $temp[1];
    		}
        if(!empty($_FILES['pic']['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,gif,png,jpeg');
            //设置附件上传目录
            $upload->savePath =  './App/Tpl/Home/Default/Public/Styles/'.$_POST["styledir"]."/";
            $upload->thumb  =  true;
            $upload->thumbMaxWidth =  200;
            $upload->thumbMaxHeight = 160;
            $upload->thumbPrefix   =  '';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
                $_POST['pic'] = "Default/Public/Styles/".$_POST["styledir"]."/".$info[0]['savename'];
            }
        }
    }
    
    function read(){
		$model	=	M("Template");
		$id     = $_REQUEST[$model->getPk()];
		$vo	=	$model->find($id);
		if(!$vo){
			$this->error('模板主题不存在');
		}else{
			$Theme = $vo['tpldir'];
		}
    	$path = './App/Tpl/Home/'.$Theme.'/';
    	$cachefile		=	DATA_PATH.'~tplfile_'.strtolower($Theme).'.php';
    	$file_id = 1;
			$tpl_extre = explode(".", C('TMPL_TEMPLATE_SUFFIX'));
			$tplext = array_pop($tpl_extre);    	
    	$allowfile = array($tplext);
    	if(file_exists($path.'tplname.php')){
    		$TplName = include($path.'tplname.php');
    	}
    	session('CUR_TEMPLATE', $Theme.'_'.$id);
    	if(!file_exists($cachefile) || time() > filemtime($cachefile) + 3600){
				foreach(glob($path.'*') as $dir){
					if(is_dir($dir)){
						if ($handle = opendir($dir)) {
								while (false !== ($file = readdir($handle))) {
										if($file != '.' && $file !== '..') {
												$cur_path = $dir . '/' . $file;
												$file_extre = explode(".", $cur_path);
												$fileext = array_pop($file_extre);
												if(!in_array(strtolower($fileext),$allowfile)){
													continue;
												}
												if(!is_dir($cur_path)) {
													$filename = strtolower(str_replace(C('TMPL_TEMPLATE_SUFFIX'), '', $file));
													$module = strtolower(str_replace($path, '', $dir));
													$filetitle = $TplName[$module][$filename]['title'];
													$filetype = intval($TplName[$module][$filename]['type']);
													$filesize = filesize($cur_path);
													$val = array(
														'id' => $Theme.'_'.$file_id,
														'filetitle' => $filetitle.' '.$file,
														'title' => $filetitle,
														'filepath' => $cur_path,
														'filedir' => $dir,
														'filetype' => $filetype,
														'module' => $module,
														'filename' => $filename,
														'fileext' => $fileext,
														'filesize' => $filesize,
														'filemtime' => filemtime($cur_path),
														'tid' => $id,
													);
													$list[$file_id] = $val;
													$file_id += 1;
												}
										}
								}
								closedir($handle);
						}
					}
				}
				$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
				file_put_contents($cachefile,$content);
			}else{
				$list = include($cachefile);
			}
			$this->assign("list",$list);
    	$this->display();
    }
    
    function update_tplfile(){
    	$id = $_POST['id'];
    	$ids = explode('_', $id);
    	$Theme = $ids[0];
    	$id = $ids[1];
    	$cachefile		=	DATA_PATH.'~tplfile_'.strtolower($Theme).'.php';
    	if(!file_exists($cachefile))$this->error('模板主题不存在');
    	$list = include($cachefile);
    	$tplfile = $list[$id];
    	if(!$tplfile)$this->error('模板文件不存在');
    	/*保存模板*/
    	$content = stripslashes($_POST['content']);
    	$pattern = array('__URL__', '__APP__', '__PUBLIC__', '../Public', '__ROOT__', '__ACTION__', 'APP_NAME', 'GROUP_NAME', 'MODULE_NAME', 'ACTION_NAME');
    	$replaces = array('__url__', '__app__', '__public__', '../public', '__root__', '__action__', 'app_name', 'group_name', 'module_name', 'action_name');
    	$content = str_replace($replaces, $pattern, $content);
    	file_put_contents($tplfile['filepath'],$content);
    	/*更新缓存*/
    	$filesize = filesize($tplfile['filepath']);
    	$title = trim($_POST['title']);
    	$tplfile['title'] = $title;
    	$tplfile['filetitle'] = $title.' '.$tplfile['filename'].'.'.$tplfile['fileext'];
    	$tplfile['filesize'] = $filesize;
    	$list[$id] = $tplfile;
			$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
			file_put_contents($cachefile,$content);
			/*更新文件列表*/
    	$tplname = './App/Tpl/Home/'.$Theme.'/tplname.php';
    	if(file_exists($tplname)){
		    	$module = strtolower($tplfile['module']);
		    	$filename = strtolower($_POST['filename']);
		    	$list = include($tplname);
		    	$list[$module][$filename] = array('title' => $title, 'type' => $tplfile['filetype']);
					$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
					file_put_contents($tplname,$content);
			}
    	$this->success(L('保存成功'));
    }
    
    function insert_tplfile(){
    	$Theme = ucwords($_POST['Theme']);
    	if(!$Theme)$this->error('模板主题不存在');
    	$cachefile		=	DATA_PATH.'~tplfile_'.strtolower($Theme).'.php';
    	if(file_exists($cachefile))$list = include($cachefile);
    	$file_id = count($list)+1;
    	$id = $_POST['id'];
    	$module = $_POST['module'];
    	$filename = $_POST['filename'];
    	$filetitle = $_POST['title'];
    	$file = $filename.C('TMPL_TEMPLATE_SUFFIX');
    	$path = './App/Tpl/Home/'.$Theme.'/';
    	$dir =  $path.$module.'/';
    	$cur_path = $dir.$file;
			$tpl_extre = explode(".", $file);
			$fileext = array_pop($tpl_extre);     	
			$tplfile = array(
					'id' => $Theme.'_'.$file_id,
					'filetitle' => $filetitle.' '.$file,
					'title' => $filetitle,
					'filepath' => $cur_path,
					'filedir' => $dir,
					'filetype' => 0,
					'module' => $module,
					'filename' => $filename,
					'fileext' => $fileext,
					'filesize' => 0,
					'filemtime' => 0,
					'tid' => $id,
			);
			if(file_exists($cur_path))$this->error('模板文件已经存在');
    	/*保存模板*/
    	$content = stripslashes($_POST['content']);
    	$pattern = array('__URL__', '__APP__', '__PUBLIC__', '../Public', '__ROOT__', '__ACTION__', 'APP_NAME', 'GROUP_NAME', 'MODULE_NAME', 'ACTION_NAME');
    	$replaces = array('__url__', '__app__', '__public__', '../public', '__root__', '__action__', 'app_name', 'group_name', 'module_name', 'action_name');
    	$content = str_replace($replaces, $pattern, $content);    	
    	file_put_contents($tplfile['filepath'],$content);
    	/*更新缓存*/
    	$filesize = filesize($tplfile['filepath']);
    	$tplfile['filesize'] = $filesize;
    	$tplfile['filemtime'] = filemtime($cur_path);
    	$list[$file_id] = $tplfile;
			$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
			file_put_contents($cachefile,$content);
			/*更新文件列表*/
    	$tplname = './App/Tpl/Home/'.$Theme.'/tplname.php';
    	if(file_exists($tplname)){
		    	$module = strtolower($tplfile['module']);
		    	$filename = strtolower($tplfile['filename']);
		    	$list = include($tplname);
		    	$list[$module][$filename] = array('title' => $filetitle, 'type' => $tplfile['filetype']);
					$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
					file_put_contents($tplname,$content);
			}
    	$this->success(L('模板添加成功'), "__URL__/read/id/".$id);
    }    
    
    function tplfile_add(){
			$id = session('CUR_TEMPLATE');
    	$ids = explode('_', $id);
    	$Theme = ucwords($ids[0]);
    	$id = $ids[1];
    	$path = './App/Tpl/Home/'.$Theme.'/';
  		$d = opendir($path);
  		while ($file = readdir($d)){
  				if ($file == '.' || $file == '..') continue;
  				if (is_dir($path.'/'.$file)){
  					$list[] = array('title' => $file, 'value' => $file);
  				}
  		}
			$this->assign("list",$list);
    	$this->assign("id",$id);
			$this->assign("theme",$Theme);
    	$this->display('tplfile_add');
    }
    
    function tplfile_edit(){
    	$id = $_GET['id'];
			if(!$id){
				 $_ids = explode(',', $_REQUEST['ids']);
				 $id = $_ids[0];
			}    	
    	$ids = explode('_', $id);
    	$Theme = $ids[0];
    	$id = $ids[1];
    	$cachefile		=	DATA_PATH.'~tplfile_'.strtolower($Theme).'.php';
    	if(!file_exists($cachefile))$this->error('模板主题不存在');
    	$Tpllist = include($cachefile);
    	$tplfile = $Tpllist[$id];
    	if(!$tplfile)$this->error('模板文件不存在');
    	$content = htmlspecialchars(file_get_contents($tplfile['filepath']));
    	$pattern = array('__URL__', '__APP__', '__PUBLIC__', '../Public', '__ROOT__', '__ACTION__', 'APP_NAME', 'GROUP_NAME', 'MODULE_NAME', 'ACTION_NAME');
    	$replaces = array('__url__', '__app__', '__public__', '../public', '__root__', '__action__', 'app_name', 'group_name', 'module_name', 'action_name');
    	$content = str_replace($pattern, $replaces, $content);
    	$this->assign("vo",$tplfile);
    	$this->assign("content",$content);
    	$this->display('tplfile_edit');
    }
    
    function tplfile_delete(){
    	$id = $_REQUEST['id'];
			if(!$id){
				 $_ids = explode(',', $_REQUEST['ids']);
				 $id = $_ids[0];
			}
    	$ids = explode('_', $id);
    	$Theme = $ids[0];
    	$id = $ids[1];    	
    	$cachefile		=	DATA_PATH.'~tplfile_'.strtolower($Theme).'.php';
    	if(!file_exists($cachefile))$this->error('模板主题不存在');
    	$Tpllist = include($cachefile);
    	$tplfile = $Tpllist[$id];
    	if(file_exists($tplfile['filepath']))unlink($tplfile['filepath']);
    	/*更新缓存*/
    	unset($Tpllist[$id]);
			$content		=   "<?php\nreturn ".var_export(array_change_key_case($Tpllist,CASE_LOWER),true).";\n?>";
			file_put_contents($cachefile,$content);
			/*更新文件列表*/
    	$tplname = './App/Tpl/Home/'.$Theme.'/tplname.php';
    	if(file_exists($tplname)){
		    	$module = strtolower($tplfile['module']);
		    	$filename = strtolower($tplfile['filename']);
		    	$list = include($tplname);
		    	unset($list[$module][$filename]);
					$content		=   "<?php\nreturn ".var_export(array_change_key_case($list,CASE_LOWER),true).";\n?>";
					file_put_contents($tplname,$content);
			}
			$this->success(L('删除成功'), "__URL__/read/id/".$tplfile['tid']);
    }
    
		function index() {
	      //列表过滤器，生成查询Map对象
	      $map = $this->_search("Tplstyle");
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
				$model = M("Tplstyle");
	      if(!empty($model)) {
	        	$this->_list($model,$map,'isdefault desc, update_time', false);
	      }
				$this->display();
		}
		
		function theme() {
	      //列表过滤器，生成查询Map对象
	      $map = $this->_search("Template");
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
				$model = M("Template");
	      if(!empty($model)) {
	        	$this->_list($model,$map);
	      }
				$this->display();
		}		
	
		function style_add() {
			$model	=	M("Template");
			$list	=	$model->select();
			$this->assign("tpllist",$list);
			$this->display("style_add");
		}
			
		function style_edit() {
					$model	=	M("Tplstyle");
					$id     = $_REQUEST[$model->getPk()];
					if(!$id){
						 $_ids = explode(',', $_REQUEST['ids']);
						 $id = $_ids[0];
					}
					$vo	=	$model->find($id);
					$model	=	M("Template");
					$list	=	$model->select();
					$this->assign("tpllist",$list);
					$this->assign('vo',$vo);
					$this->display("style_edit");
		}	
		
	function style_import() {
		if($this->_post()){
			$style_dir = rand_string(10, '', 'oOLl01_');
			$tpls = explode(',', $_POST['tpls']);
			$tpl_dir = $tpls[1];
			$tpl_id = $tpls[0];
			$style_path = './App/Tpl/Home/'.$tpl_dir.'/Public/Styles/'.$style_dir;
	        import("ORG.Net.UploadFile");
	        $upload = new UploadFile();
	        $upload->allowExts  = explode(',',strtolower('zip,rar'));
	        $upload->savePath =  './Public/Uploads/';
	        if(!$upload->upload()) {
	             $this->error($upload->getErrorMsg());
	        }else {
				$uploadfile = $upload->getUploadFileInfo();
				$zipfile = $upload->savePath.$uploadfile[0]['savename'];
				$temp = explode('.', $uploadfile[0]['name']);
				$title = $temp[0];
			}
			import("ORG.Util.Zip");
			$archive = new PclZip($zipfile);
			$list = $archive->extract(PCLZIP_OPT_PATH, $style_path, PCLZIP_OPT_REMOVE_PATH, "seophp_style_new");
			if(file_exists($style_path."/style.ini"))$style = include($style_path."/style.ini");
			$model	=	M("Tplstyle");
			$data = array(
			  'title' => $style['title'] ? $style['title'] : $title,
			  'styledir' => $style_dir,
			  'status' => '1',
			  'pic' => $tpl_dir.'/Public/Styles/'.$style_dir.'/preview.jpg',
			  'create_time' => C('NOW_TIME'),
			  'update_time' => C('NOW_TIME'),
			  'remark' => '',
			  'sort' => '0',
			  'tags' => '',
			  'industry' => '',
			  'colors' => '',
			  'tplid' => $tpl_id,
			  'tpldir' => $tpl_dir,
			  'isdefault' => intval($_POST['isdefault']),
			  'numberID' => $style['numberID'],
			);
			$id = $model->add($data);
			$model->data(array("isdefault" => 0))->where("tpldir='".$tpl_dir."' AND tplid='".$tpl_id."' AND id<>'".$id."'")->save();
			unlink($zipfile);
			if(file_exists($style_path."/style.ini"))unlink($style_path.'/style.ini');
			$this->success(L('样式导入成功，请更新模板缓存！'), U('Template/index'));
		}else{
			$model	=	M("Template");
			$list	=	$model->select();
			$this->assign("tpllist",$list);
			$this->display("style_import");
		}
	}
		
	function style_out() {
		$model	=	M("Tplstyle");
		$id     = $_REQUEST[$model->getPk()];
		$vo	=	$model->find($id);
		if(!$vo)$this->error("样式不存在");
		$zip_path = TEMP_PATH.'style_'.$vo['tpldir'].'_'.$vo['styledir'].'.zip';
		$style_path = './App/Tpl/Home/'.$vo['tpldir'].'/Public/Styles/'.$vo['styledir'];
		$content = "<?php\nreturn ".var_export($vo,true).";\n?>";
		file_put_contents($style_path.'/style.ini',$content);
		import("ORG.Util.Zip");
		$Zip = new PclZip($zip_path);
		$list = $Zip->create($style_path, PCLZIP_OPT_REMOVE_PATH, $style_path, PCLZIP_OPT_ADD_PATH, 'seophp_style_new');
		if(file_exists($style_path."/style.ini"))unlink($style_path.'/style.ini');
		import("ORG.Net.Http");
		Http::download($zip_path);
	}

    public function style_delete()
    {
        //删除指定记录
        $model = M("Tplstyle");
        if(!empty($model)) {
			$pk	= $model->getPk();
            $id = $_REQUEST[$pk];
            if(isset($id)) {
            	$ids = explode(',',$id);
                $condition = array($pk=>array('in',$ids));
                $list = $model->where("id IN('".implode("','", $ids)."')")->select();
                if(false !== $model->where($condition)->delete()){
                	foreach($list as $val){
                		$style_path = './App/Tpl/Home/'.$val['tpldir'].'/Public/Styles/'.$val['styledir'];
                		delDirAndFile($style_path, true);
                	}
                    $this->success(L('删除成功'));
                }else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    }	
    /**
     +----------------------------------------------------------
     * 默认排序操作
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws FcsException
     +----------------------------------------------------------
     */
    public function sort()
    {
		$node = D('Case');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            $sortList   =   $node->findAll(array(
                'condition'=>'status=1',
                'order'=>'sort asc')
                );
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存文件
	public function cache($name='',$fields='') {
		//$name	=	$name?	$name	:	$this->getActionName();
		$name = "Tplstyle";
		$Model	=	M($name);
		$list		=	$Model->order('isdefault desc, id desc')->select();
		$data		=	array();
		$alldata = array();
		foreach ($list as $key=>$val){
    		if(!$data)$data	=	$val;
    		$alldata[] = $val;
		}
				
		$savefile		=	DATA_PATH.'~tplstyle_all.php';
		$content		=   "<?php\nreturn ".var_export($alldata,true).";\n?>";
		$isCache = file_put_contents($savefile,$content);			

		$savefile		=	$this->getCacheFilename($name);
		// 所有参数统一为大写
		$content		=   "<?php\nreturn ".var_export(array_change_key_case($data,CASE_UPPER),true).";\n?>";
		if(file_put_contents($savefile,$content)){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
}
?>