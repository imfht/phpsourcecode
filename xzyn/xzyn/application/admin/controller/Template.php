<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\Template as Templates;

class Template extends Common {
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new Templates;   //别名：避免与控制名冲突
    }

    public function index() {
        $dataList = $this->cModel->select();
        $dir = './template';	// 模版文件夹
        $config_name = 'config.json';   // 模版配置文件名
        $dir_arr = scandir($dir);   // 获取目录中的文件和目录的数组
        $file_arr = [];	// 配置数据
        $puth_arr = [];	// 模版目录
        foreach ($dir_arr as $val ) {
            if( $val != '.' && $val != '..'){
                $dirs = $dir . '/' . $val;
                if( is_dir($dirs) ){	// 判断是否是文件夹
                    $file = $dirs.'/'.$config_name;
                    if( is_file($file) && file_exists($file) ) {	// 判断是否是常规文件 判断文件是否存在
                        $datas = file_get_contents($file);	// 读取配置文件
                        $data_obj = json_decode($datas);	//转换成对象
                        $file_arr[] = $data_obj;
                        $puth_arr[] = $dir . '/' .$data_obj->puth_name;
                    }
                }
            }
        }
        $dataList_new = [];
        foreach ($file_arr as $k => $v) {
            if( !empty($dataList) ){
                foreach ($dataList as $dk => $dv) {
                    if( $file_arr[$k]->puth_name == $dv->puth_name ){
                        $dv['img_url'] = H_NAME . '/' . $v->img;
                        $dv['is_install'] = 1;
                        $dataList_new[] = $dv;
                    }
                }
            }
        }
        if( !empty($dataList) ){
            foreach ($dataList as $k => $v) {
                foreach ($file_arr as $kk => $vv) {

                    if( $file_arr[$kk]->puth_name != $v->puth_name ){
                        $file_arr[$kk]->img_url = H_NAME . '/' . $file_arr[$kk]->img;
                        $file_arr[$kk]->is_install = 0;
                        $file_arr[$kk]->status = 0;
                    }else{
                        unset($file_arr[$kk]);
                    }
                }
            }
        }
        unset($dataList);
        $this->assign('dataList', $dataList_new);
        $this->assign('file_arr', $file_arr);
        return $this->fetch();
    }

    public function add() {	//添加
        if (request()->isPost()){
            $data = input('post.');
            $find = $this->cModel->where(['puth_name'=>$data['puth_name']])->find();
            if( !empty($find) && $find['puth_name'] == $data['puth']){
                return ajaxReturn('模版目录名字重复，请更换。');
            }
			$result = $this->validate($data,C_NAME);
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
        }
    }

    public function edit() {	//编辑
        if (request()->isPost()){
            $data = input('post.');
            $data_arr = $this->cModel->select();
            $data_arr = $data_arr->toArray();
            $save_arr = [];
            foreach ($data_arr as $k => $v) {
                unset($v['create_time']);
                unset($v['update_time']);
                if( $v['id'] == $data['id'] ){
                    $v['status'] = $data['status'];
                    $save_arr[] = $v;
                }else{
                    $v['status'] = 0;
                    $save_arr[] = $v;
                }
            }
            $result = $this->cModel->saveAll($save_arr);
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }
    }

    public function delete() {	//删除
        if (request()->isPost()){
            $id = input('id');
            if ( !empty($id) ){
                $info = $this->cModel->where(['id'=>$id])->find();
                if( $id == 1 || $info['puth_name'] == 'default' ){
                    return ajaxReturn('系统默认模版,不能卸载');
                }
                if( $info['status'] == 1 ){
                    $this->cModel->where(['id'=>1])->setField('status','1');
                }
                $result = $this->cModel->where(['id'=>$id])->delete();
                if ($result){
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }

}