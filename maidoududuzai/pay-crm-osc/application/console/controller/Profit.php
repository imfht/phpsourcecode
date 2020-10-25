<?php

namespace app\console\controller;

use \think\Db;

class Profit
{

	public $admin;

	public function __construct()
	{
		$this->admin = model('Admin')->checkLoginAdmin();
	}

	public function index()
	{
		$month = input('param.month');
		$trade_gate = input('param.trade_gate');
		$agent_id = input('param.agent_id');
		$agent_name = input('param.agent_name');
		if(empty($month)) {
			$month = gsdate('Y-m');
		}
		list($y, $m) = explode('-', $month, 2);
		$where = [];
		$where['tp.y'] = ['=', $y];
		$where['tp.m'] = ['=', $m];
		$where['t.sub_gate'] = ['=', ''];
		if(!empty($trade_gate)) {
			$where['t.trade_gate'] = ['=', $trade_gate];
		}
		if(!empty($agent_id)) {
			$where['a.agent_id'] = ['=', $agent_id];
		} else {
			if(!empty($agent_name)) {
				$where['a.agent_no|a.agent_name'] = ['LIKE', '%'.$agent_name.'%'];
			}
		}
		$object = Db::name('trade_profit')
			->alias('tp')
			->join('trade t', 't.out_trade_no = tp.out_trade_no', 'LEFT')
			->join('agent a', 'a.agent_id = tp.agent_id', 'LEFT')
			->where($where)
			->field('a.agent_id, a.agent_no, a.agent_name, count(tp.id) as trade_count, sum(tp.commission) as trade_commission')
			->group('a.agent_id')
			->order('tp.agent_id', 'ASC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		include \befen\view();
	}

	public function detail()
	{
		$agent_id = input('param.agent_id');
		$action = input('param.action');
		$month = input('param.month');
		$trade_gate = input('param.trade_gate');
		$merchant_id = input('param.merchant_id');
		$merchant_name = input('param.merchant_name');
		if(empty($month)) {
			$month = gsdate('Y-m');
		}
		list($y, $m) = explode('-', $month, 2);
		$agent_name = Db::name('agent')->where('agent_id', '=', $agent_id)->value('agent_name');
		$where['t.sub_gate'] = ['=', ''];
		if(!empty($trade_gate)) {
			$where['t.trade_gate'] = ['=', $trade_gate];
		}
		$where['t.agent_id'] = ['=', $agent_id];
		if(!empty($merchant_id)) {
			$where['m.merchant_id'] = ['=', $merchant_id];
		} else {
			if(!empty($merchant_name)) {
				$where['m.merchant_name'] = ['LIKE', '%'.$merchant_name.'%'];
			}
		}
		$where['t.trade_status'] = ['=', 'SUCCESS'];
		$beg_time = gstime("{$y}-{$m}");
		$end_time = strtotime('+1 month', gstime("{$y}-{$m}"));
		$where['t.time_create'] = ['BETWEEN TIME', [$beg_time, $end_time]];
		$object = Db::name('trade')
			->alias('t')
			->join('agent a', 'a.agent_id = t.agent_id', 'LEFT')
			->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
			->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
			->join('trade_profit tp', 't.out_trade_no = tp.out_trade_no', 'RIGHT')
			->where($where)
			->field('t.*, a.agent_no, a.agent_name, m.merchant_no, m.merchant_name, tp.commission')
			->order('trade_id', 'ASC')
			->paginate(20, false, ['query' => request()->param()]);
		$array = $object->toArray();
		$total = $array['total'];
		$list = $array['data'];
		$per_page = $array['per_page'];
		$current_page = $array['current_page'];
		$last_page = $array['last_page'];
		$pagenav = $object->render();
		if($action == 'export') {
			$list = Db::name('trade')
				->alias('t')
				->join('agent a', 'a.agent_id = t.agent_id', 'LEFT')
				->join('agent_level al', 'a.level_id = al.level_id', 'LEFT')
				->join('merchant m', 'm.merchant_id = t.merchant_id', 'LEFT')
				->join('trade_profit tp', 't.out_trade_no = tp.out_trade_no', 'RIGHT')
				->where($where)
				->field('t.*, a.agent_no, a.agent_name, m.merchant_no, m.merchant_name, tp.commission')
				->order('trade_id', 'ASC')
				->select();
			$data = [];
			$i = 0;
			$i++;
			$data[$i] = ['序号', '代理名称', '商户名称', '支付通道', '交易金额', '结算扣率', '商户扣率', '交易分润', '交易时间', '商户交易号'];
			foreach($list as $val) {
				$i++;
				$data[$i] = [$i - 1, $val['agent_name'], $val['merchant_name'], model('Trade')->getGate($val['trade_gate']), $val['total_amount'], $val['agent_rates'] . '%', $val['trade_rates'] . '%', $val['commission'] ? $val['commission'] : '0.00', gsdate('Y-m-d H:i:s', $val['time_create']), $val['out_trade_no']];
			}
			$_Key = [];
			foreach(range('A', 'Z') as $val) {
				$_Key[] = $val;
			}
			require_once EXTEND_PATH . 'PHPExcel/PHPExcel.php';
			require_once EXTEND_PATH . 'PHPExcel/PHPExcel/IOFactory.php';
			$Excel = new \PHPExcel();
			foreach($data as $key => $val) {
				if($key == 0) {
					continue;
				}
				$i = 0;
				foreach($val as $value) {
					$Excel->setActiveSheetIndex(0)->setCellValueExplicit($_Key[$i] . $key, (string)$value, 's');
					if($key == 1) {
						$Excel->getActiveSheet()->getStyle($_Key[$i] . $key)->getFont()->setBold(true);
					}
					$i++;
				}
			}
			$Excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
			$Excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
			$Excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
			$Excel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
			$Excel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
			$Excel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
			$Excel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
			$Excel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
			$Excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
			$Excel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
			header("Cache-Control: no-cache, must-revalidate");
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename={$agent_name}-{$month}.xls");
			$Writer = \PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
			$Writer->save('php://output');
			exit();
		}
		include \befen\view();
	}

}

