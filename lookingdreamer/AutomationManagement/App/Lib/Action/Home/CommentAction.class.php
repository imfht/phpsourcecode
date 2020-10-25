<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class CommentAction extends BaseAction{
    
    // 留言首页
    public function index() {
        $this->_list('Comment',"modid=0 AND status=1 AND module<>'blog' AND module<>'cms'");
        $this->display();
    }
        
		Public function verify(){
			import("ORG.Util.Image"); 
			Image::buildImageVerify(); 
		}
		
		Public function showlist(){
				$mdname = trim($this->_get("mdname"));
				$id = intval($this->_get("id"));
				if(!$mdname || !$id)$this->error(L('您要查看的内容不存在或参数错误'));
				$model = M($mdname);
				$vo = $model -> find($id);
				if(!$vo  || $vo['status']   ==0 ) {
						$this->error('访问的信息不存在或已经删除！');
				}
				$this->title  =  $vo['title'];				
        $this->_list("Comment","status=1 AND modid=".$id." AND module='".$mdname."'");
        $this->assign('data',$vo);
        $this->assign('mdname',$mdname);
        $this->display();
		}
		
		Public function digg(){
				$digg = $_POST['dig'];
				$cid = $_POST['cid'];
				$model = M("Comment");
				$commentkey = md5('comment_'.$digg.$cid);
				if(cookie($commentkey)){
					$this->error('您已经'.(strtolower($digg)=='good'?'支持':'反对').'过了');
				}else{
					cookie($commentkey, true);
					$model -> where("id='".$cid."'") -> setInc('dig_'.$digg);
					$this->success('操作成功');
				}	
		}		
		
    function insert()
    {
    		$model = D(isset($_POST["insert_model"]) ? $_POST["insert_model"] : $this->getActionName());
				$_POST['status'] = C("COMMENT_CHECK");
				$_POST['contents'] = HtmlTrim($_POST['contents'], '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17');
				if($_SESSION['verify'] != md5($_POST['verify'])) {
					$this->error(L('验证码输入错误'));
				}
				if(!$_POST['title'])$_POST['title'] = cutstr($_POST['contents'], 20, '');
				preg_match('/#(\d+)#/', $_POST['contents'], $matches);
				if($matches[1]){
					$commentid = $matches[1];
					$_POST['contents'] = str_replace('#'.$commentid.'#', '', $_POST['contents']);
					$quotecomment = $model->where("id='".$commentid."'")->find();
					$_POST['contents'] = '<div class="commentQuote"><em>'.date('Y-m-d H:i', $quotecomment['create_time']).'</em><strong><I>'.$quotecomment['nickname'].':</I></strong>&nbsp; '.$quotecomment['contents'].'</div>'.$_POST['contents'];
				}
        if(false === $model->create()) {
        	$this->error($model->getError());
        }
        $data = $model->data();
        //保存当前数据对象
        if($result = $model->add()) { //保存成功
        		$modModel = M($data['module']);
        		$modModel->where("id='".$data['modid']."'")->setInc('comments');
            // 回调接口
            if(method_exists($this,'_tigger_insert')) {
                $model->id =  $result;
                $this->_tigger_insert($model);
            }
            //成功提示
            $this->assign('jumpUrl',cookie('_currentUrl_'));
            $this->success(L($_POST["moduletitle"].'发表成功'));
        }else {
            //失败提示
            $this->error(L($_POST["moduletitle"].'发表失败'));
        }
    }

}
?>