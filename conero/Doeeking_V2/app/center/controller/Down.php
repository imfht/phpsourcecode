<?php
/*
 *  2017年2月21日 星期二
 *  center /文件下载  示例
 */
namespace app\center\controller;
use think\Controller;
use hyang\Download;
use hyang\Util;
class Down extends Controller
{
    // 文件下载 传递参数
    /*  gid     用户分支ID
     *  title   用户标题
     */
    public function userlog(){
        $param = request()->param();
        if(isset($param['gid']) && $param['title']){
            $param['title'] = base64_decode($param['title']);
            $data = model('LifeLog')->where([
                'groupid' => $param['gid'],
                'title' => $param['title']
            ])->order('life_date')->select();
            $content = "";
            foreach($data as $v){
                $v = $v->toArray();
                $content .= "## ".$v['life_date']."\r\n".$v['detal']."\r\n\r\n";
            }
            Download::setConfig([
                'name' => Util::unspace($param['title'].'.md'),
                'content'   => $content
            ]);
            Download::load();
        }
    }
}