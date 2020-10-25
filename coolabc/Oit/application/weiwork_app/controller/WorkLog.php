<?php
namespace app\weiwork_app\controller;

use app\common\api\Common;
use app\common\api\Dict;
use app\common\api\WeiWork;
use app\common\controller\WeiWorkBase;
use app\common\logic\EbsLogic;
use app\common\logic\WeiWorkLogic;
use app\common\model\app\AppEmp;
use think\Log;
use think\Config;
use think\Db;
use think\WeChat;

/**
 * Class WorkLog
 * 司机外勤
 * @package app\weiwork_app\controller
 */
class WorkLog extends WeiWorkBase {
    public $config_name = 'work_work_log';
    /**
     * 首页
     * @return string
     */
    public function index() {
        // 找到司机信息
        $emp_info_system = WeiWork::find_sys_app_emp_info();
        Dict::init_static_list();

        //$this->assign('data', $data);
        //$this->assign('js_data', json_encode($js_data));

        return $this->fetch();
    }

    /**
     * 订单列表
     * @return string
     */
    public function vr_list() {
        // 按钮组
        $js_data['Top_Buttons'] = [
            [
                'text' => lang('新增'),
                'id' => 'add',
                'icon' => 'add',
            ],
        ];
        $record_js_data['Top_Buttons'] = [
            [
                'text' => lang('新增'),
                'id' => 'add',
                'icon' => 'add',
            ],
            [
                'text' => lang('保存'),
                'id' => 'save',
                'icon' => 'save',
            ],
            [
                'text' => lang('删除'),
                'id' => 'remove',
                'icon' => 'remove',
            ],
        ];

        //$this->assign('data', $data);
        $this->assign('js_data', json_encode($js_data));
        $this->assign('record_js_data', json_encode($record_js_data));

        echo $this->fetch();
    }

    /**
     * 单据记录
     * @return string
     */
    public function vr_record() {
        $emp_info_system = WeiWork::find_sys_app_emp_info();
        $js_data['emp_info'] = $emp_info_system;
        $dict_emp = Dict::refresh('app_emp');
        $dict_car = Dict::refresh('car_card');
        $js_data['dict']['emp'] = $dict_emp;
        $js_data['dict']['car'] = $dict_car;

        $js_data['Form_Field'] = [
            [
                'id' => 'voucher_id',
                'title' => lang('单据流水'),
                'type' => 'hidden',
            ],
            [
                'id' => 'emp_id',
                'title' => lang('司机'),
                'type' => 'combobox',
                'combo_data' => $dict_emp,
                'value_id' => 'emp_id',
                'text_id' => 'name',
                'required' => true,
                'read_only' => true,
            ],
            [
                'id' => 'a_other_emp',
                'title' => lang('搬运**'),
                'type' => 'combobox',
                'combo_data' => $dict_emp,
                'value_id' => 'name',
                'text_id' => 'name',
                'required' => true,
            ],
            [
                'id' => 'voucher_no',
                'title' => lang('单据编号'),
                'tip' => lang('自动生成'),
                'type' => 'textbox',
                'read_only' => true,
            ],
            [
                'id' => 'voucher_date',
                'title' => lang('单据日期'),
                'tip' => lang('自动生成'),
                'type' => 'datebox',
                'read_only' => true,
            ],
            [
                'id' => 'a_li_beg',
                'title' => lang('开始里程**'),
                'type' => 'numberbox',
                'required' => true,
            ],
            [
                'id' => 'a_li_end',
                'title' => lang('结束里程**'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'a_li',
                'title' => lang('总里程'),
                'type' => 'numberbox',
                'read_only' => true,
            ],
            [
                'id' => 'a_car_no',
                'title' => lang('车牌号**'),
                'type' => 'combobox',
                'combo_data' => $dict_car,
                'value_id' => 'car_no',
                'text_id' => 'car_no',
                'required' => true,
            ],
            [
                'id' => 'b_gas_num',
                'title' => lang('总加油量'),
                'type' => 'hidden',
            ],
            [
                'id' => 'b_gas_amount',
                'title' => lang('总加油费'),
                'type' => 'hidden',
            ],
            [
                'id' => 'b_type_1',
                'title' => lang('总维修费'),
                'type' => 'hidden',
            ],
            [
                'id' => 'b_type_2',
                'title' => lang('总路桥费'),
                'type' => 'hidden',
            ],
            [
                'id' => 'b_type_3',
                'title' => lang('总过磅费'),
                'type' => 'hidden',
            ],
            [
                'id' => 'c_type_1',
                'title' => lang('总送货数'),
                'type' => 'hidden',
            ],
            [
                'id' => 'c_type_2',
                'title' => lang('总回料数'),
                'type' => 'hidden',
            ],
            [
                'id' => 'c_type_3',
                'title' => lang('总退货数'),
                'type' => 'hidden',
            ],
            [
                'id' => 'c_type_4',
                'title' => lang('总筒芯数'),
                'type' => 'hidden',
            ],
        ];

        $row_js_data['Top_Buttons'] = [
            [
                'text' => lang('新增'),
                'id' => 'add',
                'icon' => 'add',
            ],
            [
                'text' => lang('保存'),
                'id' => 'save',
                'icon' => 'save',
            ],
        ];
        $this->assign('js_data', json_encode($js_data));
        $this->assign('row_js_data', json_encode($row_js_data));
        echo $this->fetch();
    }

    /**
     * json 获取单据列表
     * @return \think\response\Json
     */
    public function vr_get_voucher_list_data() {
        // 查询本微信账号的员工
        $emp_info_system = WeiWork::find_sys_app_emp_info();
        $config = Config::get($this->config_name);
        $map['voucher_type'] = $config['work_vr_type'];
        $map['voucher_date'] = date('Ymd');
        $map['emp_id'] = $emp_info_system['emp_id'];
        $vr_list_data = EbsLogic::ebs_v_ebs_vr($map);
        foreach ($vr_list_data as $key => $val) {
            $vr_list_data[$key]['group'] = lang('当天');
        }
        // 当天有单据，自动打开当天的单据
        $auto_open_vr = false;
        if(count($vr_list_data)) {
            $auto_open_vr = true;
        }

        // 月初 至 昨天
        $mon_beg = date('Ym') . '01';
        $date_end = date('Ymd', strtotime("-1 day"));

        $map['voucher_date'] = ['between', [$mon_beg, $date_end]];
        $vr_list_data_2 = EbsLogic::ebs_v_ebs_vr($map);
        foreach ($vr_list_data_2 as $key => $val) {
            $vr_list_data_2[$key]['group'] = lang('当月');
        }

        $vr_list_data = array_merge($vr_list_data, $vr_list_data_2);
        if (empty($vr_list_data)) {
            $vr_list_data = [];
        }

        $js_data['vr_data'] = $vr_list_data;
        $js_data['auto_open_vr'] = $auto_open_vr;
        return json($js_data);
    }

    /**
     * json 获取单据的数据
     * @return \think\response\Json
     */
    public function vr_get_voucher_data() {
        $voucher_id = input('post.voucher_id');
        $where['a.voucher_id'] = $voucher_id;

        // ebs_v, ebs_vr,
        $js_data['vr'] = EbsLogic::ebs_v_ebs_vr($where);
        Common::date_sys_to_ui($js_data['vr'], ['voucher_date']);

        // ebs_v_attr
        EbsLogic::ebs_v_add_attr($js_data['vr']);

        // ebs_vr_item, ebs_vr_item_attr
        $where = [];
        $where['voucher_id'] = $voucher_id;
        $js_data['vr_item'] = EbsLogic::ebs_vr_item($where, false);

        // ebs_vr_item_attr
        EbsLogic::ebs_vr_item_add_attr($js_data['vr_item']);

        return json($js_data);
    }

    /**
     * 保存单据数据
     * @return \think\response\Json
     */
    public function vr_save_data() {
        $vr_data = input('post.');
        $ebs_v = $vr_data['ebs_v'];
        $ebs_v_attr = [];
        $ebs_vr = [];
        $ebs_vr_item = $vr_data['ebs_vr_item']['rows'];
        $ebs_vr_item_attr = [];

        $config = Config::get($this->config_name);

        // ebs_v 单据数据
        $voucher_date = date('Ymd', strtotime($ebs_v['voucher_date']));  // 默认的日期
        $voucher_type = $config['work_vr_type'];
        $ebs_v['voucher_type'] = $voucher_type;
        $ebs_v['create_user_id'] = $config['create_vr_default_user'];
        $ebs_v['state'] = 'A';
        $ebs_v['dept_id'] = AppEmp::get($ebs_v['emp_id'])->dept_id;
        $ebs_v['create_date'] = $voucher_date;
        $ebs_v['voucher_date'] = $voucher_date;
        $ebs_v['print_times'] = 0;

        // ebs_vr 基础数据
        $ebs_vr = EbsLogic::ebs_vr_ctl_data($voucher_type);

        $ebs_vr['amount'] = 0;  //金额
        $ebs_vr['bank_card_pay_amount'] = 0;  // 银行卡支付
        $ebs_vr['date_lmt_ebm'] = $voucher_date;  // 结算日期
        $ebs_vr['date_lmt_res'] = $voucher_date;  // 交付日期
        $ebs_vr['discount'] = 100;  // 折扣
        $ebs_vr['discount_amount'] = 0;  // 折扣
        $ebs_vr['draw_percent'] = 0;  // 提成比例
        $ebs_vr['draw_amount'] = 0;  // 提成金额
        $ebs_vr['eba_type'] = 'A';  // 是供应商还是客户,这个有可能是根据界面选择的是客户，还是供应商判断的
        $ebs_vr['gift_ticket_pay_amount'] = 0; // 优惠券
        $ebs_vr['io_amount'] = 0; // 收款金额
        $ebs_vr['mem_card_pay_amount'] = 0; // 会员卡余额
        $ebs_vr['money_factor'] = 1; // 汇率
        $ebs_vr['money_type'] = 'A'; // 币种 - 人民币
        $ebs_vr['pre_amount'] = 0; // 预收金额
        $ebs_vr['should_amount'] = 0; // 估计是应收金额-但是好像没啥用了
        $ebs_vr['vir_edt_oper_flag'] = 'N'; // 对虚拟库存影响标志,主物资或明细任一有影响就为Y

        // ebs_vr_item 单据明细数据
        $def_unit_type_id = $config['default_unit_type_id'];
        $res_id = $config['default_res_id'];
        foreach ($ebs_vr_item as $k => $v) {
            $ebs_vr_item[$k]['main_id'] = 0; // 序列
            $ebs_vr_item[$k]['item_id'] = $k + 1; // 序列
            $ebs_vr_item[$k]['res_id'] = $res_id;  // 产品id
            $ebs_vr_item[$k]['std_unit_type_id'] = $def_unit_type_id;  // 标准单位编码
            $ebs_vr_item[$k]['inp_unit_type_id'] = $def_unit_type_id;  // 录入计量单位
            $ebs_vr_item[$k]['ass_num'] = 0; // 辅助数量
            $ebs_vr_item[$k]['batch_no'] = '-'; // 批次
            $ebs_vr_item[$k]['cost_price'] = 0; // 参考成本价
            $ebs_vr_item[$k]['discount'] = 100; // 折扣
            $ebs_vr_item[$k]['discount_amount'] = 100; // 折扣
            $ebs_vr_item[$k]['discount_price'] = 0; // 折扣
            $ebs_vr_item[$k]['inp_amount'] = 0;  //金额
            $ebs_vr_item[$k]['inp_num_factor'] = 1.0;  //数量折算比率
            $ebs_vr_item[$k]['inp_price'] = 0;  //单价
            $ebs_vr_item[$k]['is_main'] = 'N';  //是否主物资
            $ebs_vr_item[$k]['item_edt_site_id'] = '-';  //库位编号
            $ebs_vr_item[$k]['item_fee_1'] = 0;  //费用一
            $ebs_vr_item[$k]['item_fee_2'] = 0;  //费用二
            $ebs_vr_item[$k]['item_target_edt_id'] = '';  //明细目标仓库
            $ebs_vr_item[$k]['produce_date'] = $voucher_date;  //生产日期
            $ebs_vr_item[$k]['ref_item_id'] = 0;  //引用流水
            $ebs_vr_item[$k]['ref_voucher_id'] = 0;  //引用单据流水
            $ebs_vr_item[$k]['ref_voucher_no'] = '';  //引用单据编号
            $ebs_vr_item[$k]['ref_voucher_type'] = '';  //引用单据类型编号
            $ebs_vr_item[$k]['res_cost_opt'] = 'B';  //成本影响标志
            $ebs_vr_item[$k]['std_num'] = 1;  //基准数量
            $ebs_vr_item[$k]['tag_amount'] = 0;  //目标金额
            $ebs_vr_item[$k]['tag_price'] = 0;  //目标单价
            $ebs_vr_item[$k]['tax_amount'] = 0;  //税额
            $ebs_vr_item[$k]['tax_price'] = 0;  //税后单价
            $ebs_vr_item[$k]['tax_rate'] = 0;  //税率%
            $ebs_vr_item[$k]['total_amount'] = 0;  //合计金额
            $ebs_vr_item[$k]['vr_item_ext_1'] = '';  //自定义1
            $ebs_vr_item[$k]['vr_item_ext_2'] = '';  //自定义2
            $ebs_vr_item[$k]['vr_item_ext_3'] = '';  //自定义3
            $ebs_vr_item[$k]['vr_item_ext_4'] = '';  //自定义4
            $ebs_vr_item[$k]['vr_item_ext_5'] = '';  //自定义5
            $ebs_vr_item[$k]['vr_item_ext_6'] = '';  //自定义6
        }

        // ebs_v_attr 单据属性扩展值
        $where = [];
        $where['obj_id'] = 'voucher.' . $ebs_v['voucher_type'];
        $ebs_v_attr_def = Db::table('app_attr_def')->where($where)->select();
        if (count($ebs_v_attr_def)) {
            foreach ($ebs_v_attr_def as $k => $v) {
                $ebs_v_attr[$k]['attr_id'] = $v['attr_id'];
                $ebs_v_attr[$k]['attr_val'] = $ebs_v[$v['attr_id']];
            }
        }

        // ebs_vr_item_attr 单据明细行扩展值
        $where['obj_id'] = 'voucher.item.' . $ebs_v['voucher_type'];
        $ebs_vr_item_attr_def = Db::table('app_attr_def')->where($where)->select();
        if (count($ebs_vr_item_attr_def)) {
            foreach ($ebs_vr_item as $ki => $vi) {
                foreach ($ebs_vr_item_attr_def as $k => $v) {
                    if (array_key_exists($v['attr_id'], $vi)) {
                        $ebs_vr_item_attr[] = [
                            'attr_id' => $v['attr_id'],
                            'attr_val' => $vi[$v['attr_id']],
                            'item_id' => $ki + 1,
                        ];
                    }
                }
            }
        }

        $data['ebs_v'] = $ebs_v;
        $data['ebs_v_attr'] = $ebs_v_attr;
        $data['ebs_vr'] = $ebs_vr;
        $data['ebs_vr_item'] = $ebs_vr_item;
        $data['ebs_vr_item_attr'] = $ebs_vr_item_attr;

        $result = EbsLogic::ebs_v_save($data);

        return json($result);
    }

    /**
     * json 单据新增时的默认数据
     * @return \think\response\Json
     */
    public function vr_get_default_add_data() {
        // 服务器日期
        // 默认中司机
        // 生成临时单据编号
        $emp_id = input('post.emp_id');
        $js_data['voucher_date'] = date('Y-m-d');
        $config = Config::get($this->config_name);
        $js_data['voucher_no'] = WeiWorkLogic::ebs_v_new_no($config['work_vr_type'], $emp_id, 'emp');

        return json($js_data);
    }

    /**
     * 单独的另一个编辑单条表格记录的界面
     */
    public function vr_row_edit() {
        $dict_res = WeiWorkLogic::res();

        $js_data['Form_Field'] = [
            [
                'id' => 'a_eba_id',
                'title' => lang('客户'),
                'tip' => lang('客户'),
                'type' => 'textbox',
                'required' => true,
            ],
            [
                'id' => 'a_time_beg',
                'title' => lang('时间进'),
                'tip' => lang('时间进'),
                'type' => 'timespinner',
            ],
            [
                'id' => 'a_time_end',
                'title' => lang('时间出'),
                'tip' => lang('时间出'),
                'type' => 'timespinner',
            ],
            [
                'id' => 'b_li_num',
                'title' => lang('里程'),
                'type' => 'numberbox',
                'required' => true,
            ],
            [
                'id' => 'c_type_1',
                'title' => lang('送货数量'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'c_type_2',
                'title' => lang('回料数量'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'c_type_3',
                'title' => lang('退货数量'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'c_type_4',
                'title' => lang('筒芯数量'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'b_gas_num',
                'title' => lang('加油量'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'b_gas_amount',
                'title' => lang('加油金额'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'b_type_1',
                'title' => lang('维修金额'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'b_type_2',
                'title' => lang('过桥过路费'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'b_type_3',
                'title' => lang('过磅费'),
                'type' => 'numberbox',
            ],
            [
                'id' => 'note_info',
                'title' => lang('备注'),
                'tip' => lang('备注'),
                'type' => 'textbox',
            ],
        ];

        $this->assign('js_data', json_encode($js_data));
        echo $this->fetch();
    }

    /**
     * 单据进度
     * @return string
     */
    public function vr_progress() {
        echo $this->fetch();
    }

    /**
     * 业务明细
     * @return string
     */
    public function eba_io() {
        echo $this->fetch();
    }

    /**
     * 收支明细
     * @return string
     */
    public function mon_account_io() {
        echo $this->fetch();
    }

    public function api() {
        $work = WeChat::agent('VrNet')->server;
        echo $work->reply();

        //return;
    }
}
