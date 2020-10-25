<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class FinanceController extends HomeController {
	protected $config = array(
		'app_type'	=> 'common',
		'admin'		=> 'account_list,add_account,save_account,edit_account,del_account',
		'write'		=> 'add_income,add_payment,add_transfer,save_transfer',
		'read'		=> 'read_account');

	//过滤查询字段
	function _search_filter(&$map) {
		$map['is_del'] = array('eq','0');
		$keyword = I('keyword');
		if (!empty($keyword)) {
			$map['name'] = array('like', "%" . $keyword . "%");
		}
	}

	public function index() {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);

		$account_list = M("FinanceAccount") -> where('is_del=0') -> getField("id,name");
		$this -> assign('account_list', $account_list);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$model = D("FinanceView");
		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	public function add_income(){
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$this -> assign("plugin", $plugin);

		$account_list = M("FinanceAccount") -> where('is_del=0') -> getField("id,name");
		$this -> assign('account_list', $account_list);

		$customer_list = M("Customer") -> where('is_del=0') -> getField("name id,name");
		$this -> assign('customer_list', $customer_list);
		
		$supplier_list = M("Supplier") -> where('is_del=0') -> getField("name id,name");
		$this -> assign('supplier_list', $supplier_list);
		
		$this -> display();
	}

	public function add_payment() {
		$plugin['date'] = true;
		$plugin['uploader'] = true;

		$account_list = M("FinanceAccount") -> where('is_del=0') -> getField("id,name");
		$this -> assign('account_list', $account_list);

		$customer_list = M("Customer") -> where('is_del=0') -> getField("name id,name");
		$this -> assign('customer_list', $customer_list);
		
		$supplier_list = M("Supplier") -> where('is_del=0') -> getField("name id,name");
		$this -> assign('supplier_list', $supplier_list);

		$this -> assign("plugin", $plugin);
		$this -> display();
	}

	public function add_transfer() {
		$plugin['date'] = true;
		$plugin['uploader'] = true;

		$account_list = M("FinanceAccount") -> where('is_del=0') -> getField("id,name");
		$this -> assign('account_list', $account_list);

		$customer_list = M("Customer") -> where('is_del=0') -> getField("name id,name");
		$this -> assign('customer_list', $customer_list);

		$this -> assign("plugin", $plugin);
		$this -> display();
	}

	public function save_transfer() {

		$account_id_payment = I('account_id_payment');
		$account_name_payment = I('account_name_payment');
				
		$account_id_income = I('account_id_income');
		$account_name_income = I('account_name_income');

		$account_list = M("FinanceAccount") -> getField('id,name');

		$account_name_payment = $account_list[$account_id_payment];
		$account_name_income = $account_list[$account_id_income];

		$money = I('money');

		$remark_income = "由[$account_name_payment]转入[$money]";
		$remark_payment = "向[$account_name_income]转出[$money]";
		
		$model = D("Finance");
		/*保存当前数据对象 */
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		$model -> account_id = $account_id_payment;
		$model -> account_name = $account_name_payment;
		$model -> payment = $money;
		$model -> remark = $remark_payment;
		$model->related_account_id=$account_id_income;
		$model->related_account_name=$account_name_income;

		$list = $model -> add();

		$model = D("Finance");
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}

		$model -> account_id = $account_id_income;
		$model -> account_name = $account_name_income;
		$model -> income = $money;
		$model -> remark = $remark_income;
		$model->related_account_id=$account_id_payment;
		$model->related_account_name=$account_name_payment;

		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			$this -> error('新增失败!');
			//失败提示
		}
	}

	public function edit($id) {
		$plugin['editor'] = true;
		$plugin['date'] = true;
		$plugin['uploader'] = true;
		$this -> assign("plugin", $plugin);

		if (IS_AJAX) {
			if ($vo !== false) {// 读取成功
				$this -> ajaxReturn($vo, "", 0);
			} else {
				die ;
			}
		}
		$this -> assign('vo', $vo);
		$this -> display();
	}

	function account_list() {
		$plugin['date'] = true;
		$this -> assign("plugin", $plugin);
		$this -> assign('auth', $this -> config['auth']);

		$map = $this -> _search();
		if (method_exists($this, '_search_filter')) {
			$this -> _search_filter($map);
		}

		$model = D("FinanceAccount");
		if (!empty($model)) {
			$this -> _list($model, $map);
		}
		$this -> display();
	}

	function add_account() {
		$this -> display();
	}

	function read_account($account_id) {
		$this -> _edit($account_id, "FinanceAccount");
	}

	function edit_account($account_id) {
		$this -> _edit($account_id, "FinanceAccount");
	}

	function save_account(){
		$this -> _save("FinanceAccount");
	}

	function del_account($account_id) {
		$this -> _del($account_id, "FinanceAccount");
	}

	function upload() {
		$this -> _upload();
	}

	function down($attach_id) {
		$this -> _down($attach_id);
	}
}
