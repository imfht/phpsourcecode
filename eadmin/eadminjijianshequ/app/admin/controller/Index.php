<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 首页控制器
 */
class Index extends AdminBase
{
    /**
     * 首页方法
     */
    public function adminindex()
    {

        $root = detect_site_url();

        $this->assign('root', $root);

        return $this->fetch('index_adminindex');
    }

    public function home()
    {
        // 获取首页数据
        $index_data = $this->adminBaseLogic->getIndexData();

        $this->assign('data', $index_data);

        return $this->fetch('index_home');
    }

    public function deal_sql()
    {

        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/update/updatedb.php';

        if (!file_exists($path)) {
            return json(['code' => 0, 'msg' => '升级文件不存在，请先把升级文件updatedb.php放置在/update/ 目录下']);

        }
    }
}
