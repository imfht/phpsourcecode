<?php
namespace app\cms\index\wxapp;

use app\common\controller\IndexBase;
use app\cms\model\Content AS ContentModel;
use plugins\fav\model\Fav AS FavModel;

class Live extends IndexBase
{
    /**
     * 订阅直播
     * @param number $id CMS视频ID
     * @param number $time 提前预约时间
     * @return void|\think\response\Json|void|unknown|\think\response\Json
     */
    public function fav($id=0,$time=0){
        if (empty($this->user)) {
            return '请先登录!';
        }
        
        $info = ContentModel::getInfoByid($id);
        if (empty($info)){
            return $this->err_js('内容不存在');
        }elseif($info['start_time']<time()){
            return $this->err_js('直播时间不存在,或已过期');
        }
        $map = [
            'sysid'=>M('id'),
            'aid'=>$id,
            'uid'=>$this->user['uid'],
        ];
        
        $result = FavModel::where($map)->find();
        if ($result){
            return $this->err_js('你已经预约过了!');
        }elseif(FavModel::create($map)==false){
            return $this->err_js('数据插入失败!');
        }
        
        $title = '直播开始了!';
        $url = get_url(iurl('content/show',['id'=>$id]));
        $content = "<a href=\"{$url}\" target=\"_blank\">你预约的直播“{$info['title']}”即将开始了，点击准备收看</a>";
        $array = [
            'time'=>$info['start_time'] - ($time?:1800),    //提前30分钟提醒
            'ext_sys'=>M('id'),
            'ext_id'=>$id,
            'msgtype'=>'msg,wxmsg',
        ];
        $reshut = fun('msg@send',$this->user['uid'],$title,$content,$array);
        if ($reshut===true) {
            return $this->ok_js();
        }else{
            return $this->err_js($reshut);
        }
    }
    


}
