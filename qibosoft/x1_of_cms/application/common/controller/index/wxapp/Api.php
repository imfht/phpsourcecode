<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;
//use think\Db;

//小程序或APP调用的列表数据
abstract class Api extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容
    protected $mid;                      //模型ID
    
    public function add(){
        die('出错了!');
    }
    public function edit(){
        die('出错了!');
    }
    public function delete(){
        die('出错了!');
    }
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'content');
        $this->model_reply = get_model_class($dirname,'reply');
        $this->mid = 1;
    }
    
    protected function check_getTab($id=0){
        
        $info = $this->model->getInfoByid($id);
        if (empty($info)) {
            return '信息内容不存在';
        }elseif (!$this->admin && fun('admin@sort',$info['fid'])!==true) {
            return '你没权限';
        }
//         $table = $this->model->getTableByMid($info['mid']);         
//         return array_merge($info,['table'=>$table]);
        return $info;
    }
    
    
    /**
     * 审核操作
     * @param number $id
     * @param number $rid
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function yz($id=0,$rid=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        if ($rid) {
            $reply_info = $this->model_reply->where('id',$rid)->find();
            if (empty($reply_info)) {
                return $this->err_js('回复不存在');
            }
            $this->model_reply->where('id',$rid)->update([
                'status'=>$reply_info['status']==1?0:1
            ]);
            return $this->ok_js([
                'status'=>$reply_info['status']
            ]);
        }else{
            $this->model->updates([
                'id'=>$id,
                'status'=>$info['status']==1?0:1
            ]);
            return $this->ok_js([
                'status'=>$info['status']
            ]);
        }
    }
    
    /**
     * 内容置顶
     * 置顶时间,单位小时
     * @param number $id 内容ID
     * @param number $time 置顶多久,单位小时
     * @return string|unknown[]|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function top($id=0,$time=24){        
        $info = $this->check_getTab($id);        
        if (is_string($info)) {
            return $this->err_js($info);
        }        
//        $table = $info['table'];
        $list = $time*3600+time();
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//         $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 信息沉底
     * @param number $id
     * @param number $time 默认为0,也可以设置压后多少个小时,
     * @return string|array|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function bottom($id=0,$time=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        if ($time==0) {
            $list = 0;
        }else{
            $list = $info['create_time'] - $time*3600;
        }
        
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 信息排序恢复原状
     * @param number $id
     * @param number $time
     * @return string|array|void|unknown|\think\response\Json|void|\think\response\Json
     */
    public function recover($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        if ($info['update_time']) {
            $list = $info['update_time'];
        }else{
            $list = $info['create_time'];
        }
        
        $data = [
                'id'=>$id,
                'list'=>$list,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 推荐
     * @param number $id
     */
    public function star($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];    
        $data = [
            'id'=>$id,
            'status'=>2,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 取消推荐
     * @param number $id
     */
    public function unstar($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];    
        $data = [
            'id'=>$id,
            'status'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 锁定
     * @param number $id
     */
    public function lock($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']);
        if (!table_field($table,'lock')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `lock` TINYINT( 1 ) NOT NULL COMMENT  '是否锁定不给修改,删除,回复' AFTER  `status`");
        }
        
        $data = [
                'id'=>$id,
                'lock'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消锁定
     * @param number $id
     */
    public function unlock($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        $data = [
                'id'=>$id,
                'lock'=>0,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 标题加粗
     * @param number $id
     */
    public function fonttype($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']); 
        if (!table_field($table,'font_type')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `font_type` TINYINT( 1 ) NOT NULL COMMENT  '标题字体加粗或其它'");
        }        
        $data = [
                'id'=>$id,
                'font_type'=>1,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消标题加粗
     * @param number $id
     */
    public function unfonttype($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
//        $table = $info['table'];        
        $data = [
                'id'=>$id,
                'font_type'=>0,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    /**
     * 标题加红色
     * @param number $id
     */
    public function fontcolor($id=0,$type='#ff0000'){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
        $table = $this->model->getTableByMid($info['mid']); 
        if (!table_field($table,'font_color')) {
            query("ALTER TABLE  `qb_{$table}` ADD  `font_color`  VARCHAR( 7 ) NOT NULL COMMENT  '标题字体颜色' ");
        }
        
        $data = [
                'id'=>$id,
                'font_color'=>$type,
        ];
//        $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
    
    /**
     * 取消标题加红色
     * @param number $id
     */
    public function unfontcolor($id=0){
        $info = $this->check_getTab($id);
        if (is_string($info)) {
            return $this->err_js($info);
        }
 //       $table = $info['table'];        
        $data = [
                'id'=>$id,
                'font_color'=>'',
        ];
 //       $result = Db::name($table)->update($data);
        $result = $this->model->updates($data);
        if($result){
            return $this->ok_js();
        }else{
            return $this->err_js('更新失败');
        }
    }
    
}







