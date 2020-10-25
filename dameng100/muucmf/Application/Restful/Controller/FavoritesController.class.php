<?php
/**
 * 收藏插件接口控制器
*/
namespace Api\Controller;

use Think\Controller\RestController;


class FavoritesController extends BaseController {

    protected $Model;
    protected $codeModel;

    function _initialize()
    {
    	parent::_initialize();
        $this->codeModel = D('Restful/Code');
        $this->Model = D('Addons://Favorites/Favorites');
    }
	//
    public function index($page=1,$r=6)
    {
        switch ($this->_method){
            case 'get': //get请求处理代码
                
				$row = I('rowid',0,'intval');
                $uId = I('uid',0,'intval');
				$appname = I('app','','text');
					$map['status']=1;
					if($uId && $uId!=0){
						$map['uid']=$uId;
					}
					if($appname){
						$map['appname']=$appname;	
					}
					if($row && $row!=0){
						$map['row']=$row;
					}
					$order='create_time desc';
					$totalCount=$this->Model->where($map)->count();
						if($totalCount){
							$data=$this->Model->where($map)->page($page,$r)->order($order)->select();
						}
					foreach($data as &$val){
						$val['User']=query_user(array('uid','avatar32','avatar64','nickname'),$val['uid']);
						$contentId['id'] = $val['row'];
						if($val['appname']=='News'){
							$val['content']=M('News')->where($contentId)->find();
						}else if($val['appname'] == 'Resources'){
							$val['content']=M('Resources')->where($contentId)->find();
						}else if($val['appname'] == 'Design'){
							$val['content']=M('Design')->where($contentId)->find();
						}else{
							$val['content']=M('Discovery')->where($contentId)->find();
						}
						$val['Thumbnail'] = getThumbImageById($val['content']['cover'],352,240);
					}
					unset($val);
					$result = $this->codeModel->code(200);
					$result['totalCount'] = $totalCount;
					$result['data'] = $data;
				
				$this->response($result,$this->type);
            break;

            case 'post'://post请求处理代码,写入评论内容
			
			//验证用户登陆状态
			$this->_needLogin();

				$aUid = is_login();
				$aApp = I('post.app','',op_t);
				$aRowid = I('post.rowid',0,intval);
				$aTable = strtolower($aApp);
				$aCreateTime = time();
				//判断是否已经收藏过
				$map['appname'] = ucfirst($aApp);//首字母大写
				$map['row'] = $aRowid;
				$map['uid'] = $aUid;
				$data=$this->Model->where($map)->select();
				if($data){
					$result = $this->codeModel->code(200);
					$result['info'] = '已经收藏过了';
				}else{
					$data = array('appname'=> $aApp, 'uid' => $aUid,'row'=>$aRowid,'create_time' => $aCreateTime, 'table' => $aTable);
					$data = $this->Model->create($data);
					if (!$data) return false;
						$this->Model->add($data);
					$result = $this->codeModel->code(200);
					$result['info'] = '收藏成功';
				}
			$this->response($result,$this->type);
            break;
        }
    }
    
}