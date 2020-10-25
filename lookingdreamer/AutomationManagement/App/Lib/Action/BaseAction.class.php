<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

/**
 +----------------------------------------------------------
 * 项目基类
 +----------------------------------------------------------
 */
class BaseAction extends Action {

    /**
     +----------------------------------------------------------
     * 项目初始化
     +----------------------------------------------------------
     */
    function _initialize()
    {
//     		if(C('WEB_TURN_OFF'))$this->error(C('WEB_OFF_MSG'));
    		if(C('WEB_TURN_OFF')){
    			$this->success(C('WEB_OFF_MSG'), 'Admin/Index/index');
    			exit();
    		}
//     		if(!file_exists(RUNTIME_PATH.'install.lock')){redirect('/install/');}
				$this->getStyle();
        // 获取导航列表
        $nav   =  include DATA_PATH.'~menu.php';
        $CacheFile = DATA_PATH.'~category_'.strtolower($this->getActionName()).'.php';
        if(file_exists($CacheFile)){
        	$category = include_once($CacheFile);
        	$this->catlist = $category;
					$category = list_to_tree($category);
					$this->category = $category;
        }
        $seokey = explode(",", C("SEOKEY"));
        foreach($seokey as $val){
        	$footseokey[] = array("name" => $val);
        }
        $this->FOOTSEOKEY = $footseokey;        
        $this->check_online();
        $catid = intval($this->_get("catid"));
        $this->assign('catid',$catid);
        $this->assign('navigation',$nav);
        $this->assign("modname", MODULE_NAME);
        $this->logo = __PUBLIC__.'/Uploads/Config/'.C('COMPANY_LOGO');
	      import("ORG.Util.Spider");
	      $spider = new spider();
	      $spider->writelog();
    }

    // 404 错误定向
    protected function _404($message='',$jumpUrl='',$waitSecond=3) {
    	if(file_exists("404.html")){
    		$this->display("404.html");		
    	}else{
	        exit("the Page can't be found!");
	    }
    }
    
    protected function getStyle(){
    		$style   =  include DATA_PATH.'~tplstyle.php';
				C("TPLID", $style["TPLDIR"]);
				C("STYLEID", $style["STYLEDIR"]);
				if($_GET["tplid"])C("TPLID", $_GET["tplid"]);
				if($_GET["styleid"])C("STYLEID", $_GET["styleid"]);
				
        /* 获取模板主题名称 */
        $templateSet =  $style["TPLDIR"];
        $group   =  defined('GROUP_NAME')?GROUP_NAME.'/':'';
        /* 模板相关目录常量 */
        define('USER_THEME_NAME',   $templateSet);                  // 当前模板主题名称
        define('USER_THEME_PATH',   TMPL_PATH.$group.(USER_THEME_NAME?USER_THEME_NAME.'/':''));
        define('USER_APP_TMPL_PATH',__ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').basename(TMPL_PATH).'/'.$group.(USER_THEME_NAME?USER_THEME_NAME.'/':''));
        C('TEMPLATE_NAME',USER_THEME_PATH.MODULE_NAME.(defined('GROUP_NAME')?C('TMPL_FILE_DEPR'):'/').ACTION_NAME.C('TMPL_TEMPLATE_SUFFIX'));
        C('CACHE_PATH',CACHE_PATH.$group);
    }
    
    protected function check_online(){
    		$IS_SERVICE_ONLINE = C("IS_SERVICE_ONLINE");
    		$kefu_tpl = "Default:Public:".trim(C("KEFU_PATTERN"));
    		if(!$IS_SERVICE_ONLINE)return false;
        $list   =  include DATA_PATH.'~online.php';
				$day = date("Y-m-d",time());
				$day_full = date("Y-m-d H:i:s",time());
				$week = date('w',strtotime($day));
				$week = $week==0 ? 7 : $week;
				$day_kefu = $list[$week];
				$kefu_count = 0;
				foreach($day_kefu as $val){
					$start_time = $day." ".date("H:i:s", $val["start_time"]);
					$end_time = $day." ".date("H:i:s", $val["end_time"]);
					if(strtotime($day_full)>=strtotime($start_time) && strtotime($day_full)<=strtotime($end_time)+1){
						$kefu_list[] = $val;
						if(!$kefu_qq && $val["qq"])$kefu_qq = $val["qq"];
						if(!$kefu_tel && $val["tel"])$kefu_tel = $val["tel"];
						$kefu_count++;
					}
				}
        $this->assign('KEFU_TPL',$kefu_tpl);
        $this->assign('KEFU_LIST',$kefu_list);
        $this->assign('KEFU_QQ',$kefu_qq);
        $this->assign('KEFU_TEL',$kefu_tel);
        $this->assign('KEFU_COUNT',$kefu_count);
    }

    // 查看某个模块的标签相关的记录
    public function tag()
    {
        $Tag = M("Tag");
        $tag=trim($_GET['tag']);
        $map['module']   =  $this->getActionName();
        $map['name'] =  $tag;
        $Stat  = $Tag->where($map)->field("id,count")->find();
        $tagId  =  $Stat['id'];
        $Tagged = M("Tagged");
        $map = array();
        $map['module']   =  $this->getActionName();
        $map['tag_id'] =  $tagId;
        $recordIds  =  $Tagged->where($map)->getField('id,record_id');
        if($recordIds) {
            $map = array();
            $map['id']   = array('IN',$recordIds);
            $map['status'] = 1;
            $model = M($this->getActionName());
            $list   =  $model->where($map)->select();
            $this->assign('list',$list);
        }
        $this->assign('tag',$tag);
        $this->assign('title',$tag);
        $this->display('tag');
    }

    public function download()
    {
        $id         =   intval($_GET['id']);
        $Attach        =   M("Attach");
        if($Attach->getById($id)) {
            $filename   =   $Attach->savepath.$Attach->savename;
            if(is_file($filename)) {
                $showname = auto_charset($Attach->name,'utf-8','gbk');
                if(!isset($_SESSION['download_'.$id])) {
                    $data['download_count'] = array('exp','download_count+1');
                    $data['id']   = $id;
       							$Attach->data($data)->save();
                    $_SESSION['download_'.$id]   =  true;
                }
		        		import("ORG.Net.Http");
                Http::download($filename,$showname);
            }else{
                $this->error('附件不存在或者已经删除！');
            }
        }else{
            $this->error('附件不存在或者已经删除！');
        }
    }

		protected function getAttach($id,$module='') {
	        //读取附件信息
	        $module = empty($module)?$this->getActionName():$module;
	        $Attach = M('Attach');
	        $attachs = $Attach->where("status=1 and module='".$module."' and record_id=$id")->order('id desc')->select();
			//模板变量赋值
			$this->assign("attachs",$attachs);
		}

    /**
     +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    protected function _list($modelName='',$map='',$sortBy='',$asc=false)
    {
        $modelName  = empty($modelName)?$this->getActionName():$modelName;
        $model  =  M($modelName);
        if(!$_GET["p"])$_GET["p"] = 1;
        $perpage = C("PER_PAGE");
        if(!$perpage)$perpage = 1;
        $catlist   =  include DATA_PATH.'~category_'.strtolower($modelName).'.php';
        $CurModule = $catlist[$modelName];
        if(!$_GET['catid'] || !is_numeric($_GET['catid'])){
        	$_GET['catid'] = 0;
        	$cat = $CurModule;
        }else{
	        $cat = $catlist[$_GET['catid']];
	      }
				$KEYWORDS = array();
				$DESCRIPTION = array();
				$TITLE[] = $cat['title'];
				if($cat['description'])$DESCRIPTION[] = $cat['description'];
				if($cat['keywords']){
					$KEYWORDS = explode(',', $cat['keywords']);
					foreach($KEYWORDS as $keyword){
						if($keyword && $cat['keywords_in_title'] && !in_array($keyword, $TITLE))$TITLE[] = $keyword;
						if($keyword && $cat['keywords_in_description'] && !in_array($keyword, $DESCRIPTION))$DESCRIPTION[] = $keyword;
					}
				}
				if($cat['urlwords']){
					$cat['urlwords'] = explode(',', $cat['urlwords']);
					foreach($cat['urlwords'] as $urlword){
						if($urlword && $cat['urlwords_in_title'] && !in_array($urlword, $TITLE))$TITLE[] = $urlword;
						if($urlword && $cat['urlwords_in_description'] && !in_array($urlword, $DESCRIPTION))$DESCRIPTION[] = $urlword;
						if($urlword && $cat['urlwords_in_keywords'] && !in_array($urlword, $KEYWORDS))$KEYWORDS[] = $urlword;
					}
				}
				if($cat['title_in_keywords'] && !in_array($cat['title'], $KEYWORDS))$KEYWORDS[] = $cat['title'];
				if($cat['title_in_description'] && !in_array($cat['title'], $DESCRIPTION))$DESCRIPTION[] = $cat['title'];
				$this->WEBTITLE  =  implode(C('TITLE_PATHINFO_DEPR'),$TITLE);
				$this->KEYWORDS  =  implode(',',$KEYWORDS);
				$this->DESCRIPTION  =  implode(',',$DESCRIPTION); 
        $this->CurModule = $CurModule;
        
        //排序字段 默认为主键名
        if(isset($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        }else {
            $order = !empty($sortBy)? $sortBy: $model->getPk();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if(isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort']?'asc':'desc';
        }else {
            $sort = $asc?'asc':'desc';
        }
        $whereArr[] = 1;
        $whereArr[] = "create_time<'".C("NOW_TIME")."'";
        if($map)$whereArr[] = $map;
        if($_GET['catid']){
        	$orsql[] = "catid='".$_GET['catid']."'";
        	$orsql[] = "catstr LIKE '%,".$_GET['catid'].",%'";
        	if($cat['childids']){
        		$childids = explode(',', $cat['childids']);
        		foreach($childids as $cid){
		        	$orsql[] = "catid='".$cid."'";
		        	$orsql[] = "catstr LIKE '%,".$cid.",%'";
        		}
        	}
        	$whereArr[] = "(".implode(' OR ', $orsql).")";
        }
        if($_GET['kw'] && $_GET[$_GET['kw']]){
        	$whereArr[] = $_GET['kw']." LIKE '%".$_GET[$_GET['kw']]."%'";
        }
        $map = implode(' AND ', $whereArr);
        //取得满足条件的记录数
        $count = $model->where($map)->count('id');
				if($count>0) {
						//分页显示
		        import("ORG.Util.Page");
		        $p    = new Page($count, $perpage);
		        $p->setConfig('theme', '%upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
		        $page = $p->show();
		        $list = $model->order($order.' '.$sort)->where($map)->page($_GET['p'].','.$perpage)->select();
		        $this->assign('page',$page);
		        $this->assign('recordcount',$count);
		        $this->assign('list',$list);
				}
        cookie('_currentUrl_',__SELF__);
        return ;
    }
    
    public function read($id, $model) {
	    	$model  = empty($model)?$this->getActionName():$model;
				// 查看具体的信息内容
				$mo = M($model);
				$vo   =  $mo->where("create_time<'".time()."' AND id='".$id."'")->find();
				if(!$vo  || $vo['status']   ==0 ) {
						$this->error('访问的信息不存在或已经删除！');
				}
				$PrevTitle   =  $mo->where('id<'.$id)->order('id DESC')->find();
				$NextTitle   =  $mo->where('id>'.$id)->order('id ASC')->find();
				$this->PrevTitle  =  $PrevTitle;
				$this->NextTitle  =  $NextTitle;
        $catlist   =  include DATA_PATH.'~category_'.strtolower($model).'.php';
        $CurModule = $catlist[$model];
        $this->CurModule = $CurModule;
				$KEYWORDS = array();
				$DESCRIPTION = array();
				$TITLE[] = $vo['title'];
				if($vo['description'])$DESCRIPTION[] = $vo['description'];
				if($vo['keywords']){
					$KEYWORDS = explode(',', $vo['keywords']);
					foreach($KEYWORDS as $keyword){
						if($keyword && $vo['keywords_in_title'] && !in_array($keyword, $TITLE))$TITLE[] = $keyword;
						if($keyword && $vo['keywords_in_description'] && !in_array($keyword, $DESCRIPTION))$DESCRIPTION[] = $keyword;
					}
				}
				if($vo['urlwords']){
					$vo['urlwords'] = explode(',', $vo['urlwords']);
					foreach($vo['urlwords'] as $urlword){
						if($urlword && $vo['urlwords_in_title'] && !in_array($urlword, $TITLE))$TITLE[] = $urlword;
						if($urlword && $vo['urlwords_in_description'] && !in_array($urlword, $DESCRIPTION))$DESCRIPTION[] = $urlword;
						if($urlword && $vo['urlwords_in_keywords'] && !in_array($urlword, $KEYWORDS))$KEYWORDS[] = $urlword;
					}
				}
				if($vo['seokey']){
					if($vo['seokey'] != $vo['title']){
						if($vo['seokey_in_title'] && !in_array($vo['seokey'], $TITLE))$TITLE[] = $vo['seokey'];
						if($vo['seokey_in_description'] && !in_array($vo['seokey'], $DESCRIPTION))$DESCRIPTION[] = $vo['seokey'];
						if($vo['seokey_in_keywords'] && !in_array($vo['seokey'], $KEYWORDS))$KEYWORDS[] = $vo['seokey'];
					}
					$this->FOOTSEOKEY  =  array(
						array("link" => getReadUrl($vo["id"], $vo, $model, 1), 'name' => $vo['seokey'])
					);
				}
				if($vo['title_in_keywords'] && !in_array($vo['title'], $KEYWORDS))$KEYWORDS[] = $vo['title'];
				if($vo['title_in_description'] && !in_array($vo['title'], $DESCRIPTION))$DESCRIPTION[] = $vo['title'];
				$this->WEBTITLE  =  implode(C('TITLE_PATHINFO_DEPR'),$TITLE);
				$this->KEYWORDS  =  implode(',',$KEYWORDS);
				$this->DESCRIPTION  =  implode(',',$DESCRIPTION);
				if(($vo['seokey'] || $KEYWORDS) && $vo["content"]){
						import("ORG.Util.Seokey");
						$seokey = array();
						if($vo['seokey']){
							$seokey[] = array('Key' => '<a href="'.getReadUrl($vo["id"], $vo, $model, 1).'">'.$vo['seokey'].'</a>', 'Href' => '<STRONG>'.$vo['seokey'].'</STRONG>', 'ReplaceNumber' => 1);
							$seokey[] = array('Key' => $vo['seokey'], 'Href' => '<STRONG>'.$vo['seokey'].'</STRONG>', 'ReplaceNumber' => 1);
						}
						if(!in_array($vo['title'],$KEYWORDS) && $vo['seokey'] != $vo['title'])$seokey[] = array('Key' => $vo['title'], 'Href' => '<STRONG>'.$vo['title'].'</STRONG>', 'ReplaceNumber' => 1);
						foreach($KEYWORDS as $val){
							if($val != $vo['seokey'])$seokey[] = array('Key' => $val, 'Href' => '<STRONG>'.$val.'</STRONG>', 'ReplaceNumber' => 1);
						}
						if($seokey){
							$Rep = new Seokey($seokey,$vo["content"]);
							$Rep->KeyOrderBy();
							$Rep->Replaces();
							$vo["content"] = $Rep->HtmlString;
						}
				}
    		if(strtolower(C('SEOKEY_TIME'))=='read'){
	    			$seokey_list = include(DATA_PATH.'~seokey.php');
	    			import("ORG.Util.Seokey");
						$Rep = new Seokey($seokey_list,$vo["content"]);
						$Rep->KeyOrderBy();
						$Rep->Replaces();
						$vo["content"] = $Rep->HtmlString;
    		}				
				// 获取评论内容
				$CommentModel = M("comment");
				$recordcount = $CommentModel -> where("status=1 AND module='".$model."' AND modid=".$id) -> count("id");
				$list = $CommentModel -> where("status=1 AND module='".$model."' AND modid=".$id) -> limit(20) -> order("create_time DESC") -> select();
				// 获取最新动态
				//$lastestlist   =  include DATA_PATH.'~'.strtolower($model).'.php';
				$this->assign('list',$list);
				$this->assign('recordcount',$recordcount);
				//$this->assign('lastestlist',$lastestlist);
				$this->assign('data',$vo);
				$this->display('read');
    }
    
    //浏览信息
    public function _empty($method){
        if(is_numeric($method)) {
        		$this->read($method);
        }else{
            $this->error('错误操作');
        }
    }    

    public function _empty0($method) {
        $this->assign('message','非法操作！');
        $this->display(C('TMPL_ACTION_ERROR'));
    }
// Base end
}
?>