<?php
namespace app\admin\controller;
use think\Db;
use \tp5er\Backup;
class Database extends Common
{
    protected $db = '', $datadir;
    function initialize(){
        parent::initialize();
        $this->config=array(
            'path'     => './Data/',//数据库备份路径
            'part'     => 20971520,//数据库备份卷大小
            'compress' => 0,//数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9 //数据库备份文件压缩级别 1普通 4 一般  9最高
        );
        $this->db = new Backup($this->config);
    }
    public function database(){
        if(request()->isPost()){
            $list = $this->db->dataList();
            $total = 0;
            foreach ($list as $k => $v) {
                $list[$k]['size'] = format_bytes($v['data_length']);
                $total += $v['data_length'];
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$list,'total'=>format_bytes($total),'tableNum'=>count($list),'rel'=>1];
        }
        return view();
    }
    //优化
    public function optimize() {
        $tables = input('tables/a');
        if (empty($tables)) {
            return ['code'=>0,'msg'=>'请选择要优化的表！'];
        }
        if($this->db->optimize($tables)){
            return ['code'=>1,'msg'=>'数据表优化成功！'];
        }else{
            return ['code'=>0,'msg'=>'数据表优化出错请重试！'];
        }
    }
    //修复
    public function repair() {
        $tables = input('tables/a');
        if (empty($tables)) {
            return ['code'=>0,'msg'=>'请选择要修复的表！'];
        }
        if($this->db->repair($tables)){
            return ['code'=>1,'msg'=>'数据表修复成功！'];
        }else{
            return ['code'=>0,'msg'=>'数据表修复出错请重试！'];
        }
    }
    //备份
    public function backup(){
        $tables = input('post.tables/a');
        if (!empty($tables)) {
            foreach ($tables as $table) {
                $this->db->setFile()->backup($table, 0);
            }
            return ['code'=>1,'msg'=>'备份成功！'];
        } else {
            return ['code'=>0,'msg'=>'请选择要备份的表！'];
        }
    }
    //备份列表
    public function restore(){
        if(request()->isPost()){
            $list =  $this->db->fileList();
            return ['code'=>0,'msg'=>'获取成功!','data'=>$list,'rel'=>1];
        }
        return view();
    }
    //执行还原数据库操作
    public function import($time) {
        $list  = $this->db->getFile('timeverif',$time);
        $this->db->setFile($list)->import(1);
        return ['code'=>1,'msg'=>'还原成功！'];
    }

    //下载
    public function downFile($time) {
        $this->db->downloadFile($time);
    }
    //删除sql文件
    public function delSqlFiles() {
        $time = input('post.time');
        if($this->db->delFile($time)){
            return ['code'=>1,'msg'=>"备份文件删除成功！"];
        }else{
            return ['code'=>0,'msg'=>"备份文件删除失败，请检查权限！"];
        }
    }
}