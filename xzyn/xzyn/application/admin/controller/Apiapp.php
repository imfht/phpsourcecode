<?php
namespace app\admin\controller;

use app\common\model\ApiApp as ApiApps;
use app\common\model\ApiApptoken;
use expand\Str;

class Apiapp extends Common {

	private $cModel;   //当前控制器关联模型

    public function initialize(){
        parent::initialize();
		$this->cModel = new ApiApps;
    }

    public function index() {
    	$where = [];
        if (input('get.search')){
            $where[] = ['app_name|app_id|app_info','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'id desc';
        }
		$dataList = $this->cModel->where($where)->order($order)->paginate(15);
		$this->assign('dataList',$dataList);
        return $this->fetch();

    }

    public function create() {	//新增
    	if (request()->isPost()){
			$data = input('post.');
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				// 验证失败 输出错误信息
				return ajaxReturn($result);
			}else{
            	$result = $this->cModel->allowField(true)->save($data);
			}
			if( $result ){
				return ajaxReturn('操作成功', url('index'));
			}else{
				return ajaxReturn('操作失败');
			}
		}else{
			$data['app_id'] = Str::randString(8, 1);
	        $data['app_secret'] = Str::randString(32);
	        $data['id'] = '';
	    	$this->assign('data',$data);
	        return $this->fetch('edit');
		}
    }

    public function edit($id) {	//编辑
    	if (request()->isPost()){
			$data = input('post.');
			if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);
			}else{
				$result = $this->validate($data,C_NAME.'.edit');
			}
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
			if( $result ){
				return ajaxReturn('操作成功', url('index'));
			}else{
				return ajaxReturn('操作失败');
			}
		}else{
			$data = $this->cModel->get( ['id'=>$id] );
			$this->assign('data',$data);
        	return $this->fetch();
		}
    }

    public function delete($id) {	//删除
    	if (request()->isPost()){
			$id = input('id');
			if (isset($id) && !empty($id)){
				$id_arr = explode(',', $id);
				$where[] = [ 'id','in', $id_arr ];
				$apptoken = new ApiApptoken;
				foreach ($id_arr as $k => $v) {
					$appid = $this->cModel->where(['id'=>$v])->find();
					$apptoken->where([ ['app_id','=',$appid['app_id']] ])->delete();
				}
                $result = $this->cModel->where($where)->delete();
				if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
			}
		}
    }

    public function apptoken($appid) {	//应用appToken列表
		$apptoken = new ApiApptoken;
		$token_arr = $apptoken->where(['app_id'=>$appid])->order('app_tokenTime','desc')->select();
		$this->assign('apptoken',$token_arr);
    	return $this->fetch();
	}

}
