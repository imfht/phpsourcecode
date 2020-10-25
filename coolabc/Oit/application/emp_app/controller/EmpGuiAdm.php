<?php
// 员工资料
namespace app\emp_app\controller;

use app\common\api\Button;
use app\common\api\Dict;
use app\common\api\Grid;
use app\common\api\Script;
use app\common\api\Tree;
use app\common\controller\OitBase;
use app\common\logic\EmpLogic;
use app\common\model\emp\Emp;
use app\common\model\emp\EmpNote;

/**
 * Class EmpGuiAdm
 * @package app\emp_app\controller
 */
class EmpGuiAdm extends OitBase {
    public $priv_obj = 'emp_card';
    public $user_type_view = true;  // 是否有不同用户种类风格视图 true || false
    private $mg_model = Emp::class;  // 员工
    private $mg_detail_model = EmpNote::class; // 员工备注

    /**
     * 查看
     * 一般只做界面控件数据固定的初始化
     */
    public function index() {
        $grid_model = new $this->mg_model();
        // 原生数据
        $data['Tree_View'] = [
            'title' => lang('部门结构'),
        ];
        $data['Lmt_Switch'] = [
            'title' => lang('检索条件'),
        ];
        //$data['Top_Buttons'] = json_encode(Button::get_toolbar_access($this->priv_obj));
        $data['Script'] = [
            'adm' => Script::get_code_to_js('adm_grid_emp_record'),
        ];
        $data['Win_Record'] = [
            'title' => lang("员工资料卡片"),
        ];

        // js对象数据
        $js_data['Table_Info'] = [
            'table' => $grid_model->table,
            'pk' => $grid_model->pk,
            'pk_name' => $grid_model->pk_name,
        ];
        $js_data['MG']['column'] = Grid::get_grid_fmt_def($this->mg_model);
        $js_data['MG_Detail']['column'] = Grid::get_grid_fmt_def($this->mg_detail_model);
        $js_data['Top_Buttons'] = Button::get_toolbar_access($this->priv_obj);
        // 顶部功能下拉菜单
        $js_data['Popup_Menu'] = [[
            'id' => 'change_emp_id',
            'title' => lang('改变员工工号'),
            'url' => url('www.baidu.com'),
            'icon' => 'icon-edit',
        ],];
        if ('Y' == session('is_admin')) {
            $js_data['Popup_Menu'] = array_merge($js_data['Popup_Menu'], [[
                'id' => 'adm_grid_ext',
                'title' => lang('明细扩展列'),
                'url' => url('www.baidu.com'),
            ], [
                'id' => 'menu-sep',
            ], [
                'id' => 'record_script',
                'title' => lang('卡片脚本'),
                'url' => url('@rep_app/rep_gui_ds_adm/index'),
            ], [
                'id' => 'adm_script',
                'title' => lang('记录脚本'),
                'url' => url('@rep_app/rep_gui_ds_adm/index'),
            ], [
                'id' => 'print_set',
                'title' => lang('打印设置'),
                'url' => url('www.baidu.com'),
            ],]);
        }

        $js_data['Script'] = [
            'adm' => [
                'obj_id' => 'adm_grid_emp_record',
                'init_mode' => 'adm',
                //'code' => Script::get_code_to_js('adm_grid_emp_record'),  // 会导致json.parse解析失败
            ],
            'record' => [
                'obj_id' => 'rec_emp',
                'init_mode' => 'record',
                //'code' => Script::get_code_to_js('rec_emp'),
            ],
        ];

        $js_data['Left_Lmt'] = [
            [
                'tab_title' => lang('常用'),
                'data' => [
                    [
                        'id' => 'state',
                        'title' => lang('员工状态'),
                        'type' => 'combobox',
                        'combo_data' => Dict::get_dict('emp_state'),
                        'default_checked' => 1,
                        'default_val' => 'A,B',
                        'value_id' => 'code',
                        'text_id' => 'name',
                        'filter_col' => 'state',  // 数据过滤的列
                        'filter_type' => '=',
                    ],
                ],
            ],
            [
                'tab_title' => lang('时间'),
                'data' => [
                    [
                        'id' => 'hire_date_beg',
                        'title' => lang('入职开始'),
                        'type' => 'datebox',
                        'filter_col' => 'hire_date',
                        'filter_type' => '<=',
                    ],
                    [
                        'id' => 'hire_date_end',
                        'title' => lang('入职结束'),
                        'type' => 'datebox',
                        'filter_col' => 'hire_date',
                        'filter_type' => '>=',
                    ],
                ],

            ],
        ];

        $emp_company = EmpLogic::get_emp_company();
        if (count($emp_company) > 1) {
            $emp_company = array_merge([['company_id' => 'all', 'company_name' => lang('所有公司')]], $emp_company);
        }
        $js_data['Para'] = [
            'Combobox_Company' => $emp_company,
        ];

        $company_dept = EmpLogic::get_emp_dept();
        $js_data['Tree_View'] = $company_dept;

        // 视图需要构建的组件有
        $js_data['Gui_Ele'] = ['MG', 'Popup_Menu', 'TopButtons', 'Script', 'Def_Function'];

        // 表格数据
        $js_data['Table_data'] =EmpLogic::get_emp_data();

        $this->assign('data', $data);
        $this->assign('js_data', json_encode($js_data));
        echo $this->fetch();
    }



}
