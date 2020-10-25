<?php
/**
 * 公共文件
 *
 * @package Model
 * @author chengxuan <i@chengxuan.li>
 */
set_time_limit(100);
define('DIR_ROOT', __DIR__ . '/');
require DIR_ROOT . 'config.inc.php';
require DIR_ROOT . 'dnspod.php';
abstract class Comm {

    /**
     * 获取DNSPOD操作对象
     * 
     * @return \Dnspod\Api
     */
    static public function dnspod() {
        $dnspod = new \Dnspod\Api(Config::TOKEN);
        return $dnspod;
    }
    
    /**
     * 更新DNS记录
     * 
     * @param string  $ip   IP地址
     * @param boolean $ddns 是否自动使用DDNS更新
     * 
     * @throws \Dnspod\Exception
     * 
     * @return stdClass|boolean
     */
    static public function updateRecord($ip, $ddns = false) {
        $ip = trim($ip);
        $dnspod = self::dnspod();
        $domain = $dnspod->domainInfo(Config::DOMAIN_ROOT);
        
        $action = 'add';
        try {
            $records = $dnspod->recordList($domain->domain->id, Config::DOMAIN_RECORD);
            $remove_all = false;
            foreach($records->records as $record) {
                if($record->type !== 'A' || $remove_all) {
                    //只要一条A记录，别的都删除
                    $dnspod->recordRemove($domain->domain->id, $record->id);
                    Comm::output(Config::DOMAIN_RECORD . ":{$record->value}", 'remove');
                } elseif($record->value === $ip) {
                    //和之前一样，不改
                    $action = 'ignore';
                    $remove_all = true;
                } else {
                    //修改
                    $action = 'modify';
                    $remove_all = true;
        
                    if($ddns) {
                        $response = $dnspod->recordDdns($domain->domain->id, $record->id, Config::DOMAIN_RECORD);
                    } else {
                        $response = $dnspod->recordModify($domain->domain->id, Config::DOMAIN_RECORD, $record->id, 'A', $ip);
                    }
                    
                }
            }
        } catch(\Dnspod\Exception $e) {
            //没有记录，增加，其它情况抛错
            if($e->getCode() != 10) {
                throw $e;
            }
        }
        
        //新增域名
        if($action === 'add') {
            $response = $dnspod->recordCreate($domain->domain->id, Config::DOMAIN_RECORD, 'A', $ip);
        }
        Comm::output(Config::DOMAIN_RECORD . ":{$ip}", $action);
        
        if(!empty($response)) {
            Comm::output($response->status->message);
            return $response;
        }
        
        return false;
    }
    
    /**
     * 自动动态更新IP
     * 
     * @return stdClass
     */
    static public function ddns() {
        $fp = fsockopen('ns1.dnspod.net', 6666);
        $ip = fread($fp, 16);
        fclose($fp);
        
        
        return self::updateRecord($ip, true);
    }
    
    
    /**
     * 输出一行数据并换行
     * 
     * @param string $string
     * 
     * @return void
     */
    static public function println($string) {
        echo "{$string}\n";
    }
    
    /**
     * 输出一行内容
     * 
     * @param string $string
     * @param string $action
     * 
     * @return void
     */
    static public function output($string, $action = 'INFO') {
        $time = date('Y-m-d H:i:s');
        $action = strtoupper($action);
        self::println("[{$action}] {$time} {$string}");
    }
}


/**
 * 处理默认异常
 */
set_exception_handler(function(Exception $exception) {
    echo "\033[35m[" . get_class($exception) . "] (" . $exception->getCode() . ")\033[0m";
    echo " \033[33m" . $exception->getMessage() . "\033[0m\r\n";
    echo $exception->getFile() . ' (' . $exception->getLine() . ")\r\n";
    
    echo "\r\n\033[36m" . $exception->getTraceAsString() . "\033[0m\r\n";
});
