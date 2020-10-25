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

namespace app\install\controller;

/**
 * Install Index
 */
class Index extends InstallBase
{
    
    // 安装首页
    public function index()
    {
        
        return $this->fetch('index');
    }
    
    // 安装完成
    public function complete()
    {
        
        $step = session('step');

        if (!$step) {
            
            $this->redirect('index');
        } elseif($step != 3) {
            
            $this->redirect("Install/step{$step}");
        }
        
        $this->assign('info', session('config_file'));
            
        session('step', null);
        session('error', null);
        session('update',null);
        
        return view('complete');
    }
    
}
