<?php
namespace app\cli_app\controller;

// 对接某物流公司单据信息

use app\common\logic\EbaLogic;
use app\common\logic\EbsLogic;
use app\common\logic\SupLogic;
use app\common\model\app\AppEmp;
use app\common\model\eba\Eba;
use think\Cache;
use think\Config;
use think\Db;
use think\Log;
use think\Controller;
use GuzzleHttp\Client;

class GetIntData extends Controller {
    public $config_name = 'tong';

    public function save_data($data) {
        // 对接数据转换
        $config = Config::get($this->config_name);
        $v_num = count($data);
        $result = '';
        for ($i = 0; $i < $v_num; $i++) {
            $ebs_v = [];
            $ebs_v_attr = [];
            $ebs_vr = [];
            $ebs_vr_item = [];
            $ebs_vr_item_attr = [];

            // ebs_v 单据数据
            $voucher_date = date('Ymd', strtotime($data[$i]['carrierTime']));  // 默认的日期
            $voucher_type = $config['vr_type'];
            $ebs_v['voucher_no'] = $data[$i]['consignNumber'];
            $ebs_v['voucher_type'] = $voucher_type;
            $ebs_v['create_user_id'] = $config['create_user'];
            $ebs_v['state'] = 'A';
            $ebs_v['emp_id'] = $config['emp_id'];
            $ebs_v['dept_id'] = AppEmp::get($ebs_v['emp_id'])->dept_id;
            $ebs_v['create_date'] = date('Ymd');
            $ebs_v['voucher_date'] = $voucher_date;
            $ebs_v['print_times'] = 0;
            // 根据 voucher_no 找 voucher_id,找不到就为空
            $ebs_v['voucher_id'] = EbsLogic::get_voucher_id($ebs_v['voucher_no']);

            // 判断传递的客户在系统中有没有
            // 没有就新增
            $eba_id = EbaLogic::is_exist($data[$i]['recieveEntName'], 'Y');
            if (false == $eba_id) {
                // 默认新增值
                $eba_data['service_id'] = $config['temp_service_id'];
                $eba_data['state'] = $config['temp_eba_state'];
                $eba_data['eba_name'] = $data[$i]['recieveEntName'];
                $eba_data['emp_id'] = $config['emp_id'];
                $eba_data['dept_id'] = AppEmp::get($eba_data['emp_id'])->dept_id;
                $eba_id = EbaLogic::add($eba_data, [], 3);
            }

            // ebs_vr 基础数据,单据保存时并不需要创建影响的其他数据,审核时才需要
            $ebs_vr = EbsLogic::ebs_vr_ctl_data($voucher_type);

            $ebs_vr['eba_id'] = $eba_id;
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
            $res_id = $config['res_id'];
            foreach ($data[$i]['goodsListVo'] as $k => $v) {
                $ebs_vr_item[$k]['main_id'] = 0; // 序列
                $ebs_vr_item[$k]['item_id'] = $k + 1; // 序列
                $ebs_vr_item[$k]['res_id'] = $res_id;  // 产品id
                $ebs_vr_item[$k]['std_unit_type_id'] = $def_unit_type_id;  // 标准单位编码
                $ebs_vr_item[$k]['inp_unit_type_id'] = $def_unit_type_id;  // 录入计量单位
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
                $ebs_vr_item[$k]['inp_num'] = $v['weight']; // 数量 - 货物重量
                $ebs_vr_item[$k]['ass_num'] = $v['num']; // 辅助数量 - 货物件数
                $ebs_vr_item[$k]['vr_item_ext_1'] = $v['name'];  //自定义1 - 产品名称
                $ebs_vr_item[$k]['vr_item_ext_2'] = $v['spec'];  //自定义2 - 规格
                $ebs_vr_item[$k]['vr_item_ext_3'] = $v['manufactor'];  //自定义3 - 厂家
                $ebs_vr_item[$k]['vr_item_ext_4'] = '';  //自定义4
                $ebs_vr_item[$k]['vr_item_ext_5'] = '';  //自定义5
                $ebs_vr_item[$k]['vr_item_ext_6'] = '';  //自定义6
                $ebs_vr_item[$k]['checked_item_note'] = $v['material'];  // ebs_vr_item_attr中的附加内容, 审核后更改内容 - 材质
            }

            // ebs_v_attr 单据属性扩展值
            $where = [];
            $where['obj_id'] = 'voucher.' . $ebs_v['voucher_type'];
            $ebs_v_attr_def = Db::table('app_attr_def')->where($where)->select();
            $ebs_v_attr[] = [  // 联系人 - 项目地址
                'attr_id' => 'linkman',
                'attr_val' => $data[$i]['recieveEntPersonName'],
            ];
            $ebs_v_attr[] = [  // 卸货地址
                'attr_id' => 'aim_address',
                'attr_val' => $data[$i]['destinationAddress'],
            ];
            if (count($ebs_v_attr_def)) {
                foreach ($ebs_v_attr_def as $k => $v) {
                    //$ebs_v_attr[$k]['attr_id'] = $v['attr_id'];
                    //$ebs_v_attr[$k]['attr_val'] = $ebs_v[$v['attr_id']];
                    switch ($v['attr_id']) {
                        case 'a_a':  // 装卸方式
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_a',
                                'attr_val' => '-'
                            ];
                            break;
                        case 'a_b':  // 代垫费用
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_b',
                                'attr_val' => '-'
                            ];
                            break;
                        case 'a_c':  // 平均单价
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_c',
                                'attr_val' => ' '
                            ];
                            break;
                        case 'a_d':  // 车号
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_d',
                                'attr_val' => $data[$i]['plateNumber']
                            ];
                            break;
                        case 'a_f':  // 承运人 - 司机 内外部 供应商
                            $sup_id = SupLogic::is_exist($data[$i]['carrierPersonName'], 'Y');
                            // 如果没有供应商的时候，软件自动添加一个
                            if ($sup_id == false) {
                                // 默认新增值
                                $sup_data['service_id'] = $config['sup_service_id'];
                                $sup_data['state'] = $config['sup_state'];
                                $sup_data['sup_name'] = $data[$i]['carrierPersonName'];
                                $sup_data['emp_id'] = $config['emp_id'];
                                $sup_data['dept_id'] = AppEmp::get($sup_data['emp_id'])->dept_id;
                                $sup_id = SupLogic::add($sup_data);
                            }
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_f',
                                'attr_val' => $sup_id
                            ];
                            break;
                        case 'a_g':  // 合同号
                            $ebs_v_attr[] = [
                                'attr_id' => 'a_g',
                                'attr_val' => ' '
                            ];
                            break;
                    }
                }
            }

            // ebs_vr_item_attr 单据明细行扩展值
            $where['obj_id'] = 'voucher.item.' . $ebs_v['voucher_type'];
            $ebs_vr_item_attr_def = Db::table('app_attr_def')->where($where)->select();
            foreach ($ebs_vr_item as $ki => $vi) {
                $sys_attr = ['checked_item_note'];
                foreach ($sys_attr as $k => $v) {
                    switch ($v) {
                        case 'checked_item_note':  // 审核之后备注
                            $ebs_vr_item_attr[] = [
                                'attr_id' => $v,
                                'attr_val' => $vi[$v],
                                'item_id' => $ki + 1,
                            ];
                            break;
                    }
                }

                if (count($ebs_vr_item_attr_def)) {
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


            $save_data['ebs_v'] = $ebs_v;
            $save_data['ebs_v_attr'] = $ebs_v_attr;
            $save_data['ebs_vr'] = $ebs_vr;
            $save_data['ebs_vr_item'] = $ebs_vr_item;
            $save_data['ebs_vr_item_attr'] = $ebs_vr_item_attr;

            // 检查单据是否已有单据，以及单据状态
            // 已经审核的单据
            // 未审核状态下,就先删除单据,再保存

            // 保存之前检查客户,客户的联系人(项目),等其他编码是否已存在
            // 不存在就插入

            // 返回失败或成功
            //Log::write($save_data, 'record');
            $result[] = EbsLogic::ebs_v_save($save_data);
        }
        Log::record($result, 'record');
        return $result;
    }

    public function get_data($beg_date, $end_date) {
        $timestamp = get_milli_second();
        $secretKey = 'JtWjG0N8YsiWmDtDvkTGNi0mdtNWiZJNYutZ';
        $sign_arr = [
            'accessKey' => 'qZjkUKQS7Sqszzpx2j',
            'startDate' => $beg_date,
            'endDate' => $end_date,
        ];
        ksort($sign_arr);
        $temp_str = '';
        foreach ($sign_arr as $key => $val) {
            $temp_str .= $key . '=' . $val . '&';
        }
        $temp_str .= 'timestamp=' . $timestamp . '&';
        $sign_str = $temp_str;
        $temp_str .= 'secretKey=' . $secretKey;
        $sign = sha1($temp_str);

        //$url = "http://api.test2.logibeat.com/openapi/common/Bs/api/openApi/getConsign.htm?" . $sign_str;
        $url = "http://api.logibeat.com/openapi/common/Bs/api/openApi/getConsign.htm?" . $sign_str;
        $url .= "sign=" . $sign;
        Log::write($url, 'notice');

        $client = new Client();
        $res = $client->request('POST', $url);
        $body = $res->getBody();
        $content = $body->getContents();
        $rev = json_decode($content, true);
        if ($rev['suc'] == true) {
            $data = json_decode($rev['data'], true);
        } else {
            $data = $rev;
        }
        $i_count = count($data);
        echo '共计: ' . $i_count . ' 条单据信息' . '</br>';

        // 检查,保存 单据是否已经写入
        return $data;
    }

    public function index() {
        set_time_limit(90);
        $beg_date = input('beg_date');
        $end_date = input('end_date');
        if (empty($beg_date) || empty($end_date)) {
            echo lang('请指定查询的开始与结束日期');
            return 0;
        }

        // 获取数据
        $int = date_day_between($beg_date, $end_date);
        echo $int . ' nums' . '<br/>';
        $need_num = ceil($int / 2) + 1;
        $all_data = [];
        for ($i = 1; $i <= $need_num; $i++) {
            $temp_date = date("Y-m-d", strtotime('+1 day', strtotime($beg_date)));
            if (strtotime($temp_date) > strtotime($end_date)) {
                $temp_date = date("Y-m-d", strtotime($end_date));
            }
            if (strtotime($beg_date) > strtotime($end_date)) {
                $beg_date = date("Y-m-d", strtotime($end_date));
            }
            // 获取本次数据
            $str_echo = '第 ' . $i . ' 次查询: 日期: ' . $beg_date . ' : ' . $temp_date . '<br/>';
            echo $str_echo;
            $data = $this->get_data($beg_date, $temp_date);
            //Log::write($str_echo, 'notice');
            //Log::write($data, 'notice');
            $all_data = array_merge($all_data, $data);
            $beg_date = date("Y-m-d", strtotime('+1 day', strtotime($temp_date)));
        }

        //print_r($all_data);
        Log::write($all_data, 'notice');

        // 保存数据
        $this->save_data($all_data);

        return $this->fetch();
    }

}
