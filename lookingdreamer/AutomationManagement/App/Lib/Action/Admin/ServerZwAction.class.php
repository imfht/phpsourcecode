<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class ServerZwAction extends CommonAction {

		public function _filter(&$map)
		{
			if(!isset($map['pid']) ) {
				$map['pid']	=	0;
			}
			$_SESSION['currentServerDetailId']	=	$map['pid'];
			//获取上级节点
			$ServerDetail  = D("ServerZw");
			if($ServerDetail->getById($map['pid'])) {
				$this->assign('level',$ServerDetail->level+1);
				$this->assign('parentId',$ServerDetail->pid);
				$this->assign('columnName',$ServerDetail->title);
			}else {
				$this->assign('level',1);
			}
		}
	
		public function _tigger_update($model) {
				$data = $model->data();
				if($data['modid'] && $data['name']){
					$M = M($data['name']);
					$M->where("id='".$data['modid']."'")->data(array("ServerDetailpos" => $data['position']))->save();
				}
		}	
	
		public function add()
		{
			$Server_detail	=	D("ServerZw");
			$Server_detail->getById($_SESSION['currentServerDetailId']);
	        $this->assign('pid',$Server_detail->id);
			$this->assign('level',$Server_detail->level+1);
			$this->display();
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
		$node = M('ServerDetail');
        if(!empty($_GET['sortId'])) {
            $map = array();
            $map['status'] = 1;
            $map['id']   = array('in',$_GET['sortId']);
            $sortList   =   $node->where($map)->order('sort asc')->select();
        }else{
            if(!empty($_GET['pid'])) {
                $pid  = $_GET['pid'];
            }else {
                $pid  = $_SESSION['currentServerDetailId'];
            }
            if($node->getById($pid)) {
                $level   =  $node->level+1;
            }else {
                $level   =  1;
            }
            $this->assign('level',$level);
            $sortList   =   $node->where('status=1 and pid='.$pid.' and level='.$level)->order('sort asc')->select();
        }
        $this->assign("sortList",$sortList);
        $this->display();
        return ;
    }

	// 缓存配置文件
	public function cache() {
		$ServerDetail		=	M("ServerDetail");
		$templist	=	$ServerDetail->where('status=1')->order('position DESC, sort ASC')->select();
		foreach($templist as $val){
			if($val['modid']){
				$Modules[$val['name']][] = $val['modid'];
			}
		}
		if($Modules){
			foreach($Modules as $key => $ids){
				$M = M($key);
				$list = $M -> where("id IN(".implode(",", $ids).")")->select();
				foreach($list as $vo){
					$ModList[$key][$vo['id']] = $vo;
				}
			}
		}
		foreach($templist as $val){
			$val['module'] = $val['name'];
			if($val['modid']){
				$vo = $ModList[$val['name']][$val['modid']];
				if($vo){
					$val['moduledata'] = $vo;
				}else{
					continue;
				}
			}
			$list[$val['id']] = $val;
			$ServerDetaillist[$val['position']][$val['id']] = $val;
		}
		$savefile		=	DATA_PATH.'~ServerDetail.php';
		// 所有配置参数统一为大写
		$content		=   "<?php\nreturn ".var_export($list,true).";\n?>";
		$iscache = file_put_contents($savefile,$content);
		foreach($ServerDetaillist as $key => $val){
			$savefile		=	DATA_PATH.'~ServerDetail_'.$key.'.php';
			// 所有配置参数统一为大写
			$content		=   "<?php\nreturn ".var_export($val,true).";\n?>";
			$iscache = file_put_contents($savefile,$content);			
		}
		if($iscache){
			$this->success('缓存生成成功！');
		}else{
			$this->error('缓存失败！');
		}
	}
}
?>