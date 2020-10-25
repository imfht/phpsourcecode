<?php
/**
 * 「PHP联盟」
 * 公共认证控制器
 * @author：楚羽幽
 */
class CommonController extends Controller
{
    //检测安装
    public function __init()
    {
        if(!file_exists(APP_PATH . 'Install/Lock.php'))
        {
            go(U('/Install/Index/index'));
        }       
    }
	// 模板主题目录
	protected $VIEW_DIR;
	function __construct()
	{
        $this->VIEW_DIR = 'Theme/'.C('STYLE').'/';
		parent::__construct();
	}

	/**
     * 404页面
     * @author：楚羽幽
     */
    protected function _404()
    {
        set_http_state(404);
        $this->display('Theme/system/404');
        exit;
    }
}
?>