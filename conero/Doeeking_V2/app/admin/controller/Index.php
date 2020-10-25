<?php
namespace app\admin\controller;
use think\Loader;
use think\Controller;
class Index extends Controller
{
    // boostrap 风格首页
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'系统管理 - Conero','css'=>['index/index'],'js'=>['index/index'],'bootstrap'=>true
        ]);
        $page = [
            'siderBar' => $this->createSiderBar()
        ];
        $this->assign('admin',$page);//siderBar')
        return $this->fetch();
    }    
    private function createSiderBar()
    {
        $html = '';
        $modules = $this->croDb('sys_menu')->where(['groupid'=>request()->module()])->field('url,code_mk,descrip,param')->select();
        foreach($modules as $v){
            $param = isset($v['param'])? json_decode($v['param'],true):null;
            $html .= '<li><a href="javascript:void(0);" class="herf_link" dataurl="'.$v['url'].'" dataid="'.$v['code_mk'].'">'.$v['descrip'].'</a> <span class="badge">'.(empty($param)? '':$this->croDb($param['model'])->count()).'</span></li>';
        }
        
        // println($modules);
        /*
        // 考虑从数据库中加载
        $modules = [
            'user'  => ['text'=>'用户管理','model'=>'net_user'],
            'textpl'  => ['text'=>'系统模板','model'=>'sys_texttpl'],
            'table'  => ['text'=>'后台数据','model'=>'table_view'],
            'sconst'  => ['text'=>'系统常量','model'=>'sys_site'],
            'files'  => ['text'=>'系统文件一览','model'=>'sys_file'],
            'lisa'  => ['text'=>'系统模块','model'=>''],
            'info'  => ['text'=>'系统信息发布','model'=>'sys_infor'],
            'midden'  => ['text'=>'数据回收箱','model'=>'sys_destory_bak'],
            'menu'  => ['text'=>'系统菜单','model'=>'sys_menu'],
        ];
        */
        /*
        foreach($modules as $k => $v){
            $html .= '<li><a href="javascript:void(0);" class="herf_link" dataid="'.$k.'">'.$v['text'].'</a> <span class="badge">'.(empty($v['model'])? '':$this->croDb($v['model'])->count()).'</span></li>';
        }
        */
        return $html;
    }
    // 首页展示栏
    public function home()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统管理','bootstrap'=>true
        ]);
        return $this->fetch();
    }
}
