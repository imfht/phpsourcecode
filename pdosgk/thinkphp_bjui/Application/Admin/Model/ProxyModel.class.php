<?php

namespace Admin\Model;

use Think\Model;

/** 
 * 自动更换代理, 代理堵塞后, 寻找连接最快的继续连接.
 * @author Lain
 * 
 */
class ProxyModel extends Model {

    protected $_validate = array(
            //array('host', '', '重复', self::EXISTS_VALIDATE, 'unique'),
    );
    
    public function _initialize(){
        define('PROXY_STATUS_OK', 99);
        define('PROXY_STATUS_FAILED', 1);
        define('PROXY_STATUS_NORMAL', 0);
        
        define('LOSE_TIME', 10);     //丢失次数警戒点
    }
    public function getCron(){
        //查看计划任务
        $map['name'] = 'proxy';
        $detail = M('Cron')->where($map)->find();
        if(!$detail){
            M('Cron')->add($map);
        }
        return $detail;
    }
    
    //更新下一次计划任务时间
    public function updateCron($success = 0, $next_time = 3600){
        $map['name'] = 'proxy';
        $info['last_cron_time'] = NOW_TIME;
        //成功则更新下一次执行时间
        if($success == 1)
            $info['next_cron_time'] = NOW_TIME + $next_time;
        
        M('Cron')->where($map)->save($info);
        return true;
    }
    /**
     * 
     * @param number $type 1, 国内代理, 2为国外代理
     * @return boolean
     */
    public function getWebProxy($type = 1){
        //状态为99的代理链接数量, 如果超过10个, 则跳过
        $proxy_ok_count = $this->where(array('status' => 99))->count();
        if($proxy_ok_count < 10){
            //检测列表中的一个代理是否可连
            //获取代理IP列表
            $this->checkProxyList($type);
        }
        
        $map_check['status'] = PROXY_STATUS_NORMAL;
        $proxy_data = $this->where($map_check)->order('id')->find();
        
        if($proxy_data && $this->checkProxy($proxy_data['host'], $proxy_data['port'], $type)){
            //更新此IP的status状态为99
            $this->where(array('id' => $proxy_data['id']))->save(array('status' => PROXY_STATUS_OK));
        }else{
            $this->where(array('id' => $proxy_data['id']))->save(array('status' => PROXY_STATUS_FAILED));
        }
        //取出一个当日可用代理, 并且status值最高的
        $proxy_data = $this->getBestProxy();
        if($proxy_data){
            //更换当前代理, 如果status值不是99, 则继续检测IP代理
            if(S('proxy_host') != $proxy_data['host'] && $proxy_data['status'] != PROXY_STATUS_OK){
                return false;
            }
            return true;
        }else{
            return false;
        }
        
    }
    
    //获取代理列表
    private function checkProxyList($type = 1){
        $this->get_xicidaili($type);
        //$this->get_66ip($type);
        return true;
    }
    
    //获取www.66ip.cn可用列表
    private function get_66ip($type = 1){

        //查看是否还有可用的IP
        $map['status'] = PROXY_STATUS_NORMAL;
        $map['verify_time'] = array('gt', NOW_TIME - 3600*12);
        
        $exist = $this->where($map)->find();
        if($exist)
            return true;
        
        //清空当前表不可用的代理
        $this->clearProxy();
        
        $list_array = array(
                1   => 'http://www.66ip.cn/areaindex_13/1.html',
                2   => 'http://www.66ip.cn/areaindex_33/1.html'
        );
        //如果没有, 跑网页获取
        //获取代理列表
        $url = $list_array[$type];
        $snoopy = new \Lain\Snoopy;
        $snoopy->fetch($url);

//         $snoopy->proxy_host = '119.51.62.88';
//         $snoopy->proxy_port = '80';
//         $snoopy->agent      = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.75 Safari/537.36';
        $html_code = $snoopy->results;
        //使用QueryList解析html
        //$query_content = \QL\QueryList::Query($html_code, array('','text'))->data;
        $query_content = \QL\QueryList::Query($html_code, array('proxy_html' => array('#footer table tr:gt(0)','html')))->data;
        $hosts = array();
        foreach ($query_content as $proxy){
            //只取前10个代理
            if(count($hosts) >= 10)
                break;
            $info = array();
            $proxy_data = \QL\QueryList::Query($proxy['proxy_html'],
                            array(
                                    'proxy' => array('td:nth-child(1)','html'),
                                    'port' => array('td:nth-child(2)', 'html'),
                                    //'verify_time' => array('td:nth-child(5)', 'html'),
                            ))->data;
            //保存到数据库
            $info['host']       = $proxy_data[0]['proxy'];
            $info['port']       = $proxy_data[0]['port'];
            $info['verify_time']= NOW_TIME;
            //$info['verify_time']= strtotime(str_replace(' 验证', '', $proxy_data[0]['verify_time']));
            $info['status']     = PROXY_STATUS_NORMAL;
            
            //不重复添加
            $exist_detail = $this->where(array('host' => $info['host']))->find();
            if($exist_detail){
                continue;
            }
            $this->add($info);
            $hosts[] = $info['host'];
        }
        return true;
        
    }
    
    //获取xicidaili列表
    private function get_xicidaili($type = 1){
        //查看是否还有可用的IP
        $map['status'] = PROXY_STATUS_NORMAL;
        $map['verify_time'] = array('gt', NOW_TIME - 3600*12);
        
        $exist = $this->where($map)->find();
        if($exist)
            return true;
        
        //清空当前表
        $this->clearProxy();

        $list_array = array(
                1   => 'http://www.xicidaili.com/nt',
                2   => 'http://www.xicidaili.com/wt/'
        );
        //$url = 'http://www.baidu.com';
        $snoopy = new \Lain\Snoopy;
        $snoopy->fetch($list_array[$type]);

//         $snoopy->proxy_host = '119.51.62.88';
//         $snoopy->proxy_port = '80';
//         $snoopy->agent      = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.75 Safari/537.36';
        $html_code = $snoopy->results;
        //使用QueryList解析html
        $query_content = \QL\QueryList::Query($html_code, array('proxy_html' => array('#ip_list tr:gt(0)','html')))->data;
        //$query_content = \QL\QueryList::Query($url, array('proxy_html' => array('#ip_list tr:gt(0)','html')))->data;
        
        $hosts = array();
        foreach ($query_content as $proxy){
            //只取前10个代理
            if(count($hosts) >= 10)
                break;
            $info = array();
            $proxy_data = \QL\QueryList::Query($proxy['proxy_html'], 
                            array(
                                    'proxy' => array('td:nth-child(2)','html'), 
                                    'port' => array('td:nth-child(3)', 'html'),
                                    //'verify_time' => array('td:nth-child(10)', 'html'),
                            ))->data;
            //保存到数据库
            $info['host']       = $proxy_data[0]['proxy'];
            $info['port']       = $proxy_data[0]['port'];
            $info['verify_time']= NOW_TIME;
            $info['status']     = PROXY_STATUS_NORMAL;
            //不重复添加
            $exist_detail = $this->where(array('host' => $info['host']))->find();
            if($exist_detail){
                continue;
            }
            $this->add($info);
            $hosts[] = $info['host'];
        }
        return true;
    }
    
    //检测代理IP是否可用
    private function checkProxy ($proxy, $port, $type = 1)
    {
        $check_rules = array(
                1   => array('url' => 'http://www.baidu.com/', 'keyword' => '百度一下'),
                2   => array('url' => 'https://www.google.com.hk/?gws_rd=ssl', 'keyword' => '<title>Google</title>')
        );
        //使用百度来检测
        $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh- CN; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5 FirePHP/0.2.1";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理服务器端口
        curl_setopt($ch, CURLOPT_URL, $check_rules[$type]['url']);//设置要访问的IP
        //curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);//模拟用户使用的浏览器
        //@curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
        curl_setopt($ch, CURLOPT_TIMEOUT, 10 ); //设置超时时间
        //curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
        //谷歌为https访问
        if($type == 2){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 检查证书中是否设置域名
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        if($result !== false && strpos($result, $check_rules[$type]['keyword']) !== false)
            return true;
        else
            return false;
    }
    
    //丢失率统计
    public function loseConnect($host, $port){
        
        //查看该代理丢失次数
        $map['host'] = $host;
        //$map['port'] = $port;
        
        $lose_time = $this->where($map)->getField('lose');
        if($lose_time > LOSE_TIME){
            //催促下一次更新快一点
            $this->updateCron(1, 1);
            //重新选择最佳代理
            $this->getBestProxy();
            $info['status']     = 90;
        }
        
        $info['lose_continuous'] = array('exp', 'lose_continuous+1');   //持续丢失次数+1
        $info['lose'] = array('exp', 'lose+1');
        $info['lose_rate']   = array('exp', 'lose/(lose+success)');//丢失率
        $this->where($map)->save($info);
        return true;
    }
    
    //成功连接, 则统计次数
    public function successConnect($host, $post){
        $map['host'] = $host;
        
        $info['success']    = array('exp', 'success+1');
        $info['lose_rate']  = array('exp', 'lose/(lose+success)');   //丢失率
        $info['lose_continuous'] = 0;   //连续丢失次数, 成功一次, 就清零
        $this->where($map)->save($info);
        return true;
    }
    
    //获取最快的代理
    private function getBestProxy(){
        $map['status'] = array('gt', PROXY_STATUS_FAILED);
        $map['lose_continuous'] = array('lt', 20);      //连续丢失小于20次
        //按丢失率来选择最佳代理
        $proxy_data = $this->where($map)->field('id, host, port, `status`, lose_rate')->order('success DESC')->find();
        if($proxy_data && $proxy_data['host'] != S('proxy_host')){
            //保存到缓存
            S('proxy_host', $proxy_data['host'], 3600*24*7);
            S('proxy_port', $proxy_data['port'], 3600*24*7);
            //显示当前正在使用的代理, 用fast暂时 替代
            $this->where(array('fast' => 1))->save(array('fast' => null));
            $this->where(array('id' => $proxy_data['id']))->save(array('fast' => 1));
        }
        return $proxy_data;
    }
    
    //清理列表
    private function clearProxy(){
        $this->where('`status`=1 OR `lose_rate`=1')->delete();
    }
}

?>