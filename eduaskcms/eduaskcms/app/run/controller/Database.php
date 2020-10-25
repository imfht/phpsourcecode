<?php
namespace app\run\controller;

use app\common\controller\Run;
use app\common\lib\Backup;

class Database extends Run
{
    //初始化 需要调父级方法
    public function initialize()
    {  
        $this->not_access_action = ['create', 'modify', 'batch_delete', 'sort'];
        call_user_func(['parent', __FUNCTION__]); 
    }
    
    //列表 
    public function lists()
    {
        $this->assign->list_fields = [
            'Name' => [
                'name' => '表名',
                'list' => 'show'               
            ],
            'Rows' => [
                'name' => '记录条数',
                'list' => 'show'                
            ],
            'Data_length' => [
                'name' => '大小',
                'list' => 'filesize'               
            ],
            'Data_free' => [
                'name' => '冗余',
                'list' => 'filesize'               
            ],
            'Engine' => [
                'name' => '类型',
                'list' => 'show'               
            ],
            'Collation' => [
                'name' => '编码',
                'list' => 'show'                
            ],
            'Comment' => [
                'name' => '说明',
                'list' => 'show'               
            ]
        ];
        
        $list = db()->query("SHOW TABLE STATUS LIKE '".config('database.prefix')."%'");
        $this->assign->list = $list;
        $this->assign->check_fields = 'Name';
        $this->addAction('备份', 'javascript:void(0);', 'fa-exchange', 'backup');
        $this->addItemAction('优化', array('Database/optimize',['table'=>'Name'],'parse'=>['table']), '&#xe756;');
        $this->addItemAction('修复', array('Database/repair',['table'=>'Name'],'parse'=>['table']), '&#xe639;');
        $this->setTitle("数据表列表", 'operation');        
        $this->fetch = true;
        
    }
    
    public function delete()
    {
        $time = trim($this->args['time']);
        if (!$time) {
            return $this->message('error', '请选择需要删除的文件');
        }
        $db = new Backup();
        if ($db->delFile($time)) {
            return $this->message('success', '数据库备份文件删除成功');
        }
    }
    
    public function download()
    {
        $time = trim($this->args['time']);
        if (!$time) {
            return $this->message('error', '请选择需要下载的文件');
        }
        $db = new Backup();
        $file = $db->getFile('time', $time); 
        $content_url = $file[0];//下载文件地址,可以是网络地址,也可以是本地物理路径或者虚拟路径        
        ob_end_clean(); //函数ob_end_clean 会清除缓冲区的内容，并将缓冲区关闭，但不会输出内容。
        header("Content-Type: application/force-download;"); //告诉浏览器强制下载
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($content_url));
        header("Content-Disposition: attachment; filename=" . basename($content_url)); 
        header("Expires: 0");
        header("Cache-control: private");
        header("Pragma: no-cache"); //不缓存页面
        readfile($content_url);         
        exit;
    }
    
    
    
    public function filelist()
    {
        $this->assign->list_fields = [
            'name' => [
                'name' => '文件名',
                'list' => 'show'               
            ],            
            'size' => [
                'name' => '大小',
                'list' => 'filesize'               
            ],
            'compress' => [
                'name' => '压缩',
                'list' => 'show'               
            ],
            'time' => [
                'name' => '日期',
                'list' => 'datetime'               
            ]
        ];
        $db = new Backup();
        $this->assign->no_check = true;
        $filelist = $db->fileList();
        $this->assign->list = array_reverse($filelist);
        //pr($this->assign->list);
        $this->setTitle("操作文件列表", 'operation');  
        //$this->addItemAction('恢复', array('Database/optimize',['time'=>'time'],'parse'=>['time']), '&#xe609;'); 
        $this->addItemAction('下载', array('Database/download',['time'=>'time'],'parse'=>['time']), '&#xe601;');     
        $this->addItemAction('删除', array('Database/delete',['time'=>'time'],'parse'=>['time']), '&#x1006;', 'item-action-delete');          
        $this->fetch = 'filelist';
    }
    
    
    public function backup()
    {
        if (!$this->request->isPost()) {
            return $this->message('error', '不是一个正确的请求方式'); 
        }
        set_time_limit(0);
        $db = new Backup();
        $selected_data = $this->request->post();
        $tables = $selected_data['selected_id'];
        if (!$tables) {
            return $this->message('error', '没有需要备份的数据表');
        }
        $fileinfo = $db->getFile();
        $db->Backup_Init();
        foreach ($tables as $table) {
            $db->setFile($fileinfo['file'])->backup($table, 0);
        }
        return $this->message('success', $fileinfo['file']['name'] . '数据库备份成功');
    }
    
    public function optimize()
    {
        $table = trim($this->args['table']);
        if (!$table) {
            return $this->message('error', '请选择需要优化的表');
        }
        db()->query("OPTIMIZE TABLE `{$table}`");
        return $this->message('success', "数据表：{$table}优化成功");
    }
    
    public function repair()
    {
        $table = trim($this->args['table']);
        if (!$table) {
            return $this->message('error', '请选择需要修复的表');
        }
        db()->query("REPAIR TABLE `{$table}`");
        return $this->message('success', "数据表：{$table}修复成功");
    }
    
    
}
