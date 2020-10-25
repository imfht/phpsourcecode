<?php
namespace app\admin\model;
use app\base\model\BaseModel;
/**
 * 数据库备份还原
 */

class DatabaseModel extends BaseModel {

    public $backupDir = 'backup/';
    public $tableList = array(); //表列表

    /**
     * 获取备份文件列表
     * @return array 文件列表
     */
    public function backupList()
    {
        $fileDir = ROOT_PATH . $this->backupDir;
        if(!is_dir($fileDir)){
            return false;
        }
        $listFile = glob($fileDir.'*');
        $list=array();
        if(is_array($listFile)){
            foreach ($listFile as $key => $value) {
                $value = basename($value);
                $list[$key]['name'] = $value;
                $fileName = explode('-', $value);
                $list[$key]['time'] = $fileName[0].'-'.$fileName[1];
            }
        }
        return $list;
    }

    /**
     * 数据库检测
     */
    public function check() {
        $list = $this->loadTableList();
        if(empty($list)){
            $this->error = '没有发现表';
            return false;
        }
        $this->tableList = $list;
        return true;
    }

    /**
     * 数据库列表
     */
    public function loadTableList(){

        return $this->query('SHOW TABLE STATUS');
    }

    /**
     * 优化数据库
     */
    public function optimizeData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        $tables = array();
        foreach ($list as $value) {
            $tables[] = $value['Name'];
        }
        $tables = implode('`,`', $tables);
        if($this->query("OPTIMIZE TABLE `{$tables}`")){
            return true;
        } else {
            $this->error = '数据库优化失败，请稍后重试！';
            return false;
        }
    }

    /**
     * 修复数据库
     */
    public function repairData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        $tables = array();
        foreach ($list as $value) {
            $tables[] = $value['Name'];
        }
        $tables = implode('`,`', $tables);
        if($this->query("REPAIR TABLE `{$tables}`")){
            return true;
        } else {
            $this->error = '数据库修复失败，请稍后重试！';
            return false;
        }
    }

    /**
     * 备份数据库
     */
    public function backupData(){
        if(!$this->check()){
            return false;
        }
        $list = $this->tableList;
        //生成备份文件信息
        $dir = ROOT_PATH . $this->backupDir;
        $path = $dir.date('Ymd-His', NOW_TIME).'/';
        //检查是否有正在执行的任务
        $lock = $dir ."backup.lock";
        if(is_file($lock)){
            $this->error = '检测到有一个备份任务正在执行，请稍后再试！';
            return false;
        } else {
            //创建锁文件
            file_put_contents($lock, NOW_TIME);
        }
        //检查目录
        if(!is_dir($path)){
            if(!mkdir($path,true)){
                $this->error = '无法创建备份目录，请手动根目录创建Backup文件夹！';
                return false;
            }
        }
        //检查备份目录是否可写
        if(!is_writeable($path)){
            $this->error = '备份目录不存在或不可写，请检查后重试！';
            return false;
        }
        //创建备份文件
        $dbConfig = config('DB.default');
        $Database = new \framework\ext\Dbbak($dbConfig['DB_HOST'].':'.$dbConfig['DB_PORT'],$dbConfig['DB_USER'],$dbConfig['DB_PWD'],$dbConfig['DB_NAME'],'utf8',$path);
        $tables = $Database->getTables();
        if(!$Database->exportSql($tables)){
            $this->error = '备份文件创建失败，请稍后再试！';
            unlink($lock);
            return false;
        }
        //删除文件锁
        unlink($lock);
        return true;
    }

    /**
     * 还原数据库
     */
    public function importData($time){
        $path  = ROOT_PATH . $this->backupDir . $time.'/';
        $fileList = glob($path.'*.sql.php');
        if(empty($fileList)){
            $this->error = '没有发现数据库文件！';
            return false;
        }
        //还原数据库
        $dbConfig = config('DB.default');
        $Database = new \framework\ext\Dbbak($dbConfig['DB_HOST'].':'.$dbConfig['DB_PORT'],$dbConfig['DB_USER'],$dbConfig['DB_PWD'],$dbConfig['DB_NAME'],'utf8',$path);
        if(!$Database->importSql()){
            $this->error = '数据库恢复失败，请稍后再试！';
            return false;
        }
        return true;
    }

    /**
     * 删除备份文件
     */
    public function delData($time){
        $path  = ROOT_PATH . $this->backupDir . $time.'/';
        if(\framework\ext\Util::delDir($path)){
            return true;
        }else{
            return false;
        }
    }

}
