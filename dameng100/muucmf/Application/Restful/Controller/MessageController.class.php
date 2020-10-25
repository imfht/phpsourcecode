<?php
/**
 * 消息接口控制器
*/
namespace Restful\Controller;

use Think\Controller\RestController;


class MessageController extends BaseController {

    protected $codeModel;
    protected $Model;
	
    function _initialize()
    {
    	parent::_initialize();
    	$this->codeModel = D('Restful/Code');
        $this->Model = D('Common/Message');
    }
    //获取用户消息类型
    //获取某类型所有消息
    //获取新消息数量
    public function messages($page = 1,$r = 20){
    	$this->_needLogin();
		$action = I('action','','text');
		
		if($action==='list'){
			//获取某类型标识的消息类型详情
        	$type = I('type','','text'); //消息类型
        	$map['to_uid'] = is_login();
        	if(!empty($type)){
        		$map['type'] = $type;
        	}
            
            $messages = D('Message')->where($map)->order('create_time desc')->page($page, $r)->select();
            $totalCount = D('Message')->where($map)->order('create_time desc')->count(); //用于分页
            foreach ($messages as &$v) {
                D('Common/Message')->readMessage($v['id']);//设置这个消息为已读
                $v['content'] = D('Common/Message')->getContent($v['content_id']);
                if ($v['from_uid'] != 0) {
                    $v['from_user'] = query_user(array('nickname', 'space_url', 'avatar64', 'space_link'), $v['from_uid']);
                }
                if($v['content']['url']) {
                    if(preg_match('/^(http|https).*$/',$v['content']['url'])){
                        $v['from']=$v['content']['url'];
                    }else{
                        if($v['content']['args']){
                            $model = explode('/',$v['content']['url']);
                            $v['module']=ucwords($model[0]);
                            $map=json_decode($v['content']['args']);
                            $from = M($v['module'])->where($map)->find();
                            if($from){
                                $v['from_module']=$from;
                            }
                        }else{
                            $v['from']=U($v['content']['url']);
                        }
                    };
                }
            }
            unset($v);
        	
        	$result = $this->codeModel->code(200);
        	$result['data'] = $messages;
        	$this->response($result,$this->type);
		}
        
        //获取用户消息类型列表
        if($action==='type'){
        	$messageTypeList= D('Common/Message')->getMyMessageTypeList();
        	$result = $this->codeModel->code(200);
        	$result['data'] = $messageTypeList;
        	$this->response($result,$this->type);
        }
    }



   
    public function detail()
    {
        switch ($this->_method){
            case 'get': //get请求处理代码
				$id = I('id',0,'intval');
					$map['id']=$id;
					$map['status']=1;
					$data=$this->Model->where($map)->find();
						$data['toUser']=query_user(array('uid','avatar32','avatar64','nickname'),$data['to_uid']);
						$data['fromUser']=query_user(array('uid','avatar32','avatar64','nickname'),$data['from_uid']);
						$contentId['id'] = $data['content_id'];
						$data['content']=$this->ModelContent->where($contentId)->find();
						$isRead['is_read'] = 1;
						$this->Model->where($map)->save($isRead); 
						if(!empty($data['content']['url'])){//获取消息的来源内容
						if(!empty($data['content']['args'])){
							$url = $data['content']['url'];
							$n = strpos($url,'/');
							if ($n) $str=substr($url,0,$n);//获取模型名称
							unset($n);
							$data['formModelName'] = $str;
							$args = $data['content']['args'];
							$n = strpos($args,'":');
							if ($n) $idName = substr($args,2,$n-2);//获取id名，Uid或id
							unset($n);
							$n = strpos($args,':"');
							$m = strpos($args,'"}');
							if ($n) $id = substr($args,$n+2,$m-$n-2);//获取id值
							unset($n);
							unset($m);
							if($idName=='id'){
								if($str=='News'){
									$map['id'] = $id;
									$data['fromModelInfo']= M('News')->where($map)->find();
									$data['fromModelInfo']['Thumbnail'] = getThumbImageById($data['fromModelInfo']['cover'],352,240);
								}
								if($str=='Resources'){
									$map['id'] = $id;
									$data['fromModelInfo']= M('Resources')->where($map)->find();
									$data['fromModelInfo']['Thumbnail'] = getThumbImageById($data['fromModelInfo']['cover'],352,240);
								}
								if($str=='Design'){
									$map['id'] = $id;
									$data['fromModelInfo']= M('Design')->where($map)->find();
									$data['fromModelInfo']['Thumbnail'] = getThumbImageById($data['fromModelInfo']['cover'],352,240);
								}
								if($str=='Discovery'){
									$map['id'] = $id;
									$data['fromModelInfo']= M('Discovery')->where($map)->find();
								}
							}
						}
						}
						
					$result['info'] = '返回成功';
					$result['data'] = $data;
					$result['code'] = 200;
				$this->response($result,$this->type);
            break;
        }  
    }
    
}