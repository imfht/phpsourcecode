<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class PagesAction extends CommonAction {
    public function _before_insert() {
    		$this->seokeyReplace();
        $this->upload();
    }
    public function _before_update() {
    		$this->seokeyReplace();
        $this->upload();
    }

		public function _tigger_insert($model) {
			$this->saveUrl($model->url,$model->id);
			$this->saveSeokey($model->seokey,$model->id);
			$this->saveNav($model->title,$model->menupos,$model->id);
        	$this->saveTag($model->tags,$model->id);
		}

		public function _tigger_update($model) {
			$this->saveUrl($model->url,$model->id);
			$this->saveSeokey($model->seokey,$model->id);
			$this->saveNav($model->title,$model->menupos,$model->id);
        	$this->saveTag($model->tags,$model->id);
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
		$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M($name);
		$list		=	$Model->limit(3)->order('id desc')->select();
		$data		=	array();
		foreach ($list as $key=>$val){
    		$data[$val[$Model->getPk()]]	=	$val;
		}
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