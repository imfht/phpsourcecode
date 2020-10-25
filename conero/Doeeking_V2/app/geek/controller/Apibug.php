<?php
/*
 * 2017年1月17日 星期二
 * 接口调试工具
*/
namespace app\geek\controller;
use think\Controller;
use think\Debug;
use hyang\Validate;
use hyang\Location;
use hyang\Net;
class Apibug extends Controller
{
    public function index()
    {
        // 动态导航栏
        $this->dynamicNavPlus = ['text'=>'接口调试','url'=>''];
        geek_navBar($this->view,$this);

        $udata = count($_POST)>0? $_POST:$_GET;
        $url = isset($udata['url'])? $udata['url']:'';
        $pages = ['url'=>$url];
        if($url){   // 实际的 url
            $data = parse_url($url);
            $urlParam = $data;
            $host = $data['host'];
            // 数据保存
            if(!(Validate::ipv4($host))){
                $ip = gethostbyname($host);
                $data['id'] = $ip;
                Location::setIp($ip);
                $tmpData = Location::getLocation();
                if(isset($tmpData['code']) && 0 === $tmpData['code']){
                    // $data = array_merge($data,$tmpData['data']);
                    $tmpData = $tmpData['data'];
                    $data['地区'] = $tmpData['country'] . '('. $tmpData['country_id'] . ') '. $tmpData['area'] . '('. $tmpData['area_id'] . ')';
                    $data['城市'] = $tmpData['city'] . '.' . $tmpData['region'] . '('. $tmpData['city_id'] .'/'. $tmpData['region_id'] .')';
                    $data['区县'] = $tmpData['county'] . '('. $tmpData['county_id'] .')';
                    $data['运营商'] = $tmpData['isp'] . '('. $tmpData['isp_id'] .')';
                    
                }
                // println($tmpData);
            }
            if(isset($udata['post'])) $data['post'] = $udata['post'];
            $this->assign('urlParam',$data);
            
            // 
            Debug::remark('begin');
            $data = get_headers($url,true);
            $this->assign('urlHeader',$data);       
            $context = '没有任何记录';
            if(!isset($udata['noContent']) || (isset($udata['noContent']) && 'Y' != $udata['noContent'])){
                $opt = ['url' => $url];
                if(isset($udata['post'])) $opt['post'] = $udata['post'];
                $context = Net::curl($opt);
                // $context = file_get_contents($url);
                $this->assign('context',$context);
            }
            // println($udata);            

            Debug::remark('end'); 
            $timeRpt = Debug::getRangeTime('begin','end',6);
            $this->assign('run',[
                'times'     => $timeRpt.'s',
                'core'      => Debug::getRangeMem('begin','end').'kb'
            ]);
            // 数据保存到数据库
            $model = model('ApiBug');   
            $uInfo = uInfo();
            $svdata = [
                'url'   =>$url,
                'method'=> isset($udata['post'])? 'POST':'GET',
                'ip'    => request()->ip(),
                'nick'  => isset($uInfo['nick'])? $uInfo['nick'] : 'GUEST',
                'content' => $context,
                'sec'   => $timeRpt
            ];
            $svdata = array_merge($svdata,$urlParam);
            if(isset($uInfo['code'])) $svdata['code'] = $uInfo['code'];
            if(isset($udata['post'])) $svdata['data'] = json_encode($udata['post']);                     
            $model->save($svdata);
        }
        $this->assign('pages',$pages);
        $this->loadScript([
            'title'=>'Conero-接口调试工具','js'=>['ApiBug/index'],'bootstrap'=>true
        ]);
        return $this->fetch();
    }
}