<?php
namespace app\web_app\widget;

//use app\common\widget\WidgetBase;
use think\Controller;
use think\Log;

/**
 * Class Admin
 * @package app\entrance\widget
 */
class Admin extends Controller {
    public function load_resource() {
        return $this->fetch('admin/main/load_resource');
    }

    public function load_lang_and_config() {
        return $this->fetch('admin/main/load_lang_and_config');
    }

    public function top() {
        return $this->fetch('admin/main/top');
    }

    public function main_menu() {
        return $this->fetch('admin/main/main_menu');
    }

    public function tabs() {
        return $this->fetch('admin/main/tabs');
    }

    public function propergrid() {
        return $this->fetch('admin/main/propergrid');
    }

    public function hot_key() {
        return $this->fetch('admin/main/hot_key');
    }
}
