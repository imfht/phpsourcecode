<?php
namespace app\home\controller;
use think\Db;
use clt\Lunar;
use think\facade\Env;
class Index extends Common{
    public function initialize(){
        parent::initialize();
    }
    public function index(){
        $order = input('order','createtime');
        $list=Db::name('article')->alias('a')
            ->join(config('database.prefix').'category c','a.catid = c.id','left')
            ->field('a.*,c.catdir,c.catname')
            ->order($order.' desc')
            ->limit('15')
            ->select();
        foreach ($list as $k=>$v){
            $list[$k]['time'] = toDate($v['createtime']);
            $list[$k]['url'] = url('home/'.$v['catdir'].'/info',array('id'=>$v['id'],'catId'=>$v['catid']));
        }
        $this->assign('list', $list);
        //节日插件
        if(!isMobile()){
            $m= $thisDate = date("m");
            $d= $thisDate = date("d");
            $y= $thisDate = date("Y");
            $Lunar=new Lunar();
            //获取农历日期
            $nonliData = $Lunar->convertSolarToLunar($y,$m,$d);
            $nonliData = $nonliData[1].'-'.$nonliData[2];
            $feastId = db('feast')->where(array('feast_date'=>$nonliData,'type'=>2))->value('id');
            if($feastId){
                $element = db('feast_element')->where('pid',$feastId)->select();
                $style = '<style>';
                $js = '';
                foreach ($element as $k=>$v){
                    $style .= $v['css'];
                    $js .= $v['js'];
                }
                $style .= '</style>';
                $this->assign('style', $style);
                $this->assign('js', $js);
            }else{
                $style='';
                $js='';
                $feastId = db('feast')->where(array('feast_date'=>$m.'-'.$d,'type'=>1))->value('id');
                if($feastId){
                    $element = db('feast_element')->where('pid',$feastId)->select();
                    $style = '<style>';
                    $js = '';
                    foreach ($element as $k=>$v){
                        $style .= $v['css'];
                        $js .= $v['js'];
                    }
                    $style .= '</style>';
                }
                $this->assign('style', $style);
                $this->assign('js', $js);
            }
        }
        $this->assign('demo_time',$this->request->time());
        //广告
        $adList = cache('adList');
        if(!$adList){
            $adList = Db::name('ad')->where([['open','=',1],['as_id','eq',1]])->order('sort asc')->select();
            cache('adList', $adList, 3600);
        }
        $this->assign('adList', $adList);
        return $this->fetch();
    }
    public function download($id=''){
        $map['id'] = $id;
        $files = Db::name('download')->where($map)->find();
        return download(Env::get('root_path').'public'.$files['files'], $files['title']);
    }
}