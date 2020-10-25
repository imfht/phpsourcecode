<?php
namespace app\admin\controller;

use app\common\model\ApiList as ApiLists;
use app\common\model\ApiFields;
use expand\Str;

class Apilist extends Common {

	private $cModel;   // ApiList 控制器关联模型
	private $afModel;   // ApiFields 控制器关联模型

    public function initialize(){
        parent::initialize();
		$this->cModel = new ApiLists;
		$this->afModel = new ApiFields;
    }

    public function index() {
    	$where = [];
        if (input('get.search')){
            $where[] = ['apiName|method|info','like', '%'.input('get.search').'%'];
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
			$data['hash'] = uniqid();
	        $data['id'] = '';
	        $data['method'] = '';
	        $data['accessToken'] = '';
	        $data['needLogin'] = '';
	        $data['isTest'] = '';
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
                $result = $this->cModel->where($where)->delete();
				if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
			}
		}
    }

    public function request($hash,$type) {	// 请求/返回 参数列表
    	if (request()->isPost()){

		}else{
	    	$where = [];
	        if (input('get.search')){
	            $where[] = ['fieldName|default|info','like', '%'.input('get.search').'%'];
	        }
	        if (input('get._sort')){
	            $order = explode(',', input('get._sort'));
	            $order = $order[0].' '.$order[1];
	        }else{
	            $order = 'id desc';
	        }
			//type=0 请求参数, type=1 返回参数.
			$apiData = $this->afModel->where(['type'=>$type,'hash'=>$hash])->where($where)->order($order)->select();
			$api_name = '没有';
			if( !empty($apiData) ){
				foreach ($apiData as $k => $v) {
					$api_name = $v->api_list->apiName;
				}
			}
			if( $type == 0 ){
				$title_name = '请求字段列表';
			}else{
				$title_name = '返回字段列表';
			}
			$this->assign('dataType',$this->afModel->dataType);	//字段类型
			$this->assign('title','【'.$api_name.'】'.$title_name);
			$this->assign('hash',$hash);
			$this->assign('type',$type);
			$this->assign('apiData',$apiData);
			return $this->fetch();
		}
    }

    public function editrs($type,$hash) {	//编辑/新增 参数字段
    	$id = input('id');
    	if (request()->isPost()){
			$data = input('post.');
			if( empty($id) ){	//新增字段 提交
				$data['type'] = $type;
				$data['hash'] = $hash;
				if( !empty($data['fieldName']) ){
					$data['showName'] = $data['fieldName'];
				}
				$result = $this->validate($data,'ApiFields.add');
				if( true !== $result ){
					return ajaxReturn($result);
				}else{
					$result = $this->afModel->allowField(true)->save($data);
				}
			}else{	//编辑字段 提交
				if( !empty($data['fieldName']) ){
					$data['showName'] = $data['fieldName'];
				}
				if (count($data) == 2){
	                foreach ($data as $k =>$v){
	                    $fv = $k!='id' ? $k : '';
	                }
					$result = $this->validate($data,'ApiFields.'.$fv);
				}else{
					$result = $this->validate($data,'ApiFields.edit');
				}
				if( true !== $result ){
					return ajaxReturn($result);
				}else{
					$result = $this->afModel->allowField(true)->save($data, $data['id']);
				}
			}
			if( $result ){
				return ajaxReturn('操作成功', url('Apilist/request',['type'=>$type,'hash'=>$hash]));
			}else{
				return ajaxReturn('操作失败');
			}
		}else{
			if( empty($id) ){	//新增字段
				if( $type == 0 ){	//新增请求字段
					$title = '新增请求字段';
				}else{	//新增返回字段
					$title = '新增返回字段';
				}
				$data['hash'] = $hash;
				$data['id'] = 0;
				$data['isMust'] = '';
			}else{	//编辑字段
				if( $type == 0 ){	//编辑请求字段
					$title = '编辑请求字段';
				}else{	//新增返回字段
					$title = '编辑返回字段';
				}
				$data = $this->afModel->get( ['id'=>$id] );
			}
			$biao_list = db()->query("SHOW TABLE STATUS");	// 获取数据库的所有表信息
			$biao_data = [];
			foreach ($biao_list as $k => $v) {
				$b_name_arr = explode('xzyn_',$v['Name']);
				$biao_data[$k]['name'] = $b_name_arr[1];
				$biao_data[$k]['info'] = $v['Comment'];
			}
			$this->assign('type',$type);
			$this->assign('biao_data',$biao_data);
			$this->assign('title',$title);
			$this->assign('data',$data);
        	return $this->fetch();
		}
    }

    public function deleters($hash,$type) {	//删除 参数字段
    	if (request()->isPost()){
			$id = input('id');
			if (isset($id) && !empty($id)){
				$id_arr = explode(',', $id);
				$where[] = [ 'id','in', $id_arr ];
				$where[] = [ 'hash','=', $hash ];
				$where[] = [ 'type','=', $type ];
                $result = $this->afModel->where($where)->delete();
				if ($result){
                    return ajaxReturn('操作成功', url('Apilist/request',['type'=>$type,'hash'=>$hash]));
                }else{
                    return ajaxReturn('操作失败');
                }
			}
		}
    }

    public function getInfo() {
    	if (request()->isPost()){
    		$name = input('name');
    		$biaoInfo = db()->query("SHOW FULL COLUMNS FROM xzyn_".$name);	// 获取 [xzyn_user] 表的所有字段信息
			$biao_info = [];
			foreach ($biaoInfo as $k => $v) {
				$biao_info[$k]['name'] = $v['Field'];
				$biao_info[$k]['info'] = $v['Comment'];
				$biao_info[$k]['type'] = $v['Type'];
			}
			return ajaxReturn('操作成功','',1,$biao_info);
		}
	}











}
