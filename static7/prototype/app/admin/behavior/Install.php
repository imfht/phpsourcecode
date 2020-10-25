<?php

namespace app\admin\behavior;

use think\Url;

/**
 * Description of Install
 * 检测系统是否安装
 * @author static7
 */
class Install {

    //引入jump类
    use \traits\controller\Jump;

    /**
     * 检测系统是否安装
     * @author static7 <static7@qq.com>
     */
    public function run() {
        if (!is_file(APP_PATH . 'database.php')) {
            return $this->redirect(Url::build('Install/index/index'));
        }
    }

}
