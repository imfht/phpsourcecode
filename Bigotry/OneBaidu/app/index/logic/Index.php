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

namespace app\index\logic;

/**
 * 百度首页逻辑
 */
class Index extends IndexBase
{

    //百度首页HTML
    public function getHtml($baidu_url = '')
    {
        
        $data = get_curl_data($baidu_url);
        
        //过滤头部返回的http信息
        $p = '#<html>(.+?)</html>#s';
        
        $m = [];
        
        preg_match($p,$data,$m);
        
        $data = '<html>'.$m[1].'</html>';
       
        return cdomain($data);
    }

    //百度搜索页
    public function getShtml($param = [], $top_list_template = '')
    {
        
        $wd = $param['wd'];
        $pn = empty($param['pn']) ? '0'     : $param['pn'];
        $rn = empty($param['rn']) ? '10'    : $param['rn'];
        
        $baidu_url = "http://www.baidu.com/s?wd=$wd&pn=$pn&rn=$rn";
        
        $list_data = get_curl_data($baidu_url);
        
        $p = '#<html>(.+?)</html>#s';
        
        $m = [];
        
        preg_match($p,$list_data,$m);
        
        $data = '<html>'.$m[1].'</html>';
        
        //搜索关键字标红
        if(!is_numeric($wd)){ $top_list_template = preg_replace('#'.$wd.'#', '<em>'.$wd.'</em>', $top_list_template); }
        
        return cdomain(preg_replace('#id="content_left"\>#', $top_list_template, $data));
    }
    
    //获取百度定义数据
    public function getBaiduList($location = 0)
    {
        
        return $this->modelBaidu->getList(['location' => $location], true, 'sort desc', false);
    }
}
