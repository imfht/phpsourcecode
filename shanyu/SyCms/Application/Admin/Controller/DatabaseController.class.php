<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class DatabaseController extends AdminBaseController {

    public function index(){
        $this->_batch();

        $list = D('Database')->getTables();

        //搜索筛选
        $this->_search();
        $search=I('name','');
        foreach ($list as $k => $v) {
            if(!empty($search)){
                if(!strpos($v['name'],$search)) unset($list[$k]);
            }
            $data_size += $v['data_length'];
        }
        $this->assign('data_size',format_bytes($data_size));
        $this->assign('data_count',count($list));
        
        $this->list=$list;
        $this->display();
    }
    protected function batchCall($batch,$pk){
        switch ($batch) {
            case 'backup':
                $result=D('Database')->backup($pk);
                break;
            case 'optimize':
                $result=D('Database')->optimize($pk);
                break;
            case 'repair':
                $result=D('Database')->repair($pk);
                break;
        }

        if($result){
            $this->success('批量处理成功');
        }else{
            $this->error(D('Database')->error);
        }

    }
    public function createSql($table){
        $prefix=C("DB_PREFIX");
        $table=ltrim($table,$prefix);
        $sql=D('Table')->getCreateSql($table);
        $this->assign('sql',$sql);

        $this->display();
    }
    public function columns($table){
        $prefix=C("DB_PREFIX");
        $table=ltrim($table,$prefix);
        $columns=D('Table')->getTableColumns($table);
        $this->assign('columns',$columns);

        $this->display();
    }

    //备份列表
    public function backupList(){
        $list=D('Database')->backupList();
        $this->assign('list',$list);

        $this->assign('data_count',count($list));
        $this->display();
    }

    //还原数据库
    public function import(){
        if(!IS_AJAX) return false;
        $time = I('time','');
        if(empty($time)) $this->error('请选择数据库');

        if(D('Database')->backupImport($time)){
            $this->success('备份恢复成功！');
        }else{
            $this->error(D('Database')->getError());
        }
    }
    //下载数据库
    public function down()
    {
        $name = I('name','');
        if(empty($name)) $this->error('请选择数据库文件');

        $filePath = './Backups/' . $name;
        if (!file_exists($filePath)) {
            $this->error("该文件不存在，可能是被删除");
        }
        $filename = basename($filePath);
        
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Content-Length: " . filesize($filePath));
        readfile($filePath);
    }

    //删除备份数据
    public function del(){
        if(!IS_AJAX) return false;
        $time = I('time','');
        if(empty($time)) $this->error('请选择数据库');

        if(D('Database')->backupDel($time)){
            $this->success('备份删除成功！');
        }else{
            $this->error('备份删除失败！');
        }
    }
    

}