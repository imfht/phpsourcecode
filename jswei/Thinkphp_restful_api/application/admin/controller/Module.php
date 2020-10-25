<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/14
 * Time: 13:03
 */
namespace app\admin\controller;

use app\admin\validate\Module as validateModule ;
use app\admin\model\Module as modelModule;

class Module extends Base{

    protected $isSink = false;
    protected $sinkMethods = [];
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
        if($tree){
            $_column =$column::field('id,fid,title,name')
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
            'data'=>$_column
        ];
    }
    /**
     * 保存
     * @param int $id
     * @return array
     */
    public function save($id=0){
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
     * @route('admin/module_list','get')
     */
    public function module_list(){
        $column = new modelModule;
        $column = $column::where('status','eq',0) ->select();
        $column = \service\Category::limitForLevel($column);
        if(!$column){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        array_unshift($column,[
            'id'=>0,
            'title'=>'顶级栏目',
            'icon'=>'',
            'name'=>'top',
            'html'=>''
        ]);
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$column
        ];
    }

    /**
     * @param int $name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     * @route('admin/module_one','get')
     */
    public function module_one($name=0){
        $column = new modelModule;
        if(!$name){
            return [
                'status'=>0,
                'msg'=>'缺少参数'
            ];
        }
        $_m = $column::where('name','eq',$name)->find();
        if(!$_m){
            return [
                'status'=>0,
                'msg'=>'无数据'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'查询成功',
            'data'=>$_m
        ];
    }

    /**
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     * @route('admin/modules','get')
     */
    public function modules(){
        $column = new modelModule;
        $column = $column::field('id,id as `key`,title as `label`,name,status as `disabled`')->select();
        $column = \service\Category::limitForLevel($column);
        foreach ($column as $k=>$v){
            $column[$k]['disabled']=$v['disabled']==1?true:false;
        }
        return $this->__s('',$column);
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

    public function update($id=0,$status=-1){
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
        if(!db('module')->update(['id'=>$id,'status'=>$status])){
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
     * @param array $where
     * @param int $tree
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @route('admin/navbar')
     */
    public function navbar($where=[],$tree=0){
        $where = $this->_where($where);
        $module = new modelModule;
        $column = new \app\first\model\Column;
        $gid = self::$user['gid']?self::$user['gid']:-1;
        if($gid!=-1 && !empty($gid)){
            $_group = db('group')->field('id,title,power')->find($gid);
            $_module =$module::field('id,fid,title,name as `index`,ico as `icon`')
                ->where($where)
                ->where('status','eq',0)
                ->where('id','in',$_group['power'])
                ->order('sort asc')
                ->select();
        }else{
            $_module =$module::all($where);
        }

        if($tree){
            $_module = \service\Category::unlimitedForLevel($_module,0,'subs');
            $_module = json_decode(json_encode($_module,true),true);
            $_module = array_remove_empty($_module);
            $_column =$column::field('id,fid,title,cate_type,name  as `index`,ico as `icon`')
                ->where($where)->where('status','eq',0)->select();
            $_column = \service\Category::unlimitedForLevel($_column,0,'subs');
            $_column = json_decode(json_encode($_column,true),true);
            $_column = array_remove_empty($_column);
            $_module = array_merge($_module,$_column);
        }else{
            $_module =$module::all($where);
            $_module = \service\Category::limitForLevel($_module);
            $_column =$column::all($where);
            $_column = \service\Category::limitForLevel($_column);
            $_module = array_merge($_module,$_column);
        }
        if(!$_module){
            $this->__s('',[
                'status'=>0,
                'msg'=>'无数据'
            ]);
        }
        $this->__s('',$_module);
    }
}