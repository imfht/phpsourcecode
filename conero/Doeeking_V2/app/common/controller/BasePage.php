<?php
/* 2017年1月23日 星期一
 * 基础页面- 其他页面可以继承
 */
namespace app\common\controller;
use think\Db;
use think\Controller;
class BasePage extends Controller{
    //  首页
    public function index(){
        return $this->fetch();
    }
    //  编译页面   
    public function edit(){
        return $this->fetch();
    }
    // 数据保存页面
    // croCodeBs -> bsjson/用于数据删除
    public function save(){
        $data = count($_POST)? $_POST:$_GET;
        if(isset($_GET['uid'])) $data = bsjson($data['uid']);
        $param = $this->_savedata($data);
        $pk = isset($param['pk'])? $param['pk']:null;
        $url = isset($param['url'])? $param['url']: (request()->module()).'/'.(request()->controller()).'/index';
        // $url = isset($param['url'])? $param['url']: (request()->module()).'/'.(request()->controller());
        // url 传递加密数据
        if(isset($data['croCodeBs'])){
            $data = bsjson($data['croCodeBs']);
        }
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        $msg = '数据保存失败';
        $ret = null;
        switch($mode){
            case 'A':                              
                $ret = is_object($param['table'])?
                    $param['table'] -> save($data):
                    Db::table($param['table'])->insert($data)
                    ;
                $msg = $ret? 
                    '数据保存成功':
                    '数据保存失败';
                break;
            case 'M':
                $map = null;
                if(isset($data[$pk])) $map = [$pk=>$data[$pk]];
                if($map) unset($data[$pk]);         // 数据销毁
                if(isset($param['map'])) $map = $param['map'];
                $ret = is_object($param['table'])?
                    $param['table'] -> where($map) -> update($data) :
                    Db::table($param['table'])-> where($map) -> update($data);
                $msg = $ret? 
                    '数据修改成功':
                    '数据修改失败';
                break;                
            case 'D':
                $map = null;
                if(isset($data[$pk])) $map = [$pk=>$data[$pk]];
                // 数据
                $backData = is_object($param['table'])?
                    $param['table'] -> where($map) -> find() ->toArray() :
                    Db::table($param['table'])-> where($map) ->find();
                if(isset($param['map'])) $map = $param['map'];
                // 条件删除
                if($map)
                    $ret = is_object($param['table'])?
                        $param['table'] -> where($map) -> delete() :
                        Db::table($param['table'])-> where($map) ->delete();
                // 全部删除
                else
                    $ret = is_object($param['table'])?
                        $param['table'] -> delete() :
                        Db::table($param['table'])->delete();
                // 数据回收                        
                if($ret){
                    // 此时model需要继承 app\common\model\BaseModel
                    $table = is_object($param['table'])? $param['table'] -> getTable(): $param['table'];
                    $this->pushRptBack($table,$backData); 
                }
                $msg = $ret? 
                    '数据删除成功':
                    '数据删除失败';
                break;
        }
        return $this->success($msg,$url);
    }
    // 保存页面参数
    protected function _savedata(&$data)
    {
        return [
            'table' => '数据库名称',
            'pk'    => '主键',
            'map'    => '条件',
            'url'   => 
                (request()->module()).
                '/'.
                (request()->controller()).
                '/index'
        ];
    }
}