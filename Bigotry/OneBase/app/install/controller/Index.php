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

use think\Controller;
use app\install\logic;

/**
 * 安装控制器
 */
class Index extends Controller
{
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        // 执行父类构造方法
        parent::__construct();
        
        'complete' != $this->request->action() && $this->checkInstall();
    }
    
    /**
     * 检查是否已安装
     */
    public function checkInstall()
    {
        
        file_exists(APP_PATH . 'database.php') && $this->error('OneBase已经成功安装，请勿重复安装!');
    }
    
    /**
     * 安装引导首页
     */
    public function index()
    {
        
        return $this->fetch('index');
    }
    
    /**
     * 安装成功页
     */
    public function complete()
    {
        
        return $this->fetch('complete');
    }
    
    /**
     * 检测运行所需的环境设置
     */
    public function step1()
    {

        !function_exists('saeAutoLoader') && $dirfile = check_dirfile();
        
        $this->assign('dirfile', $dirfile);
        
        $this->assign('env', check_env());
        
        $this->assign('func', check_func());
        
        return $this->fetch('step1');
    }
    
    /**
     * 安装数据写入
     */
    public function step2($db = null, $admin = null)
    {
        
        if (request()->isGet()) {
            
            return $this->fetch('step2');
        }
            
        $obj = new logic\Install();
        
        // 检查安装数据
        $check_result = $obj->check($db, $admin);
        
        is_string($check_result) && $this->error($check_result);
        
        // 开始安装
        $install_result = $obj->install($db, $admin);

        is_string($install_result) ? $this->error($install_result) : $this->success('安装完成', 'complete');
    }
}
