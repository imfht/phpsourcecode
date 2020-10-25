<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Config as Configs;
use app\common\model\Music;

class Config extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new Configs;   //别名：避免与控制名冲突
    }

    public function index() {
        $where = [];
        if (input('get.search')){
            $where[] = ['k|v|desc|type|texttype','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'type asc,status desc,sorts asc,id asc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    public function create() {	//新增字段
        if (request()->isPost()){
            $data = input('post.');
			if( !empty($data['textvalue']) ){
				$textarr = explode("\n",$data['textvalue']);	//分割数组
				$arr = [];
                foreach ($textarr as $k => &$v) {
                    if (stripos($v, "|") !== false) {
                        $item = explode('|', $v);
                        $arr[$item[0]] = $item[1];
                    }
                }
                $data['textvalue'] = $arr ? json_encode($arr) : '';
			}
			$result = $this->validate($data,C_NAME.'.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
        	$this->assign('typeList', $this->cModel->getTypeList());	//表单类型列表
        	$this->view->assign('groupList', $this->cModel->getGroupList());
			$this->assign('data',$data=0);
			$this->assign('textvalue', $textvalue='');
            return $this->fetch('edit');
        }
    }

    public function edit($id) {	//编辑字段
        if (request()->isPost()){
            $data = input('post.');
            if (count($data) == 2){
                foreach ($data as $k =>$v){
                    $fv = $k!='id' ? $k : '';
                }
				$result = $this->validate($data,C_NAME.'.'.$fv);

            }else{
            	$result = $this->validate($data,C_NAME.'.edit');
				if( !empty($data['textvalue']) ){
					$textarr = explode("\n",$data['textvalue']);	//分割数组
					$arr = [];
	                foreach ($textarr as $k => &$v) {
	                    if (stripos($v, "|") !== false) {
	                        $item = explode('|', $v);
	                        $arr[$item[0]] = $item[1];
	                    }
	                }
	                $data['textvalue'] = $arr ? json_encode($arr) : '';
				}
            }
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $this->cModel->allowField(true)->save($data, $data['id']);
			}
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $data = $this->cModel->get($id);
			$textvalarr = json_decode($data['textvalue'],true);
			$textvalue = '';
			if(!empty($textvalarr)){
				foreach($textvalarr as $k => $v){
					$textvalue .= $k.'|'.$v.'
';
				}
			}else{
				$textvalue = '';
			}
            $this->assign('textvalue', trim($textvalue));
            $this->assign('data', $data);
			$this->view->assign('groupList', $this->cModel->getGroupList());
			$this->assign('typeList', $this->cModel->getTypeList());
            return $this->fetch();
        }
    }

    public function delete() {	//删除
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

    /**
     * 循环保存数据
     */
    public function save() {
        if (request()->isPost()){
            $data = input('post.');
            $type = $data['type'];   //取出类型
            unset($data['type']);
            if(!empty($type)){
                if(is_array($data) && !empty($data)){
                    foreach ($data as $k=>$val) {
                        $where = array('type' => $type, 'k'=>$k);
                        $this->cModel->where($where)->update(['v' => $val]);
                    }
                    return ajaxReturn('操作成功', url('Config/'.$type));
                }else{
                    return ajaxReturn('操作失败');
                }
            }else{
                return ajaxReturn('操作失败');
            }
        }
    }

    /**
     * 站点配置
     */
    public function web() {
        $type = A_NAME;
        $where = ['type' => $type, 'status'=>1];
        $data = $this->cModel->where($where)->order('sorts ASC,id ASC')->select();
		foreach( $data as $k => &$v ){
			if( !empty($v['textvalue']) ){
				$data[$k]['textvalue'] = json_decode($v['textvalue'], true);
			}
		}
        $this->assign('data', $data);
        $this->assign('type', $type);
        return $this->fetch();
    }

    /**
     * 系统配置
     */
    public function system() {
        $type = A_NAME;
        $where = ['type' => $type, 'status'=>1];
        $data = $this->cModel->where($where)->order('sorts ASC,id ASC')->select();
		foreach( $data as $k => &$v ){
			if( !empty($v['textvalue']) ){
				$data[$k]['textvalue'] = json_decode($v['textvalue'], true);
			}
		}
        $this->assign('data', $data);
        $this->assign('type', $type);
        return $this->fetch('web');
    }

    /**
     * 上传配置
     */
    public function up()
    {
        $type = A_NAME;
        $where = ['type' => $type, 'status'=>1];
        $data = $this->cModel->where($where)->order('sorts ASC,id ASC')->select();
		foreach( $data as $k => &$v ){
			if( !empty($v['textvalue']) ){
				$data[$k]['textvalue'] = json_decode($v['textvalue'], true);
			}
		}
        $this->assign('data', $data);
        $this->assign('type', $type);
        return $this->fetch('web');
    }

    /**
     * 短信配置
     */
    public function sms()
    {
        $type = A_NAME;
        $where = ['type' => $type, 'status'=>1];
        $data = $this->cModel->where($where)->order('sorts ASC,id ASC')->select();
		foreach( $data as $k => &$v ){
			if( !empty($v['textvalue']) ){
				$data[$k]['textvalue'] = json_decode($v['textvalue'], true);
			}
		}
        $this->assign('data', $data);
        $this->assign('type', $type);
        return $this->fetch('web');
    }

    /**
     * 音乐播放器
     */
    public function music() {
    	$music = new Music();
		$musicList = $music->order('orderby ASC,id desc')->paginate(20);
        $type = A_NAME;
        $where = ['type' => $type, 'status'=>1];
        $data = $this->cModel->where($where)->order('sorts ASC,id ASC')->select();
		foreach( $data as $k => &$v ){
			if( !empty($v['textvalue']) ){
				$data[$k]['textvalue'] = json_decode($v['textvalue'], true);
			}
			if( $v['texttype'] == 'array' ){
				$data[$k]['v'] = $yydata;
			}
		}
        $this->assign('musicList', $musicList);
        $this->assign('data', $data);
        $this->assign('type', $type);
        return $this->fetch();
    }



}