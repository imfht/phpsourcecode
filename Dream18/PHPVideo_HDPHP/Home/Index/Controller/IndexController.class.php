<?php
/**
 * 前台首页控制器
 */
class IndexController extends CommonController
{
	/**
	 * [__init 构造函数]
	 * @return [type] [description]
	 */
	public function __init()
    {
        // 网站关闭检测
        $this->CheckWebClose();
        // 缓存目录
        $this->cacheDir = TEMP_PATH . 'Content/';
    }
    /**
     * [index 首页视图]
     * @return [type] [description]
     */
    public function index()
    {
        $this->display($this->VIEW_DIR . '/index', C('CACHE_INDEX'), $this->cacheDir);
    }
}
