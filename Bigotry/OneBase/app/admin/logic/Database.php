<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\admin\logic;

use think\Db;

/**
 * 数据库备份逻辑
 */
class Database extends AdminBase
{
    
    /**
     * 获取数据表列表
     */
    public function getTableList()
    {
        
        $list  = Db::query('SHOW TABLE STATUS');
        
        return array_map('array_change_key_case', $list);
    }
    
    /**
     * 获取数据表列表只包含名称的索引数组
     */
    public function getTableListIndex()
    {
        
        $table_list = $this->getTableList();
        
        return  array_extract($table_list, 'name');
    }
    
    /**
     * 获取备份目录，不存在则创建
     */
    public function getBackupDir()
    {
        
        $path = "../data/";
        
        !is_dir($path) && mkdir($path, 0755, true);
        
        return $path;
    }
    
    /**
     * 通过time获取备份路径
     */
    public function getBackupPathByTime($time = 0)
    {
        
        // 获取备份文件信息
        $name  = date('Ymd-His', $time) . '-*.sql*';
        
        $path  = realpath($this->getBackupDir()) . DIRECTORY_SEPARATOR . $name;
        
        return $path;
    }

    /**
     * 数据备份
     */
    public function dataBackup($param = [])
    {
        
        $path = $this->getBackupDir();
        
        $config = [
            'path'     => realpath($path) . SYS_DS_PROS,
            'part'     => config('data_backup_part_size'),
            'compress' => config('data_backup_compress'),
            'level'    => config('data_backup_compress_level'),
        ];
        
        // 检查是否有正在执行的任务
        $lock = "{$config['path']}backup.lock";
        
        if (is_file($lock)) { return [RESULT_ERROR, '检测到有一个备份任务正在执行，请稍后再试！']; }
        
        // 创建锁文件
        file_put_contents($lock, TIME_NOW);
        
        // 检查备份目录是否可写
        if (!is_writeable($config['path'])) {  return [RESULT_ERROR, '备份目录不存在或不可写，请检查后重试！']; }
        
        session('backup_config', $config);
        
        // 生成备份文件信息
        $file = ['name' => date('Ymd-His', TIME_NOW), 'part' => DATA_NORMAL ];
        
        session('backup_file', $file);
        session('backup_tables', $param['tables']);
        
        $database = new \ob\Database($file, $config);
        
        if (false == $database) { return [RESULT_ERROR, '备份初始化失败！']; }
        
        $tab = array('id' => 0, 'start' => 0);
        
        header('Content-Type:application/json; charset=utf-8');
        
        exit(json_encode(array('tables' => $param['tables'], 'tab' => $tab, 'status' => DATA_NORMAL)));
    }
    
    /**
     * 数据备份，步骤2
     */
    public function dataBackupStep2($param = [])
    {
        
        $id      = $param['id'];
        $start   = $param['start'];
        
        $tables = session('backup_tables');
        
        $database = new \ob\Database(session('backup_file'), session('backup_config'));
        
        $start  = $database->backup($tables[$id], $start);
        
        header('Content-Type:application/json; charset=utf-8');
        
        if (false === $start) {
            
            exit(json_encode(array('status' => DATA_NORMAL, 'msg' => '备份出错')));
        } elseif (0 === $start) {
            
            if(isset($tables[++$id])){
                
                $tab = array('id' => $id, 'start' => 0);
                exit(json_encode(array('msg' => '备份完成', 'tab' => $tab, 'status' => DATA_NORMAL)));
            } else {
                
                $config = session('backup_config');

                @unlink($config['path'] . 'backup.lock');
                session('backup_tables', null);
                session('backup_file', null);
                session('backup_config', null);
                exit(json_encode(array('msg' => '备份完成', 'status' => DATA_NORMAL)));
            }
        } else {
            
            $tab  = array('id' => $id, 'start' => $start[0]);
            $rate = floor(100 * ($start[0] / $start[1]));
            exit(json_encode(array('msg' => "正在备份...({$rate}%)", 'tab' => $tab, 'status' => DATA_NORMAL)));
        }
    }

    /**
     * 优化 or 修复 表
     */
    public function optimize($mark = true)
    {
        
        $table_list = $this->getTableListIndex();
        
        $tables = implode('`,`', $table_list);
        
        $list = $mark ? Db::query("OPTIMIZE TABLE `{$tables}`") : Db::query("REPAIR TABLE `{$tables}`");

        $text = $mark ? '优化' :  '修复';
        
        if (!$list) { return [RESULT_ERROR, $text . '出错']; }
        
        $mark ? action_log('优化', '数据库优化') : action_log('修复', '数据库修复');
        
        return [RESULT_SUCCESS, $text . '完成'];
    }
    
    /**
     * 获取备份列表
     */
    public function getBackupList()
    {
        
        $path = realpath($this->getBackupDir());

        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        
        $glob = new \FilesystemIterator($path,  $flag);
        
        return $this->backupListHandle($glob);
    }
    
    /**
     * 备份列表处理
     */
    public function backupListHandle($glob = null)
    {
        
        $list = [];
        
        foreach ($glob as $name => $file)
        {
            
            if (!preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) { continue; }
                
            $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');

            $date = "{$name[0]}-{$name[1]}-{$name[2]}"; $time = "{$name[3]}:{$name[4]}:{$name[5]}"; $part = $name[6];

            if (isset($list["{$date} {$time}"])) {
                
                $info         = $list["{$date} {$time}"];
                
                $info['part'] = max($info['part'], $part);
                
                $info['size'] = $info['size'] + $file->getSize();
            } else {
                
                $info['part'] = $part;
                
                $info['size'] = $file->getSize();
            }
            
            $extension        = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            
            $info['compress'] = ($extension === 'SQL') ? '-' : $extension;
            
            $info['time']     = strtotime("{$date} {$time}");

            $list["{$date} {$time}"] = $info;
        }
        
        return $list;
    }
    
    /**
     * 删除备份文件
     */
    public function backupDel($time = 0)
    {
        
        $path = $this->getBackupPathByTime($time);
        
        array_map("unlink", glob($path));
        
        if (count(glob($path))) { return [RESULT_ERROR, '备份文件删除失败，请检查权限！']; }
        
        action_log('删除', '数据库备份文件删除，path：'. $path);
        
        return [RESULT_SUCCESS, '备份文件删除成功'];
    }
    
    /**
     * 数据还原
     */
    public function dataRestore($param = [])
    {
        
        header('Content-Type:application/json; charset=utf-8');
        
        if (is_numeric($param['time']) && !isset($param['part']) && !isset($param['start'])) {
            
            $path   = $this->getBackupPathByTime($param['time']);

            $files  = glob($path);

            $list   = [];
            
            foreach($files as $name)
            {
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }

            ksort($list);
            
            // 检测文件正确性
            $last = end($list);

            if (!(count($list) === $last[0])) { return [RESULT_ERROR, '备份文件可能已经损坏，请检查！']; }
            
            session('backup_list', $list);
                
            exit(json_encode(array('msg' => "初始化完成,数据还原中...", 'part' => 1, 'start' => 0, 'status' => DATA_NORMAL)));
        }  elseif(is_numeric($param['part']) && is_numeric($param['start'])) {
            
            $part  = $param['part'];
            $start = $param['start'];
            
            $list  = session('backup_list');
        
            $path = $this->getBackupDir();

            $db = new \ob\Database($list[$part], array(
                    'path'     => realpath($path) . SYS_DS_PROS,
                    'compress' => $list[$part][2]
                    ));


            $start = $db->import($start);

            if(false === $start){
                exit(json_encode(array('msg' => "还原数据出错", 'status' => DATA_ERROR)));
            } elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    exit(json_encode(array('msg' => "正在还原...#{$part}", 'part' => $part, 'start' => 0, 'status' => DATA_NORMAL)));
                } else {
                    session('backup_list', null);
                    exit(json_encode(array('msg' => "还原完成", 'status' => DATA_NORMAL)));
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1]));
                    exit(json_encode(array('msg' => "正在还原...#{$part} ({$rate}%)", 'part' => $part, 'start' => $start[0], 'status' => DATA_NORMAL)));
                } else {
                    $data['gz'] = 1;
                    exit(json_encode(array('msg' => "正在还原...#{$part}", 'part' => $part, 'start' => $start[0], 'gz' => 1,'status' => DATA_NORMAL)));
                }
            }
        } else {
            exit(json_encode(array('msg' => "还原数据出错", 'status' => DATA_ERROR)));
        }
    }
}
