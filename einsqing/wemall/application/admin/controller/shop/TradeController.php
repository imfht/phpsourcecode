<?php
namespace app\admin\controller\shop;
use app\admin\controller\BaseController;
use PHPExcel_IOFactory;
use PHPExcel;

class TradeController extends BaseController
{
	//财务列表
	public function index(){
		$tradelist = model('Trade')->with('user')->order('id desc')->paginate();
		// halt($tradelist->toArray());
		cookie("prevUrl", request()->url());

		$this->assign('tradelist', $tradelist);
		return view();
	}

	//导出财务明细
	public function export(){
		$map = array();
		if(input('param.type') != ''){
            $map['type']  = input('param.type');
        }
        if(input('param.id') != ''){
            $map['id']  = ['in',input('param.id')];
        }
		$tradelist = model('Trade')->with('user')->where($map)->select()->toArray();
		$data = array(
			'0' => array(
                '1' => '编号',
                '2' => '用户',
                '3' => '交易号',
                '4' => '交易方式',
                '5' => '交易类型',
                '6' => '交易金额',
                '7' => '交易状态',
                '8' => '交易时间',
            ),
        );
		foreach ($tradelist as &$v) {
			switch ($v['type']) {
				case '0':
					$v['type'] = '消费';
					break;
				
				default:
					$v['type'] = '充值';
					break;
			}
			switch ($v['status']) {
				case '0':
					$v['status'] = '交易失败';
					break;
				
				default:
					$v['status'] = '交易成功';
					break;
			}
			array_push($data, array(
				'1' => $v['id'],
                '2' => $v['user']['username'],
                '3' => $v['tradeid'],
                '4' => $v['payment'],
                '5' => $v['type'],
                '6' => $v['money'],
                '7' => $v['status'],
                '8' => $v['created_at'],
			));
		}
		export_to($data,'财务明细');//导出财务明细
	}


}