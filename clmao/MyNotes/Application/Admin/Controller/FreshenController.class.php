<?php

namespace Admin\Controller;

use Think\Controller;

class FreshenController extends CommonController {

  

    /* 执行更新 */

    public function freshen() {
        set_time_limit(0);
        include MODULE_PATH. '/Helper/file.php';
        delete_files(TEMP_PATH);
        delete_files(HTML_PATH);
        file_put_contents(HTML_PATH.'/.htaccess', 'deny from all');
        $this->success('缓存已经成功清理了！',U('Admin/main'));
        
    }

}