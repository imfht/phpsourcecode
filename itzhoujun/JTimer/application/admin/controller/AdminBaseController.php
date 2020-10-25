<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/27
 * Time: 18:10
 */

namespace app\admin\controller;


use app\admin\model\CateModel;
use think\db\Query;
use think\Exception;
use think\exception\ErrorException;
use think\Model;

use traits\controller\LayuiTableSet;
use think\Controller;
use think\Db;
use think\Request;

class AdminBaseController extends Controller
{

    use LayuiTableSet;



    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        //ip限制
        $ips = getSetting('admin_limit_ip');
        if($ips){
            $ip = $request->ip();
            if(!in_array($ip,explode(',',$ips))){
                $this->error('无权限访问',url('public/login'));
            }
        }


        //登录判断
        $admin_user = $this->getCurrentUser();
        if(empty($admin_user)){
            $this->redirect(url('public/login'));
        }
        $this->assign('admin_user',$admin_user);
    }

    public function index(){
        return $this->fetch();
    }

    protected function getCurrentUser(){
        return session('admin_user');
    }

    /**
     * @param $model string 表名
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function lists($model = '',$return = false){

        $model = $this->getModel($model);
        $page = input('page',1);
        $limit = input('limit',10);

        $order = method_exists($model, 'getOrderFields') ?
            $model->getOrderFields() :
            $model->getPk() . ' desc';

        $list = $model->where($this->getWhere($model))->order($order)->page($page,$limit)->select();

        $count = $model->where($this->getWhere($model))->count();

        if($return){
            return ['data'=>$list,'count'=>$count];
        }
        $this->tableSet($list, $count);
    }


    /**
     * @param string $model
     * @return array
     * @throws Exception
     */
    protected function getWhere($model = ''){

        $model = $this->getModel($model);
        $request = Request::instance();

        $fields = $model->getTableFields();
        $where = [];

        foreach ($fields as $field){
            if($request->has($field)){
                $where[$field] = $request->input($field);
            }
        }
        return $where;

    }

    /**
     * @param mixed $model
     * @return Query | Model
     * @throws
     */
    public function getModel($model = ''){
        if(is_object($model)){
            return $model;
        }
        if(is_string($model)){
            $table = $model ? : Request::instance()->controller();
            $model = ucfirst($table);
        }
        $model_class = '\\app\\admin\\model\\'.$model.'Model';
        if(class_exists($model_class)){
            $obj = new $model_class();
            return $obj;
        }
        throw new Exception('请创建'.$model_class);
    }

    /**
     * 新增页面
     */
    public function add($model = ''){
        if(Request::instance()->isPost()){
            $this->doAdd($model);
        }else{
            return $this->fetch('edit');
        }
    }

    /**
     * @param  string $model
     * 执行新增操作
     */
    public function doAdd($model = ''){
        $model = $this->getModel($model);
        $model->data(Request::instance()->post());
        try{
            $result = $model->save();
            if($result){
                $this->success('新增成功',url('index'));
            }else{
                $this->error($model->getError());
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
     * 编辑页面
     */
    public function edit($model = ''){
        $request = Request::instance();
        if($request->isPost()){
            $this->doEdit($model);
        }else{
            $id = $request->param('id');
            $model = $this->getModel($model);
            $vo = $model->where($model->getPk(),$id)->find();
            $this->assign('vo',$vo);
            return $this->fetch();
        }
    }

    /**
     * 执行编辑操作
     */
    public function doEdit($model = ''){
        $model = $this->getModel($model);
        try{
            $result = $model->update(Request::instance()->post());
            if($result){
                $this->success('更新成功',url('index'));
            }else{
                $this->error($model->getError());
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
     * 执行删除操作
     */
    public function doDel($model = ''){
        $model = $this->getModel($model);

        $ids = Request::instance()->param('ids');
        try{
            $result = $model->whereIn($model->getPk(),explode(',',$ids))->delete();
            if($result){
                $this->success('删除成功',url('index'));
            }else{
                $this->error($model->getError());
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
    }
}