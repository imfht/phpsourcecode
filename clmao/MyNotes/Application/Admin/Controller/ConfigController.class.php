<?php

namespace Admin\Controller;

use Think\Controller;

class ConfigController extends CommonController {
    /*
     * 在线编辑
     */

    public function config() {
        $this->title = '在线编辑';
        if (empty($_REQUEST['filename'])) {
            die;
        }
        import("Admin.Class.NOOP_Translations");
        include(APP_PATH . 'Admin/Function/editor.php');
        $file = I('filename');
        validate_file_to_edit($file);
        $this->updateFile = $file;
        if ($file == 'config.php') {
            $file = APP_PATH . 'Common/Conf/' . $file;
        } else {
            $file = APP_PATH . 'Home/View/Index/' . $file;
        }
        if (isset($_POST['update'])) {
            $newcontent = wp_unslash($_POST['newcontent']);
            if (is_writeable($file)) {
                $f = fopen($file, 'w+');
                if ($f !== false) {
                    fwrite($f, $newcontent);
                    fclose($f);
                }
            }
        }
        $this->remark = I('remark');

        $f = fopen($file, 'r');
        $content = fread($f, filesize($file));
        if ('.php' == substr($file, strrpos($file, '.'))) {
            $functions = wp_doc_link_parse($content);

            $docs_select = '<select name="docs-list" id="docs-list">';
            $docs_select .= '<option value="">' . esc_attr__('Function Name&hellip;') . '</option>';
            foreach ($functions as $function) {
                $docs_select .= '<option value="' . esc_attr(urlencode($function)) . '">' . htmlspecialchars($function) . '()</option>';
            }
            $docs_select .= '</select>';
        }
        $content = esc_textarea($content);
        $this->content = $content;
        $this->display();
    }

}
