<?php

namespace Addons\Fenci;
use Common\Controller\Addon;

/**
 * 中文分词插件
 * @author 翟小斐
 */

    class FenciAddon extends Addon{

        public $info = array(
            'name'=>'Fenci',
            'title'=>'提取中文关键词',
            'description'=>'提取关键词',
            'status'=>1,
            'author'=>'翟小斐',
            'version'=>'0.1'
        );

        public function install(){
            /* 先判断插件需要的钩子是否存在 */
            $this->existHook('getKeyWords', $this->info['name'], $this->info['description']);
            return true;
        }

        public function uninstall(){
            $this->deleteHook('getKeyWords');
            return true;
        }
        public function getKeyWords($param){
            $content = trim($param);
            $config = $this->getConfig();
         
            if(empty($content)){
                return false;
            }
            
            if($config['api'] == 'pullword'){
                $this->pullWord($content);
            }
            if($config['api'] == 'discuz'){
                $this->discuz($content);
            }
            
         
        }
        public function pullWord($content){
            $config = $this->getConfig();
           
            $appkey = trim($config['appkey']);
            $num    = $config['keyword_num'] ? $config['keyword'] : 10;
            $ch = curl_init();
            $url = 'http://apis.baidu.com/apistore/pullword/words?source='.$content.'&param1=0.5&param2=1';
            
            
            $header = array(
                'apikey: '.$appkey,
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch , CURLOPT_URL , $url);
            $res = curl_exec($ch);
             
            $res = str_replace(PHP_EOL, ',', $res);
            $res = explode(',', $res);
            $keyword = array();
            foreach ($res as $row){
                $a = explode(':',$row);
                $b[$a['0']] = intval($a['1']*1000000);
                $keyword[$a['0']] = $a['1'];
                unset($b);
            }
            arsort($keyword);
            $i= 1;
            $result = '';
            foreach ($keyword as $k=>$v){
                if($i>$num){
                    break;
                }
                if(!empty($k)){
                    $result[] = $k;
                    $i++;
                }
            }
            $result = implode(',', $result);
             
            session('get_keywords',$result);
        }
        /**
         * DZ在线中文分词
         * @param $title string 进行分词的标题
         * @param $content string 进行分词的内容
         * @param $encode string API返回的数据编码
         * @return  array 得到的关键词数组
         */
        function discuz( $content = '', $encode = 'utf-8'){
           
            $content = trim($content);
            $content = strip_tags($content);
            if(strlen($content)>2400){ //在线分词服务有长度限制
                $content =  mb_substr($content, 0, 800, $encode);
            }
            $content = rawurlencode($content);
            $url = 'http://keyword.discuz.com/related_kw.html?content='.$content.'&ics='.$encode.'&ocs='.$encode;
            $xml_array=simplexml_load_file($url);  
                             //将XML中的数据,读取到数组对象中
            $result = $xml_array->keyword->result;
            $data = array();
            foreach ($result->item as $key => $value) {
                array_push($data, (string)$value->kw);
            }
            if(count($data) > 0){             
                $data = implode(',', $data);
               
                session('get_keywords',$data);
            }else{
                return false;
            }
        }

    }