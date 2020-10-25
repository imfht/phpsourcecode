<?php
namespace Lib;

use \PDO, PDOException, Exception, Core\Config;

/**
 * 内置MYSQL连接，只需要简单配置数据连接
 * 使用方法如下
 *
 *
    //全部导出，不分卷
    $config = Config::get('APP');
    $db = new \Lib\Dbbak($config);
    //$db->tableList=array('cp_access','t2');  //设置该属性可导出指定表
    if($db->exportSql()){
        $db->remember['export']=='success' && exit('export all success');
    }
    //全部导出，分卷
    $config = Config::get('APP');
    $db = new \Lib\Dbbak($config);  
    //$db->tableList=array('cp_access','t2');  //设置该属性可导出指定表
    $db->subSize=2*1000;
    $db->remember['tableNo']=intval($_GET['tableNo']);    
    $db->remember['index'] = intval($_GET['index']);
    $db->remember['part']=intval($_GET['part']);
    if($db->exportSql()){
        $db->remember['export']=='success' && exit('export part success');
        echo "<a href=".url('index/index',$db->remember).">export part {$db->remember['part']}</a>";
    }
    
    //全部导入
    $config = Config::get('APP');
    $db = new \Lib\Dbbak($config); 
    //$db->savePath='./'; //导入操作的目录基于savePath属性默认值'./'   
    if($db->importSql()){
        $db->remember['import']=='success' && exit('import part success');        
    }
    //导入分卷
    $config = Config::get('APP');
    $db = new \Lib\Dbbak($config); 
    //$db->savePath='./'; //导入操作的目录基于savePath属性默认值'./' 
    $db->subSize=true;   
    $db->remember['part']=intval($_GET['part']);
    if($db->importSql()){
        $db->remember['import']=='success' && exit('import part success');
        echo "<a href=".url('index/index',$db->remember).">import part {$db->remember['part']}</a>";
    }
    
 */


class Dbbak
{

    private $db = NULL;

    private $affectedRows = 0;

    private $config = array();

    private $sql = "";
    
    private $time = "";

    public $tableList = null;

    public $subSize = 0;
    // 分卷大小
    public $savePath = './';
    // 存放路径
    public $remember = array(
        'part' => 0,//分卷卷标
        'index' => 0,//数据索引
        'tableNo' => 0,//表序列号
        'import'=>'',//导入状态，完成变为success
        'export'=>'',//导出状态完成变为success
    );
    // 记录，分卷跳转时使用
    public $sqlData = '';
    
    
    //构造函数初始化
    public function __construct($config = array())
    {
        $this->time = time();        
        $this->config = $config;
        $this->_connect($config); // 建立链接
    }   
    
    

    /*
     * 载入sql文件，恢复数据库
     * @param diretory $dir
     * @return booln
     * 注意:请不在目录下面存放其它文件和目录，以节省恢复时间
     */
    public function importSql($sqlFile='')
    {        
		
		if ($sqlFile){
		    if($this->_importSqlFile($sqlFile)){
		        return true;
		    }
		}
		
		$dir = $this->savePath;
		if ($this->subSize==true){//开启分卷
		    $part = intval($this->remember['part']); 
		    $file = glob($dir.'*part'.$part.'.sql.php');
		    if(empty($file) && $part>0){
		        $this->remember['import']='success';
		        return true;//找不到文件说明导入完成
		    }
		    if($this->_importSqlFile($file[0])){
		        $this->remember['part']++;
		        return true;
		    }else {
		        return false;
		    }
		}else{//未开启分卷
		    $file = glob($dir.'*_all.sql.php');
		    if($this->_importSqlFile($file[0])){
		        $this->remember['import']='success';
		        return true;
		    }else {
		        return false;
		    }
		}       
    }
    
    // 执行sql文件，恢复数据库
    protected function _importSqlFile($filename = '')
    {   
        if (empty($filename)){
            $this->remember['import'] = "未指定导入文件名";
            return false;
        }
        if (!file_exists($filename)){
            $this->remember['import'] = "$filename 不存在";
            return false;
        }
        $sqls = file_get_contents($filename);
        $sqls = substr($sqls, 13);
        $sqls = explode("\n", $sqls);
        if (empty($sqls))
            return false;
        
        foreach ($sqls as $sql) {
            if (empty($sql))
                continue;
            $this->exec($sql);
        }
        return true;
    }
    /**
     * 分卷导出
     */
    public function exportSql()
    {
    
        if (! $this->_mkdir($this->savePath)) {
            $this->error('您没有权限操作目录,备份失败');
            return false;
        }
        $table = $this->getTables(); // 获取需要备份的表
        
        if($this->subSize>0){//启用分卷备份
            if(count($table) <= $this->remember['tableNo']){// 如果开启分卷，而且表编号超出范围说明备份完成 返回true                
                $this->remember['export']='success';//导出完成标记
                return true;
            }
            // 循环表数组
            foreach ($table as $key => $val) {
                if ($key >= $this->remember['tableNo']) { // 从记录的表开始导出
                    $status = $this->_sql($val);
                    if ($status && $this->subSize)
                        break; // 如果sql准备完成，且开启分卷则退出循环
                }
            }
            $fileName = $this->savePath . date("Ymd", $this->time) . '_part' . $this->remember['part'] . '.sql.php';
            $this->remember['part'] ++; // 分卷增加
        }else{//未启用分卷备份
            foreach ($table as $key => $val) {
                $this->_sql($val);
            }
            $fileName = $this->savePath . date("Ymd", $this->time) .'_all.sql.php';
            $this->remember['export']='success';//导出完成标记
        }
    
        if (! $this->_writeSql($fileName, $this->sqlData)) {
            $this->error('文件写入失败,备份失败');
            return false;
        }
        return true;
    }
    /**
     * 生成sql语句
     *
     * @param $table 要备份的表            
     * @return $tabledump 生成的sql语句
     */
    private function _sql($table)
    {
        if ($this->subSize > 0) {
            if ($this->remember['index'] == 0) {
                $this->sqlData .= "DROP TABLE IF EXISTS $table ;\n";
                $createTable = $this->getCreateTableSql($table); // 获取表创建sql
                $createTable = str_replace("\n", "", $createTable); // 处理换行
                $createTable = str_replace("\t", "", $createTable); // 处理缩进
                $this->sqlData .= $createTable . ";\n";
            }           
            $index = $this->getFields($table);
            $index = $index[0]['Field']; // 取得第一个字段名
            $where = $this->remember['index'] > 0 ? "where $index > '{$this->remember['index']}'" : '';
            $order = "order by $index asc";
        }else{
            $this->sqlData .= "DROP TABLE IF EXISTS $table ;\n";
            $createTable = $this->getCreateTableSql($table); // 获取表创建sql
            $createTable = str_replace("\n", "", $createTable); // 处理换行
            $createTable = str_replace("\t", "", $createTable); // 处理缩进
            $this->sqlData .= $createTable . ";\n";
            $where = '';
            $order = '';
        } 
        
        $query = $this->query("SELECT * FROM $table $where $order");
        while ($row = $this->fetch($query)) {
            $field = '';
            $data = '';
            foreach ($row as $key => $val) {
                $field .= "`" . $key . "`,";
                $data .= $this->escape($val) . ",";
            }
            $field = substr($field, 0, - 1); // 去除最后的“,”
            $data = substr($data, 0, - 1); // 去除最后的“,”
            $this->sqlData .= "INSERT INTO $table ( $field ) VALUES( $data );\n";
            
            if ($this->subSize > 0 && strlen($this->sqlData) >= $this->subSize) {
                $this->remember['tableNo'] = $this->getTables($table);
                $this->remember['table'] = $table;
                $this->remember['index'] = $row[$index];                
                return true;
            }
        }
        // 开始备份新的表
        $this->remember['tableNo'] ++; // 表索引加1进入下一表
        $this->remember['table'] = $this->getTables($this->remember['tableNo']); // 获得新表的名称
        $this->remember['index'] = 0; // 索引归零
                                      // 如果没有启用分卷则返回的是true 如果启用则返回false，因为字符串还未达到最大字符数
        return $this->subSize == 0;
    }
    
    // 创建存放目录
    private function _mkdir($dir, $mode = 0777)
    {
        if (! $dir)
            return 0;
        $dir = str_replace("\\", "/", $dir);
        
        $mdir = "";
        foreach (explode("/", $dir) as $val) {
            $mdir .= $val . "/";
            if ($val == ".." || $val == "." || trim($val) == "")
                continue;
            
            if (! file_exists($mdir)) {
                if (! @mkdir($mdir, $mode)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 将数据写入到文件中
     *
     * @param file $fileName 文件名
     * @param string $str 要写入的信息
     * @return booln 写入成功则返回true,否则false
     */
    protected function _writeSql($fileName, $str)
    {
        $re = true;
        if (! $fp = @fopen($fileName, "w+")) {
            $re = false;
            $this->error("在打开文件时遇到错误，备份失败!");
        }
        if (! @fwrite($fp, '<?php exit;?>' . $str)) {
            $re = false;
            $this->error("在写入信息时遇到错误，备份失败!");
        }
        if (! @fclose($fp)) {
            $re = false;
            $this->error("在关闭文件 时遇到错误，备份失败!");
        }
        return $re;
    }
    // 执行sql查询
    public function query($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        
        try {
            $query = $this->db->query($sql);
            return $query;
        } catch (PDOException $e) {
            $errorInfo = $this->db->errorInfo(); // 取得错误信息数组
            $errorCode = $errorInfo[1];
            $errorMsg = $errorInfo[2];
            $this->error('MySQL Query Error', $errorMsg, $errorCode);
        }
    }
    
    // 执行sql命令
    public function exec($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        try {
            $query = $this->db->exec($sql);
            $this->affectedRows = $query; // 用于affectedRows()获取返回结果
            return $query;
        } catch (PDOException $e) {
            $errorInfo = $this->db->errorInfo(); // 取得错误信息数组
            $errorCode = $errorInfo[1];
            $errorMsg = $errorInfo[2];
            $this->error('MySQL Query Error', $errorMsg, $errorCode);
        }
    }
    // 从结果集中的下一行返回单独的一列
    public function fetchColumn($query, $number = 0)
    {
        return $query->fetchColumn($number);
    }
    // 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
    public function fetch($query, $result_type = PDO::FETCH_ASSOC)
    {
        return $query->fetch($result_type);
    }
    
    // 从结果集中取得一行作为关联数组，或数字数组，或二者兼有
    public function fetchall($query, $result_type = PDO::FETCH_ASSOC)
    {
        return $query->fetchall($result_type);
    }
    
    // 取得前一次 MySQL 操作所影响的记录行数
    public function affectedRows()
    {
        return $this->affectedRows ? $this->affectedRows : true;
    }
    
    // 获取上一次插入的id
    public function lastId()
    {
        return $this->db->lastInsertId();
    }
    
    // 获取数据库表
    public function getTables($tab = null)
    {
        if (! $this->tableList) {
            // 获取列表
            $this->sql = "SHOW TABLES FROM `{$this->config['DB_NAME']}`";
            $query = $this->query($this->sql);
            $data = array();
            $tableNo = 0;
            while ($row = $this->fetchColumn($query, 0)) {
                $data[$tableNo] = $row;
                $tableNo ++;
            }
            $this->tableList = empty($data) ? '' : $data;
        }
        // 无传入参数则将所有表组合为一个数组
        if (is_null($tab)) {
            return $this->tableList;
        }
        // $tab有参数时根据情况获取数据
        foreach ($this->tableList as $tableNo => $table) {
            // 当使用$this->getTables(1);传入参数是索引值，返回表名
            if (is_numeric($tab) && $tableNo == $tab) {
                return $table;
            }
            // 当使用$this->getTables('table');传入参数时表名，返回表的索引值
            if (is_string($tab) && $table == $tab) {
                return $tableNo;
            }
        }
        // 未找到符合条件数据返回空
        return '';
    }
    // 获取建表sql
    public function getCreateTableSql($table)
    {
        $this->sql = "SHOW CREATE TABLE $table";
        $query = $this->query($this->sql);
        return $this->fetchColumn($query, 1);
    }
    // 获取表结构
    public function getFields($table)
    {
        $this->sql = "SHOW FULL FIELDS FROM {$table}";
        $query = $this->query($this->sql);
        return $this->fetchall($query);
    }
    
    // 获取行数
    public function count($table, $where)
    {
        $this->sql = "SELECT count(*) FROM $table $where";
        $query = $this->query($this->sql);
        return $query->fetchColumn();
    }
    
    // 数据过滤
    public function escape($value)
    {
        if (is_array($value)) {
            return array_map(array(
                $this,
                'escape'
            ), $value);
        } else {
            return "'" . addslashes($value) . "'";
        }
    }
    
    // 输出错误信息
    public function error($message = '', $error = '', $errorno = '')
    {
        if (DEBUG) {
            $error_sql = str_replace(Config::get('DB_PREFIX'), '[PRE]', $this->sql);
            $error = str_replace(Config::get('DB_PREFIX'), '[PRE]', $error);
            $str = " {$message}<br>
            <b>SQL</b>: {$error_sql}<br>
            <b>错误详情</b>: {$error}<br>
            <b>错误代码</b>:{$errorno}<br>";
        } else {
            $str = "<b>出错</b>: $message<br>";
        }
        throw new Exception($str);
    }
    
    // 建立数据库链接
    private function _connect($config)
    {
        $db = $config;
        
        try {
            $errorCode = '00000';
            $errorMsg = '';
            $dns = "mysql:host={$db['DB_HOST']};port={$db['DB_PORT']};dbname={$db['DB_NAME']}";
            $this->db = new PDO($dns, $db['DB_USER'], $db['DB_PWD']);
            $this->db->setAttribute(PDO::ATTR_PERSISTENT, $db['DB_PCONNECT']); // 设置数据库连接为持久连接
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // 设置抛出错误
            $this->db->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL); // 指定数据库返回的NULL值在php中对应的数值 不变
            $this->db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL); // 强制PDO 获取的表字段字符的大小写转换,原样使用列值
            $this->db->exec("SET NAMES {$db['DB_CHARSET']}"); // 设置编码
        } catch (PDOException $e) {
            $errorCode = $e->getCode();
            $errorMsg = $e->getMessage();
        }
        if ($errorCode != '00000') {
            $this->error('无法连接到数据库服务器', $errorMsg, $errorCode);
        }
    }
    
    // 关闭数据库
    public function __destruct()
    {
        $this->db = NULL;
    }
}

