<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class FinancialCloudAction extends CommonAction {

		public function _filter(&$map)
		{
			if(!isset($map['pid']) ) {
				$map['pid']	=	0;
			}
			$_SESSION['currentServerDetailId']	=	$map['pid'];
			//获取上级节点
			$ServerDetail  = D("ServerDetail");
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

        //添加新的数据时赋值给模板,如:赋值添加菜单中  input name="{$name}"其中的$name以及label标签的中文名,中文名来自字段的注释。
        $modelname = $this->getActionName();
        $build_info= R('Admin/GetModule/checkmodel', array($modelname));
        if ($build_info) {
            $getmodelarray = R('Admin/GetModule/getmodule', array($build_info));
            $tranarr = model_trans_cn($modelname, $getmodelarray);
        } else {
            $tranarr = model_trans_cn($modelname);
        }
        $tb = strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $modelname)); //正则:驼峰转换为下划线
        $Info = M("columns", "", "DB_INFO");
        // 查询字段和注释对应关系
        $intb = C('DB_PREFIX') . $tb;
        $map ['table_name'] = $intb;
        $xlsCell = $Info->where($map)->table("columns")->field("column_name, column_comment")->select();

        $addarr = array();
        foreach ($xlsCell as $add) {
//               echo $add["column_comment"]."||". $add["column_name"]."<br/>";
            $keyval = $add["column_comment"];
            $addarr["$keyval"] = $add["column_name"];
        }
//            $tranarr=model_trans_cn($modelname);

        $selectarr=$tranarr["select_array"];

        //转换索引为索引数组
        $select_index=array();
        foreach($selectarr as $key=>$val){
            $select_index[]=$key;
        }
        $index_link = U("Admin/$modelname/index");
        $insert_link = U("Admin/$modelname/insert");

        $nowtime=toDate(NOW_TIME,$format='Y-m-d H:i:s');
        //传首页等链接在首页的顶端
        $transtring = $tranarr;

        $this->assign('transtring', $transtring);
        $this->assign('selectarr',$selectarr);
        $this->assign('select_index',$select_index);
        $this->assign('index_link',$index_link);  //取消按钮链接
        $this->assign('insert_link',$insert_link);  //提交按钮链接
        $this->assign('nowtime',$nowtime);         //初始时间
        $this->assign('addlist', $addarr);        //初始模板数据
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