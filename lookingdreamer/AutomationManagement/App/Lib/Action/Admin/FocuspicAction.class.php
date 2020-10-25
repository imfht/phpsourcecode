<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class FocuspicAction extends CommonAction {
    public function _before_insert() {
        $this->upload();
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
        if(!empty($_FILES['pic']['name'])) {
            import("ORG.Net.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,gif,png,jpeg');
            //设置附件上传目录
            $upload->savePath =  './Public/Uploads/Focus/';
            //$upload->thumb  =  true;
            //$upload->thumbMaxWidth =  200;
            //$upload->thumbMaxHeight = 124;
            //$upload->thumbPrefix   =  '';
            if(!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            }else{
                $info =  $upload->getUploadFileInfo();
                $_POST['pic'] = $info[0]['savename'];
            }
        }
    }
		
    public function _before_add() {
		$model	=	M("Datacall");
		$list	=	$model->where("calltype='focus'")->select();
		$this->assign("focuscodes",$list);
    }
    
    public function _before_edit() {
		$model	=	M("Datacall");
		$list	=	$model->where("calltype='focus'")->select();
		$this->assign("focuscodes",$list);
    }

	// 缓存文件
	public function cache($name='',$fields='') {
		//$name	=	$name?	$name	:	$this->getActionName();
		$Model	=	M("Focuspic");
		$list		=	$Model->order('id desc')->select();
		$data		=	array();
		foreach ($list as $key=>$val){
    		$data[$val["focuscode"]][$val['id']]	=	$val;
		}
		$savefile		=	$this->getCacheFilename("Focus");
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