<?php
namespace Admin\Model;
use Lib\Database;

class DatabaseModel{
    //数据库列表
    public $tables = array();
    public $error='';

    //获取数据库列表
    public function getTables(){
        return M()->query('SHOW TABLE STATUS');
    }

    //检测数据库
    public function checkTable() {
        if(C('DB_TYPE') <> 'mysql'){
            $this->error = '抱歉，数据库驱动非mysql时无法使用备份功能！';
            return false;
        }
        $tables = $this->getTables();
        if(empty($tables)){
            $this->error = '没有发现表';
            return false;
        }
        $this->tables = $tables;
        return true;
    }

    //优化数据库
    public function optimize( $tables=array() ){
        if(empty($tables) && !is_array($tables)) return false;
        $table_str = implode('`,`', $tables);

        if(M()->query("OPTIMIZE TABLE `{$table_str}`")){
            return true;
        } else {
            $this->error = '数据库优化失败，请稍后重试！';
            return false;
        }
    }

    //修复数据库
    public function repair( $tables=array() ){
        if(empty($tables) && !is_array($tables)) return false;
        $table_str = implode('`,`', $tables);

        if(M()->query("REPAIR TABLE `{$table_str}`")){
            return true;
        } else {
            $this->error = '数据库修复失败，请稍后重试！';
            return false;
        }
    }

    //备份数据库
    public function backup($tables=array()){

        if(empty($tables) && !is_array($tables)) return false;

        //检测备份路径
        $path = './Backups/';
        if(!is_dir($path)){
            mkdir($path, 0755, true);
        }elseif(!is_writeable($path)){
            $this->error='备份目录不存在或不可写，请检查后重试！';
            return false;
        }

        //读取备份配置
        $config = array(
            'path'     => realpath($path) . DIRECTORY_SEPARATOR,
            'part'     => 20971520,//分卷大小:20971520
            'compress' => 0,//开启压缩:1
            'level'    => 4,//压缩级别(1-9):4
        );

        //检测文件锁
        $lock = "{$config['path']}backup.lock";
        if(is_file($lock)){
            $this->error='检测到有一个备份任务正在执行，请稍后再试！';
            return false;
        } else {
            //创建锁文件
            file_put_contents($lock, NOW_TIME);
        }
        
        //暂存备份文件信息
        $file = array(
            'name' => date('Ymd-His', NOW_TIME),
            'part' => 1,
        );

        $Database = new \Lib\File\Database($file, $config);

        foreach ($tables as $table) {
            $start = 0;
            do{
                $start=$Database->backup($table, $start);
                if(is_array($start)){
                    $start=$start[0];
                }elseif($start === false){
                    unlink($lock);
                    $this->error="备份到{$table}表时发生错误";
                    return false;
                }
            }while( $start != 0 );
        }
        //删除文件锁
        unlink($lock);
        return true;
    }


    //获取备份文件列表
    public function backupList()
    {
        $fileDir = './Backups/';
        if(!is_dir($fileDir)) return false;

        $listFile = glob($fileDir . '*.sql*');
        if(is_array($listFile)){
            $list=array();
            foreach ($listFile as $key => $value) {
                $list[$key]['create']=date('Y-m-d H:i:s',filemtime($value));
                $list[$key]['size']=intval(filesize($value)/1024);
                $value = explode('/', $value);
                $value = end($value);
                $list[$key]['name'] = $value;
                $fileName = explode('-', $value);
                $list[$key]['time'] = $fileName[0].'-'.$fileName[1];
            }
        }
        return array_reverse($list);
    }


    //还原数据库
    public function backupImport($time){
        $name  = $time . '-*.sql*';
        $fileDir  = './Backups/';
        $path = realpath($fileDir) . DIRECTORY_SEPARATOR . $name;
        $files = glob($path);
        $list  = array();
        foreach($files as $name){
            $basename = basename($name);
            $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
            $gz = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
            $list[$match[6]] = array($match[6], $name, $gz);
        }
        ksort($list);
        //检测文件正确性
        $last = end($list);
        if(!count($list) === $last[0]){
            $this->error='备份文件可能已经损坏，请检查！';
            return false;
        }

        //还原数据库
        $start = 0;
        foreach ($list as $value) {
            $file = $value;
            $config = array(
                'path'     => realpath($fileDir) . DIRECTORY_SEPARATOR,
                'compress' => $value[2],
            );
            $Database = new \Lib\File\Database($file, $config);
            do{
                $start = $Database->import($start);
                if($start === false){
                    $this->error = '恢复数据失败，请稍后再试！';
                    return false;
                }elseif(is_array($start) && $start[0] != $start[1]){
                    $start=$start[0];
                }
            }while($start != 0);
        }
        return true;

    }

    //删除备份文件
    public function backupDel($time){
        $name  = $time . '-*.sql*';
        $fileDir = './Backups/';
        $path  = $fileDir . $name;

        array_map("unlink", glob($path));
        if(!count(glob($path))){
            return true;
        } else {
            return false;
        }
    }


}
?>