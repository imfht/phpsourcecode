<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class ArticleAction extends CommonAction {
		
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
		
		public function _before_add(){
				$wd = $_GET['wd'];
				if($wd && !$this->swlist){
					$start = '<p class="res">';
					$end = '<em class=p2></em><br><br>';
					$url = getBaiduUrl($wd, $bs,4);
					$html = GetContent($url);
					$html = ConvertChatSet($html,'gb2312','utf-8');
					$html = GetField($html, $start, $end, 0);
					$url = getBaiduUrl($wd, $bs,4,1);
					$html2 = GetContent($url);
					$html2 = ConvertChatSet($html2,'gb2312','utf-8');
					$html2 = GetField($html2, $start, $end, 0);
					$html .= $html2;
					$listtext = GetField($html, '<span>&#8226;&nbsp;<a href=http://(*)  target=_blank>', '</a>&nbsp;(*)<br></span>', 40);
					$listhref = GetField($html, '<span>&#8226;&nbsp;<a href=http://', '  mon="(*)"  target=_blank>', 40);
					$shtml = '';
					foreach($listtext as $key => $txt){
						$shtml .= '<li><a href="http://'.$listhref[$key].'" target="_blank">'.$txt.'</a></li>';
					}
					$shtml = '<div class="result">长尾关键词“<font color=red>'.$_GET['wd'].'</font>”新闻参考</div><ul>'.$shtml.'</ul>';
					$this->swlist = $shtml;
				}
		}

    function top()
    {
        //置顶指定记录
        $Blog        = D("New");
        $id         = $_REQUEST['id'];
        if(isset($id)) {
            $condition = array('id'=>array('in',$id));
            if($Blog->top($condition)){
				$this->assign("jumpUrl",$this->getReturnUrl());
				$this->success('置顶成功！');
            }else {
                $this->error('置顶失败');
            }
        }else {
        	$this->error('非法操作');
        }
    }
}
?>