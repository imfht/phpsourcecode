<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class DownAction extends CommonAction {

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
    
    public function _tigger_upload($file) {
				if($file['key'] == 'attachfile'){
					$_POST['filename'] = $file['name'];
					$_POST['size'] = $file['size'];
					$_POST['extension'] = strtoupper($file['extension']);
				}
    }
    
}
?>