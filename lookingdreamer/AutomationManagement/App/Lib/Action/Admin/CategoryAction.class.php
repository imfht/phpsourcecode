<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class CategoryAction extends CommonAction {
	
		public function _tigger_insert($model) {
				$this->saveNav($model->title,$model->menupos,$model->id);
		}

		public function _tigger_update($model) {
				$this->saveNav($model->title,$model->menupos,$model->id);
		}	
	
		public function index()
	  {
	      //列表过滤器，生成查询Map对象
	      $map = $this->_search();
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
				$model = M($this->getActionName());
	      if(!empty($model)) {
	        	$this->_list($model,$map);
	      }
	      $modules = $this->modules;
	      if(!$_GET['module'])$this->error('模块错误！');
	      $module=$modules[strtolower($_GET['module'])];
	      $this->assign('module',$module);
				$this->display();
	      return;
	  }
	
		public function _filter(&$map)
		{
			if(!isset($map['pid']) ) {
				$map['pid']	=	0;
			}
			$_SESSION['currentCategoryId']	=	$map['pid'];
			//获取上级节点
			$Category  = D("Category");
			if($Category->getById($map['pid'])) {
				$this->assign('level',$Category->level+1);
				$this->assign('parentId',$Category->pid);
				$this->assign('columnName',$Category->title);
			}else {
				$this->assign('level',1);
			}
		}
	
		public function add()
		{
			$Category	=	D("Category");
			$Category->getById($_SESSION['currentCategoryId']);
			if(!$Category->id)$Category->module=$_GET['module']; 
		  $modules = $this->modules;
		  $module=$modules[strtolower($_GET['module'])];
		  $this->assign('groupName',$module['title']);		  
			$this->assign('parentCat',$Category->title);
			$this->assign('parentMod',$Category->module);
	    $this->assign('pid',$Category->id);
			$this->assign('level',$Category->level+1);
			$this->display();
		}
	
    public function delete()
    {
        //删除指定记录
        $model = M($this->getActionName());
        if(!empty($model)) {
						$pk	=	$model->getPk();
            //$id = $_REQUEST[$pk];
            $id = $_POST[$pk];
            $ids = $_POST['ids'];
            $del_ids = array();
            if($id)$del_ids[] = $id;
            if($ids)$ids = explode(',', $ids); 
            if(isset($del_ids)) {
            		$category		=	include_once DATA_PATH.'~category.php';
            		$childs = array();       		
            		foreach($del_ids as $val){
            			if(!$val['level'])continue;
            			$childs[] = $val;
            			if($category[$val]['childids'])$childs[] = $category[$val]['childids'];
            		}
            		$catids = implode(',', $childs);
                $condition = array($pk=>array('in',explode(',',$catids)));
                if(false !== $model->where($condition)->delete()){
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
				$Category = M('Category');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $Category->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentCategoryId'];
            }
            if($Category->getById($pid)) {
                $level   =  $Category->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $Category->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }
    
		// 更新分类数量统计
		public function updatecounts() {
				$module = $_GET['module'];
				if(!$module)$this->error('模块错误');
				$Model =	M($module);
				$Category	=	M("Category");
				$list = $Model -> field("catid, count(*) as count") -> where("1") -> group("catid") -> select();
				foreach($list as $val){
						$totalcount += $val['count'];
						$Category -> data(array('counts' => $val['count'])) -> where("id='".$val['catid']."'") -> save();	
				}
				$Category -> data(array('counts' => $totalcount)) -> where("module='".$module."' AND pid=0 AND level=0") -> save();	
				$this->success('统计更新成功！');
		}

		// 缓存分类文件
		public function cache() {
				$Category		=	M("Category");
				$templist			=	$Category->where('status=1')->order('module, pid, sort')->select();
				$listTree = list_to_tree($templist);
				foreach($templist as $val){
					$list[$val["id"]] = $val;
				}
				foreach($list as $val){
					$val["parentids"] = getParentIDs($list, $val["id"]);
					$val["childids"] = getChildIDs($listTree, $val["parentids"]);
					if($val['level']){
						$catlist[$val["id"]] = $val;
						$ModuleCat[$val["module"]][$val["id"]] = $val;
					}else{
						$ModuleList[strtolower($val['module'])] = $val;
						$catlist[$val["module"]] = $val;
						$ModuleCat[$val["module"]][$val["module"]] = $val;					
					}
				}
				foreach($ModuleCat as $key => $val){
					$savefile		=	DATA_PATH.'~category_'.strtolower($key).'.php';
					$content		=   "<?php\nreturn ".var_export($val,true).";\n?>";
					$isCache = file_put_contents($savefile,$content);			
				}
				$savefile		=	DATA_PATH.'~category.php';
				$content		=   "<?php\nreturn ".var_export($catlist,true).";\n?>";
				$isCache = file_put_contents($savefile,$content);
				$savefile		=	DATA_PATH.'~modulelist.php';
				$content		=   "<?php\nreturn ".var_export($ModuleList,true).";\n?>";
				$isCache = file_put_contents($savefile,$content);					
				if($isCache){
					$this->success('缓存生成成功！');
				}else{
					$this->error('缓存失败！');
				}
		}
}
?>