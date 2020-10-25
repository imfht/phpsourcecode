<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/14
 * Time: 13:03
 */
namespace app\admin\controller;

use app\admin\validate\Admin as validateMember;
use app\admin\model\Admin as modelMember;

class Admin extends Base{
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param array $where
     * @param string $order
     * @param int $sql
     * @return array
     * @throws \think\exception\DbException
     */
    public function index($where=[],$order='id desc',$sql=1){
        $where = $this->_where($where);
        $column = new modelMember;
        $_column =$column
            ->field("{$column->getTable()}.*,group.title as gtitle")
            ->leftJoin('group',"group.id = {$column->getTable()}.gid")
            ->where($where)
            ->order($order)
            ->paginate();
        foreach ($_column as $k => $v) {
            if($v['gid']==-1){
                $_column[$k]['gtitle'] = '超级管理员';
            }
        }
        $data = [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_column
        ];
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        if(!$sql){
            unset($data['sql']);
        }
        return $data;
    }

    public function reset(){
        $data = request()->post();
        $validate = new validateMember;
        if(!$validate->scene('scenePassword')->check($data)){
            return [
                'status'=>0,
                'msg'=>$validate->getError()
            ];
        }
        $password = $this->_password($data['password']);
        $column = new modelMember;
        if(!$column->where('id','eq',$data['id'])
            ->setField('password',$password)){
            return [
                'status'=>0,
                'msg'=>'更新失败'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'更新成功'
        ];
    }

    /**
     * 保存
     * @param int $id
     * @return array
     */
    public function save($id=0){
        $data = request()->post();
        $validate = new validateMember;
        $column = new modelMember;
        if($id){
            if(!$validate->scene('sceneInfo')->check($data)){
                return [
                    'status'=>0,
                    'msg'=>$validate->getError()
                ];
            }
            unset($data['password']);
            if(!$column->allowField(true)->isUpdate(true)->save($data)){
                return [
                    'status'=>0,
                    'msg'=>'更新失败'
                ];
            }
            return [
                'status'=>1,
                'msg'=>'更新成功'
            ];
        }else{
            if(!$validate->check($data)){
                return [
                    'status'=>0,
                    'msg'=>$validate->getError()
                ];
            }
            $data['hash'] = substr((md5(uniqid(rand(), true))),16,4);
            $count = $column::where(['username'=>$data['username']])->count('id');
            if($count){
                return [
                    'status'=>0,
                    'msg'=>'用户已存在'
                ];
            }
            if(!$column->allowField(true)->save($data)){
                return [
                    'status'=>0,
                    'msg'=>'添加失败'
                ];
            }
            return [
                'status'=>1,
                'msg'=>'添加成功'
            ];
        }
    }

    /**
     * 删除
     * @param int $id
     * @param array $data
     * @return array
     */
    public function delete($id=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $id = str_replace('_',',',$id);
        $column = new modelMember;
        if(!$column::destroy($id)){
            return [
                'status'=>0,
                'msg'=>'删除失败'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'删除成功'
        ];
    }

    /**
     * 读取
     * @param int $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function edit($id=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $column = new modelMember;
        $_column = $column::get($id);
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_column
        ];
    }

    public function read($id=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $column = new modelMember;
        $_column = $column::get($id);
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_column
        ];
    }

    public function update($id=0,$status=-1){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $column = new modelMember;
        $_column = $column::get($id);
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        $_column->status=$status;
        if(!$_column->save()){
            return [
                'status'=>0,
                'msg'=>'修改失败'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'修改成功'
        ];
    }
}