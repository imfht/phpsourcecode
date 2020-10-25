<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/14
 * Time: 13:03
 */
namespace app\admin\controller;

use app\admin\validate\Group as validateModule ;
use app\admin\model\Group as modelModule;

class Group extends Base{
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
        $column = new modelModule;
        $_column =$column::where($where)->paginate();
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$_column
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
        $validate = new validateModule;
        if(!$validate->check($data)){
            return [
                'status'=>0,
                'msg'=>$validate->getError()
            ];
        }
        $column = new modelModule;
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
        $column = new modelModule;
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
        $column = new modelModule;
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
            'data'=>$_column
        ];
    }

    public function update($id=0,$status=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $column = new modelModule();
        $_column = $column::get($id);
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        if(!$_column->status($status,['id'=>$id])){
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
     * @param int $id
     * @param int $power
     * @return array
     * @throws \think\exception\DbException
     *
     * @route('admin/power','post')
     */
    public function power($id=0,$power=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $validate = new \app\admin\validate\Group;
        if(!$validate->scene('power')->check(['id'=>$id,'power'=>$power])){
            return [
                'status'=>0,
                'msg'=>$validate->getError()
            ];
        }
        $column = new modelModule();
        $_column = $column::get($id);

        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        $_column->power=$power;
        if(!$_column->save()){
            return [
                'status'=>0,
                'msg'=>'设置失败'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'设置成功'
        ];
    }

    /**
     * @param int $id
     * @return array
     * @throws \think\exception\DbException
     *
     * @route('admin/powers','get')
     */
    public function powers($id=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $column = new modelModule();
        $_column = $column::get($id);
        if(!$_column){
            return [
                'status'=>0,
                'msg'=>'没有数据'
            ];
        }
        $power = explode(',',$_column['power']);
        foreach ($power as $k=>$v){
            $power[$k]=intval($v);
        }
        return [
            'status'=>1,
            'msg'=>'获取成功',
            'data'=>$power
        ];
    }

    /**
     * @param int $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     * @route('admin/check','get')
     */
    public function check($id=0){
        if(!$id){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        $model = db('module')->field('id,title,name')
            ->where('id','in',$id)->select();
        if(!$model){
            return [
                'status'=>0,
                'msg'=>'缺少id'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'获取成功',
            'data'=>$model
        ];
    }
}