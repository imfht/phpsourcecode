<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/14
 * Time: 13:03
 */
namespace app\admin\controller;

use app\admin\validate\Member as validateMember;
use app\admin\model\Member as modelMember;

class Member extends Base{
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
    public function index($where=[],$order='id desc',$sql=0){
        $where = $this->_where($where);
        $column = new modelMember;
        $_column =$column::pager($where);
        $_sql = $column::getLastSql();
        $data = [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_column,
            'sql'=>$_sql
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

    /**
     * 保存
     * @param int $id
     * @return array
     */
    public function save($id=0){
        sleep(1);
        $data = request()->post();
        $validate = new validateMember;
        if(!$validate->check($data)){
            return [
                'status'=>0,
                'msg'=>$validate->getError()
            ];
        }
        $column = new modelMember;
        if($id){
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
        sleep(1);
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