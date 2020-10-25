<?php
// 项目管理 - 2016年12月30日 星期五
namespace app\center\Logic;
use app\center\Logic\Controller;
class Project extends Controller{
    public function main()
    {
        $app = $this->app;
        // 获取项目列表        
        $list = '';
        $data = $app->croDb('project_list')->where('user_code',uInfo('code'))->order('prjtype')->select();
        foreach($data as $v){
            $list .= '
                <a href="/conero/geek/project.html?code='.$v['pro_code'].'" class="list-group-item" target="_blank">
                    <h4 class="list-group-item-heading">'.$v['pro_code'].'<small>'.$v['pro_name'].'</small></h4>
                    <p class="list-group-item-text">'.$v['descrip'].'</p>
                    <p class="list-group-item-text">创建时间：<em>'.$v['edittm'].'</em> 是否公开:<em>'.$v['private_mk'].'</em></p>
                </a>
            ';
        }
        $this->assign('project_list',$list);
        return $this->fetch('project');
    }
}