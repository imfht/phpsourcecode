<?php

class BackBaseController extends Controller {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    public function __construct() {
        /**
         * 定义静态变量
         */
        define('NOW_FORMAT_TIME', date('Y-m-d H:i:d', time()));
        define('NOW_TIME', time());

        /**
         * 获取站点配置
         */
        //主题
        $this->adminThem = Setting::find('admin_theme') ? Setting::find('admin_theme')->value : 'default';
        View::addNamespace('BackTheme', dirname(__DIR__) . '/views/backend/' . $this->adminThem . '/');
        //前端静态Public地址
        define('BACK_THEME_STATIC', '/views/backend/' . $this->adminThem);
    }

}
