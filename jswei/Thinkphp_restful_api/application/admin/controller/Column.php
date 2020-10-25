<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/14
 * Time: 13:03
 */
namespace app\admin\controller;

use app\admin\validate\Column as validateColumn ;
use app\admin\model\Column as modelColumn;
/**
 * Class Column
 * @package app\admin\controller
 *
 * @route('column')
 */
class Column extends Base{
    public function __construct(){
        parent::__construct();
    }

    /**
     * @param array $where
     * @param int $tree
     * @return array
     * @throws \think\exception\DbException
     */
    public function index($where=[],$tree=0){
        $where = $this->_where($where);
        $column = new modelColumn;
        if($tree){
            $_column =$column::field('id,fid,title,name as `index`')
                ->where($where)->where('status','eq',0)->select();
            $_column = \service\Category::unlimitedForLevel($_column,0,'subs');
            $_column = json_decode(json_encode($_column,true),true);
            $_column = array_remove_empty($_column);
        }else{
            $_column =$column::all($where);
            $_column = \service\Category::limitForLevel($_column);
        }
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_column
        ];
    }

    /**
     * 保存
     * @param int $id
     * @return array
     */
    public function save($id=0){
        sleep(1);
        $data = request()->post();
        $validate = new validateColumn;
        if(!$validate->check($data)){
            return [
                'status'=>0,
                'msg'=>$validate->getError()
            ];
        }
        $column = new modelColumn;
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
     * @route('admin/column_list','get')
     */
    public function column_list(){
        $column = new modelColumn;
        $column = $column::where('status','eq',0) ->select();
        $column = \service\Category::limitForLevel($column);
        if(!$column){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$column
        ];
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
        $column = new modelColumn;
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
        $column = new modelColumn;
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
        $column = new modelColumn;
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

    /**
     * @param int $name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     * @route('admin/column_one','get')
     */
    public function column_one($name=0){
        $column = new modelColumn;
        if(!$name){
            return [
                'status'=>0,
                'msg'=>'缺少参数'
            ];
        }

        $_m = $column::field('id,title,name as `index`,ico as `icon`,fid,type')
            ->where('name','eq',$name)->find();

        if(!$_m){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'result'=>$_m
        ];
    }
}