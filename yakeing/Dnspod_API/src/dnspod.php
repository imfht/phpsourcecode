<?php
/**
    * dnspod API class
    *
    * @author http://weibo.com/yakeing
    * @version 2.5
    * @documentation https://www.dnspod.com/docs/index.html (API docs)
    * Need to cooperate with Curl extension
**/
namespace dnspod_api;
class Dnspod{
    private $parameters = array(); //Public parameter
    private $uri = 'https://www.dnspod.com/'; //China Hong Kong API url
    //private $uri = 'https://www.dnspod.cn/'; //China Shandong Province API url
    public $message = 0; //return message
    public $develop = false; //test switch
    public $location = 0; //Number of location
    public $userAgent = 'Let\'s Encrypt/2.0.0 (yakeing@qq.com)'; //UA simulation
    public $AllowedType = array('SRV', 'MX', 'CNAME', 'AAAA', 'A', 'TXT', 'NS'); //Allowed type

    //Constructor
    public function __construct($uid, $token){
        function_exists('gethostbynamel') or die('The dnspod class lacks the gethostbynamel function.');
        extension_loaded('curl') or die('dnspod class lacks Curl extension.');
        $this->parameters = array(
            'login_token' => $uid.','.$token,
            'format' => 'json',
            'lang' => 'cn',
            //'user_id' => '',
            'error_on_empty' => 'no'
        );
    }//END __construct

    //Add or modify records 增加或修改记录
    public function Records($domain, $value, $name='_acme-challenge', $type='TXT', $removeRecord=true){
        $type = strtoupper($type);
        if(!in_array($type, $this->AllowedType)){
            $this->message = '['.$type.'] error in type';
            return false;
        }
        $recordList = $this->getRecordList($domain);
        if(is_bool($recordList)){
            return false;
        }
        $remove = $valueNew = $record = $recordDb = array();
        foreach($recordList['records'] as $records){
            if($records['type'] == $type AND $records['name'] == $name){
                $record[$records['id']] = $records['value'];
            }
        }
        //Need to test [$record] is an empty array 需要考验 $record 是空数组
        if(count($record)<1){
            $valueNew = is_array($value)?$value:array($value);
        //Determine that the input is an array 输入是数组
        }else if(is_array($value)){
            //$valueNew = array_diff($value,$record);
            foreach($value as $v){
                $id = array_search($v, $record);
                if(is_bool($id)){
                    $valueNew[] = $v;
                }else{
                    unset($record[$id]);
                }
            }
            if(count($valueNew)==0){
                $this->message = '[ value ] Already existed';
                return true;
            }
        //Determine the input is a numeric or string 输入是数值/字符串
        }else if(is_string($value) OR is_numeric($value)){
            $id = array_search($value, $record);
            if(is_bool($id)){
                $valueNew[0] = $value;
            }else{
                $this->message = '[ '.$value.' ] Already existed';
                return true;
            }
        //Input type error 输入类型错误
        }else{
            $this->message = '[$value] error in type';
            return false;
        }
        //Delete existing records 删除已有记录
        if($removeRecord===true){
            foreach($record as $id => $v){
                $remove[$id] = $this->recordRemove($domain, $id);
            }
        }
        //add in batches 批量添加
        if(count($valueNew)>1){
            $recordDB = array();
            foreach($valueNew as $VN){
                $recordDB[] = array('name'=>$name, 'type'=>$type, 'value'=>$VN);
            }
            $ret = $this->batchAddRecord($recordList['domain']['id'], $recordDB);
        //Single add 单项添加
        }else{
            $ret = $this->addRecord($domain, $name, $valueNew[0], $type);
        }
        //error 错误
        if(is_bool($ret)) return false;
        $this->message = 'remove: '.http_build_query($remove);
        return true;
    }//END Records

    //Copy A record 复制A记录
    public function copyArecord($copyDomain, $toDomain){
        $A = gethostbynamel($copyDomain);
        if(!is_array($A)){
            $this->message = 'Invalid domain name or no A record';
            return false;
        }
        $recordList = $this->getRecordList($toDomain);
        if(is_bool($recordList)){
            return false;
        }
        foreach($recordList['records'] as $value){
            if($value['name'] == '@' AND ($value['type'] == 'A' OR $value['type'] == 'CNAME')){
                if($value['type'] == 'A'){
                    $valueKey = array_search($value['value'], $A);
                    if($valueKey !== false){
                        unset($A[$valueKey]);
                        continue;
                    }
                }
                $this->RecordRemove($toDomain, $value['id']);
            }
        }
        foreach($A as $arr){
            $this->addRecord($toDomain, '@', $arr, 'A');
        }
        return true;
    }//END copyArecord

    //Get the API version number 获取API版本号
    public function getVersion(){
        return $this->http('Info.Version', array());
    }//END getVersion

    //Get the level allowed line 获取等级允许的线路
    public function getRecordLine($domain){
        $data = array(
            'domain' => $domain,
            'domain_grade' => 'DP_Free', //old D_Free
        );
        return $this->http('Record.Line', $data);
    }//END getRecordLine

    //Get a list of domain names 获取域名列表
    public function getDomainList(){
        $data = array(
            'offset' => 0,
            //'length' => 100,
            //'group_id' =>'',
            //'keyword' => '',
            'type' => 'all'
        );
        return $this->http('Domain.List', $data);
    }//END getDomainList

    //Get domain information 获取域名信息
    public function getDomainInfo($domain){
        $data = array(
            'domain' => $domain
        );
        return $this->http('Domain.Info', $data);
    }//END getDomainInfo

    //Get a list of records 获取记录列表
    public function getRecordList($domain){
        $data = array(
            'domain' => $domain,
            'offset' => '0',
            //'sub_domain' => 'www.'.$domain,
            //'keyword' => '',
            'length' => '20'
        );
        return $this->http('Record.List', $data);
    }//END getRecordList

    //Get details of batch tasks 获取批量任务的详情
    public function getBatchDetail($job_id){
        $data = array('job_id ' => $job_id);
        return $this->http('Batch.Detail', $data);
    }//END getBatchDetail

    //Construct a new record table 构造新记录表
    public function newRecords($name, $type, $value){
        return array(
            'sub_domain' => $name,
            'record_type' => $type,
            'record_line' => '默认',
            //'record_line_id' => '0',
            'ttl' => 600,
            //'weight' => '0',
            'value' => $value,
            'status' => 'enable'
        );
    }//END newRecords

    //Add a single record 添加单项记录
    public function addRecord($domain, $name='test', $value='test', $type='TXT'){
        $data = $this->newRecords($name, $type, $value);
        $data['domain'] = $domain;
        return $this->http('Record.Create', $data);
    }//END addRecord

    //Add records in bulk 批量添加记录
    //$record[0] = array('name'=>'w', 'type'=>'MX', 'value'='mx.com', 'mx'=>1)
    public function batchAddRecord($domain_id, $record){
        if(!is_array($record) OR 0==count($record)){
            return false;
        }
        $records = array();
        foreach($record as $v){
            $arr = $this->newRecords($v['name'], $v['type'], $v['value']);
            if($v['type'] == 'MX'){
                $arr['mx'] = isset($v['mx'])?$v['mx']:1;
            }
            $records[] = $arr;
        }
        $data = array(
            'domain_id' => $domain_id, //this is ID
            'records' => json_encode($records)
        );
        return $this->http('Batch.Record.Create', $data);
    }//END batchAddRecord

    //Modify record 修改记录
    public function recordModify($domain, $record_id, $name, $value, $type='A', $mx=1){
        $data = $this->newRecords($name, $type, $value, $mx);
        $data['domain'] = $domain;
        $data['record_id'] = $record_id;
        return $this->http('Record.Modify', $data);
    }//END recordModify

    //Delete Record 删除记录
    public function recordRemove($domain, $record_id){
     $data = array(
        'domain' => $domain,
        'record_id' => $record_id
    );
    return $this->http('Record.Remove', $data);
    }//END recordRemove

    //Transfer data 传送数据
    private function http($url, $data){
        $postFields = array_merge($this->parameters, $data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->uri.$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if(0<$this->location){
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $this->location);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('curl failed: '.curl_error($ch));
        }
        if($this->develop){
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $httpHeader = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            echo "\nURL: \n".$this->uri.$url." (".$httpCode.")\n";
            echo "\nHEADER: \n";
            echo $httpHeader ;
            echo "POST: \n";
            var_dump($postFields);
            echo "\n";
            var_dump($response);
            echo "\n---------NED------------\n";
        }
        curl_close($ch);
        $ret = json_decode($response, true);
        $this->message = $ret['status']['message'];
        if($ret['status']['code'] != 1){
            return false;
        }
        return $ret;
    }//END HTTP

    //destruct 析构函数
    public function __destruct(){
    } //END __destruct
}
