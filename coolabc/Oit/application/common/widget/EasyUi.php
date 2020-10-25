<?php
namespace app\common\widget;

use think\Controller;
use think\Log;

/**
 * Class EasyUi
 * @package app\common\widget
 */
class EasyUi extends Controller {
    /**
     * @param        $title
     * @param string $icon
     * @return mixed
     */
    public function tree($title, $icon = 'icon-reload') {
        //$this->assign('title', $title);
        //$this->assign('icon', $icon);
        return $this->fetch('common@widget/pc/EasyUi/tree');
    }

    /**
     * @param        $title
     * @param string $icon
     * @return mixed
     */
    public function left_lmt($title, $icon = 'icon-reload') {
        $this->assign('title', $title);
        $this->assign('icon', $icon);
        return $this->fetch('common@widget/pc/EasyUi/left_lmt');
    }


    /**
     *  按钮功能
     **/
    public function buttons() {
        return $this->fetch('common@widget/pc/EasyUi/buttons');
    }

    /**
     *  快捷键功能
     **/
    public function index_hot_key() {
        return $this->fetch('common@widget/pc/EasyUi/index_hot_key');
    }
}
