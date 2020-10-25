<?php
namespace app\emp_app\widget;

use app\common\widget\WidgetBase;
use think\Controller;
//use app\common\controller\OitBase;
use think\Log;

//class EmpGuiAdm extends WidgetBase {
class EmpGuiAdm extends Controller {
    public $priv_obj = 'emp_card';
    public $user_type_view = true;  // 是否有不同用户种类风格视图 true || false

    public function popup_menu() {
        return $this->fetch('emp_gui_adm/index/popup_menu');
    }

    public function def_function() {
        return $this->fetch('emp_gui_adm/index/def_function');
    }

    public function left_lmt() {
        return $this->fetch('emp_gui_adm/index/left_lmt');
    }

    public function search_para() {
        return $this->fetch('emp_gui_adm/index/search_para');
    }

    public function tree_para() {
        return $this->fetch('emp_gui_adm/index/tree_para');
    }

    public function search() {
        return $this->fetch('emp_gui_adm/index/search');
    }

    public function tree() {
        return $this->fetch('emp_gui_adm/index/tree');
    }

    public function datagrid() {
        return $this->fetch('emp_gui_adm/index/datagrid');
    }
}
