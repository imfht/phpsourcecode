<?php
// 脚本工具
namespace app\rep_app\controller;

use app\common\api\Para;
use app\common\controller\OitBase;
use app\common\model\app\AppObjSource;
use app\common\api\Script;

/**
 * Class PduGuiCspProg
 * 脚本代码窗口
 * @package app\emp_app\controller
 */
class PduGuiCspProg extends OitBase {
    //public $priv_obj = 'emp_card';
    //public $user_type_view = true;  // 是否有不同用户种类风格视图 true || false
    private $code_model = AppObjSource::class;  // 代码模型对象

    // 脚本初始化的样本
    public $script_init_code = [
        'record' =>
            "(function(){
  return {
    // that 表示页面对象

    func_f9_call: function(){
      // 按f9时运行
    },
    func_show_win: function(){
      // 第一次弹出窗口时执行
    },
    func_create_rec: function(){
      // 新建记录时执行
    },
    func_edit_rec: function(){
      // 编辑记录时运行
    },
    func_delete_rec: function(){
      // 删除记录时运行
	  var can_delete = 1;
    },
    func_save_rec: function(){
      // 保存记录时运行
	  var can_save=1;
    },
    obj_change: function(){
      // 控件改变时运行
	  // change_obj
    },
    func_duplicate_rec: function(){
      // 复制新增时运行
    },
  };
}());
",
        'adm' =>
            "(function(){
  return {
    // that 表示页面对象

    init_win: function(){
      // 界面完成时运行
    },
    load_ext: function(){
      // 显示记录前运行
    },
    show_row_ext(){
      // 显示每条记录的时候运行
    },
    row_change_ext: function(){
      // 每条记录有改变时运行
    },
    func_before_delete_all: function(){
      // 删除所有记录之前运行
      var can_oper = 1;
    },
    show_detail_row_ext: function(){
      // 显示每条明细的时候运行
    },
    main: function(){
      // 测试或初始化
    },
  };
}());
",
    ];

    /**
     * 查看
     * 一般只做界面控件数据固定的初始化
     */
    public function index() {
        $obj_id = input('obj_id');
        $init_mode= input('init_mode');

        $code = Script::get_code_to_textarea($obj_id);
        $this->assign('code', $code);
        $this->assign('obj_id', $obj_id);
        $this->assign('init_mode', $init_mode);
        // 习惯性参数
        $theme = Para::system_para_get('script_theme');
        $this->assign('theme', $theme);
        $editor_type = Para::system_para_get('editor_type');
        $this->assign('editor_type', $editor_type);

        echo $this->fetch();
    }

    /**
     * 保存代码
     */
    public function save() {
        $obj_id = input('obj_id');
        //$code = preg_split('/\r\n/', input('code'));
        $code = preg_split('/\n/', input('code'));
        if(Script::save($obj_id, $code) == 1){
            return json(lang('保存成功'));
        } else {
            return json(lang('保存失败'));
        }
    }

    /**
     * 初始化脚本编辑器里的内容
     * @return \think\response\Json
     */
    public function init() {
        // 初始化分为几种类型、
        // 1 卡片记录 record，2 单据脚本 voucher，3 管理列表 adm，4 打印脚本 print
        $init_mode = input('init_mode');
        return json($this->script_init_code[$init_mode]);
    }

}
