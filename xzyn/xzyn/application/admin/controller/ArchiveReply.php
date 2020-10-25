<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\ArchiveReply as ArchiveReplys;

class ArchiveReply extends Common
{
    private $cModel;   //当前控制器关联模型

    public function initialize()
    {
        parent::initialize();
        $this->cModel = new ArchiveReplys;   //别名：避免与控制名冲突
    }

    public function index() {
        $where = [];
        if (input('get.search')){
            $where[] = ['aid|uid','like', '%'.input('get.search').'%'];
        }
        if (input('get._sort')){
            $order = explode(',', input('get._sort'));
            $order = $order[0].' '.$order[1];
        }else{
            $order = 'id desc';
        }
        $dataList = $this->cModel->where($where)->order($order)->paginate(15);
		foreach ($dataList as $k => $v) {
			$dirs = $v->Archive->Arctype->dirs;
			$arcid = $v->Archive->id;
			$dataList[$k]['archive_url'] = url('/detail/'.$dirs.'/'.$arcid);
		}
        $this->assign('dataList', $dataList);
        return $this->fetch();
    }

    /*
    public function create()
    {
        if (request()->isPost()){
            $data = input('post.');
            $result = $this->cModel->validate(C_NAME.'.add')->allowField(true)->save($data);
            if ($result){
                return ajaxReturn(lang('action_success'), url('index'));
            }else{
                return ajaxReturn($this->cModel->getError());
            }
        }else{
            return $this->fetch('edit');
        }
    }
    */

    public function edit($id) {	//编辑回复
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
            if ($result){
                return ajaxReturn('操作成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            $data = $this->cModel->get($id);
            $this->assign('data', $data);
            return $this->fetch();
        }
    }

    public function delete() {	//删除回复
        if (request()->isPost()){
            $id = input('id');
            if (isset($id) && !empty($id)){
                $id_arr = explode(',', $id);
				$pids[] = [ 'pid','in', $id_arr ];
				$pid_arr = $this->cModel->where($pids)->select();	//子级回复
				if(count($pid_arr) > 0){
					foreach ($pid_arr as $k => $v) {
						$this->cModel->where(['id'=>$v['id']])->delete();	//删除 子级回复
						db('zan_log')->where(['ar_id'=>$v['id']])->delete();   //删除 子级回复 赞 数据
					}
				}
                $where[] = [ 'id','in', $id_arr ];
                $result = $this->cModel->where($where)->delete();	//删除 回复
                if ($result){
                	$wheres[] = [ 'ar_id','in', $id_arr ];
                	db('zan_log')->where($wheres)->delete();   //删除 回复 赞数据
                    return ajaxReturn('操作成功', url('index'));
                }else{
                    return ajaxReturn('操作失败');
                }
            }
        }
    }
}