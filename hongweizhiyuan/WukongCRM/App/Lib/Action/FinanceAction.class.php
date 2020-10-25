<?php 
class FinanceAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('changecontent', 'listdialog', 'revert', 'adddialog', 'analytics', 'checkout','getmonthlyreceive','getyearreceivecomparison','getreceivablesmoney',
			'getpayablesmoney')
		);
		B('Authenticate', $action);
		
		$this->type = trim($_GET['t'])?trim($_GET['t']):'receivables';
		if(!in_array($this->type,array('receivables','payables','receivingorder','paymentorder'))){
			alert('error',L('PARAMETER_ERROR'),U('index/index'));
		}
	}

	public function changecontent(){
		$where = array();
		$params = array();
		$order = "";
		$p = !$_REQUEST['p']||$_REQUEST['p']<=0 ? 1 : intval($_REQUEST['p']);
		$below_ids = getSubRoleId();
		$where[$this->type . '.is_deleted'] = 0;
		$where[$this->type . '.owner_role_id'] = array('in',implode(',', $below_ids)); 
		$where['receivables.status'] = array('neq',2);
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? $this->type . '.name|'.$this->type .'.description' : $this->type .'.'. $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field ) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($_REQUEST['condition']) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
		}
		$order = empty($order) ? $this->type . '.update_time desc' : $order;
		
		switch ($this->type) {
			case 'receivables' :
				$receivables = D('ReceivablesView');
				$list = $receivables->order($order)->where($where)->page($p.',10')->select();
				 
				foreach($list as $k=>$v){
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					$list[$k]['pay_time'] = date("Y-m-d",$v['pay_time']);
				}
				$count = $receivables->where($where)->count();
				$data['list'] = $list;
				$data['p'] = $p;
				$data['count'] = $count;
				$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
				$this->ajaxReturn($data,"",1);
				break;
			case 'payables' :
				$payables = D('PayablesView');
				$list = $payables->order($order)->where($where)->page($p.',10')->select();
				
				foreach($list as $k=>$v){
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					$list[$k]['pay_time'] = date("Y-m-d",$v['pay_time']);
				}
				$count = $payables->where($where)->count();
				$data['list'] = $list;
				$data['p'] = $p;
				$data['count'] = $count;
				$data['total'] = $count%10 > 0 ? ceil($count/10) : $count/10;
				$this->ajaxReturn($data,"",1);
				break;
		}
	}
	
	public function index(){
		$where = array();
		$params = array();
		$order = "";
		
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		$p = isset($_GET['p']) ? intval($_GET['p']) : 1 ;
		$by = isset($_GET['by']) ? trim($_GET['by']) : '';
		$below_ids = getSubRoleId(false);
		$all_ids = getSubRoleId();
		switch ($by) {
			case 'create' : $where[$this->type . '.creator_role_id'] = session('role_id'); break;
			case 'sub' : $where[$this->type . '.owner_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'subcreate' : $where[$this->type . '.creator_role_id'] = array('in',implode(',', $below_ids)); break;
			case 'none' : $where[$this->type . '.status'] = array('eq',0); break;
			case 'part' : $where[$this->type . '.status'] = array('eq',1); break;
			case 'all' : $where[$this->type . '.status'] = array('eq',2); break;
			case 'today' : 
				$where[$this->type . '.pay_time'] =  array(array('lt',strtotime(date('Y-m-d', time()))+86400), array('gt',0), 'and'); 
				$where[$this->type . '.status'] = array('neq',2);
				break;
			case 'week' : 
				$where[$this->type . '.pay_time'] =  array(array('lt',strtotime(date('Y-m-d', time())) + (date('N', time()) - 1) * 86400), array('gt', 0),'and'); 
				$where[$this->type . '.status'] = array('neq',2);
				break;
			case 'month' : 
				$where[$this->type . '.pay_time'] =  array(array('lt',strtotime(date('Y-m-01', strtotime('+1 month')))), array('gt', 0),'and'); 
				$where[$this->type . '.status'] = array('neq',2);
				break;
			case 'deleted' : $where[$this->type . '.is_deleted'] = 1; break;
			case 'add' : $order = $this->type . '.create_time desc'; break;
			case 'update' : $order = $this->type . '.update_time desc'; break;
			case 'me' : $where[$this->type . '.owner_role_id'] = session('role_id'); break;
			
		}
		if (!isset($where[$this->type . '.is_deleted'])) {
			$where[$this->type . '.is_deleted'] = 0;
		}
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? $this->type . '.name|'.$this->type .'.description' : $this->type .'.'. $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('receivables.create_time' == $field || 'receivables.update_time' == $field ) {
				$search = is_numeric($search)?$search:strtotime($search);
			}
			switch ($_REQUEST['condition']) {
				case "is" : $where[$field] = array('eq',$search);break;
				case "isnot" :  $where[$field] = array('neq',$search);break;
				case "contains" :  $where[$field] = array('like','%'.$search.'%');break;
				case "not_contain" :  $where[$field] = array('notlike','%'.$search.'%');break;
				case "start_with" :  $where[$field] = array('like',$search.'%');break;
				case "end_with" :  $where[$field] = array('like','%'.$search);break;
				case "is_empty" :  $where[$field] = array('eq','');break;
				case "is_not_empty" :  $where[$field] = array('neq','');break;
				case "gt" :  $where[$field] = array('gt',$search);break;
				case "egt" :  $where[$field] = array('egt',$search);break;
				case "lt" :  $where[$field] = array('lt',$search);break;
				case "elt" :  $where[$field] = array('elt',$search);break;
				case "eq" : $where[$field] = array('eq',$search);break;
				case "neq" : $where[$field] = array('neq',$search);break;
				case "between" : $where[$field] = array('between',array($search-1,$search+86400));break;
				case "nbetween" : $where[$field] = array('not between',array($search,$search+86399));break;
				case "tgt" :  $where[$field] = array('gt',$search+86400);break;
				default : $where[$field] = array('eq',$search);
			}
			$params = array('field='.trim($_REQUEST['field']), 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
		}
		$order = empty($order) ? $this->type . '.create_time desc' : $order;
		
		switch ($this->type) {
			case 'receivables' :
				$receivables = D('ReceivablesView');
				$list = $receivables->order($order)->where($where)->page($p.',10')->select();
				$sum_money = $receivables->where($where)->sum('receivables.price');
				foreach($list as $k=>$v){
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					$money += $v['price'];
					if($by == 'deleted'){
						$list[$k]['deleted'] = getUserByRoleId($v['delete_role_id']);
					}
					$num = D('ReceivingorderView')->where('receivingorder.is_deleted <> 1 and receivingorder.receivables_id = %d and receivingorder.status = 1', $v['receivables_id'])->sum('money');
					$list[$k]['un_payable'] = $v['price'] - $num;
				}
				$money = number_format($money,2);
				$sum_money = number_format($sum_money,2);
				$count = $receivables->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,10);
				$params[] = 'by=' . trim($_GET['by']);
				$params[] = 't=' . $this->type;
				$this->parameter = implode('&', $params);
				if ($_GET['desc_order']) {
					$params[] = "desc_order=" . trim($_GET['desc_order']);
				} elseif($_GET['asc_order']){
					$params[] = "asc_order=" . trim($_GET['asc_order']);
				}
				
				$Page->parameter = implode('&', $params);
				$show = $Page->show();
				
				$this->alert = parseAlert();
				$this->assign('page',$show);
				$this->assign('money',$money);
				$this->assign('sum_money',$sum_money);
				$this->assign('list',$list);
				$this->display('receivables');
				break;
			case 'payables' :
				$payables = D('PayablesView');
				$list = $payables->order($order)->where($where)->page($p.',10')->select();
				$sum_money = $payables->where($where)->sum('payables.price');
				foreach($list as $k=>$v){
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					$money += $v['price'];
					$list[$k]['purchase_sn_code'] = M('purchase')->where('purchase_id = %d', $v['purchase_id'])->getField('sn_code');
					if($by == 'deleted'){
						$list[$k]['deleted'] = getUserByRoleId($v['delete_role_id']);
					}
				}
				$money = number_format($money,2);
				$sum_money = number_format($sum_money,2);
				$count = $payables->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,10);
				$params[] = 'by=' . trim($_GET['by']);
				$params[] = 't=' . $this->type; 
				
				$this->parameter = implode('&', $params);
				if ($_GET['desc_order']) {
					$params[] = "desc_order=" . trim($_GET['desc_order']);
				} elseif($_GET['asc_order']){
					$params[] = "asc_order=" . trim($_GET['asc_order']);
				}
				$Page->parameter = implode('&', $params);
				$show = $Page->show();
				
				$this->alert = parseAlert();
				$this->assign('page',$show);
				$this->assign('money',$money);
				$this->assign('sum_money',$sum_money);
				$this->assign('list',$list);
				$this->display('payables');
				break;
			case 'receivingorder' :
				$receivingorder = D('ReceivingorderView');
				$list = $receivingorder->order($order)->where($where)->page($p.',10')->select();
				$sum_money = $receivingorder->where($where)->sum('money');
				foreach($list as $k=>$v){
					$money += $v['money'];
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					if($by == 'deleted'){
						$list[$k]['deleted'] = getUserByRoleId($v['delete_role_id']);
					}
				}
				$money = number_format($money,2);
				$sum_money = number_format($sum_money,2);
				$count = $receivingorder->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,10);
				$params[] = 'by=' . trim($_GET['by']);
				$params[] = 't=' . $this->type;
				
				$this->parameter = implode('&', $params);
				if ($_GET['desc_order']) {
					$params[] = "desc_order=" . trim($_GET['desc_order']);
				} elseif($_GET['asc_order']){
					$params[] = "asc_order=" . trim($_GET['asc_order']);
				}
				
				$Page->parameter = implode('&', $params);
				$show = $Page->show();
				
				$this->alert = parseAlert();
				$this->assign('page',$show);
				$this->assign('list',$list);
				$this->assign('money',$money);
				$this->assign('sum_money',$sum_money);
				$this->display('receivingorder');
				break;
			case 'paymentorder' :
				$paymentorder = D('PaymentorderView');
				$list = $paymentorder->order($order)->where($where)->page($p.',10')->select();
				$sum_money = $paymentorder->where($where)->sum('money');
				foreach($list as $k=>$v){
					$money +=$v['money'];
					$list[$k]['owner'] = getUserByRoleId($v['owner_role_id']);
					if($by == 'deleted'){
						$list[$k]['deleted'] = getUserByRoleId($v['delete_role_id']);
					}
				}
				$money = number_format($money,2);
				$sum_money = number_format($sum_money,2);
				$count = $paymentorder->where($where)->count();
				import("@.ORG.Page");
				$Page = new Page($count,10);
				$params[] = 'by=' . trim($_GET['by']);
				$params[] = 't=' . $this->type;
				
				$this->parameter = implode('&', $params); 
				if ($_GET['desc_order']) {
					$params[] = "desc_order=" . trim($_GET['desc_order']);
				} elseif($_GET['asc_order']){
					$params[] = "asc_order=" . trim($_GET['asc_order']);
				}
				
				$Page->parameter = implode('&', $params);
				$show = $Page->show();
				
				$this->alert = parseAlert();
				$this->assign('page',$show);
				$this->assign('money',$money);
				$this->assign('sum_money',$sum_money);
				$this->assign('list',$list);
				$this->display('paymentorder');
				break;
		}
	}
	
	public function add(){
		switch ($this->type) {
			case 'receivables' :
				if($_POST['submit']){
					$receivables = M('receivables');
					$data['name'] = trim($_POST['name'])?trim($_POST['name']):alert('error',L('PLEASE_FILL_IN_THE_NAME'),$_SERVER['HTTP_REFERER']);
					$data['price'] = $_POST['price'];
					if(empty($data['price'])){
						alert('error','请填写金额', $_SERVER['HTTP_REFERER']);
					}
					$data['customer_id'] = intval($_POST['customer_id'])?intval($_POST['customer_id']):alert('error',L('PLEASE_SELECT_CUSTOMERS'),$_SERVER['HTTP_REFERER']);
					$data['contract_id'] = intval($_POST['contract_id']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):session('role_id');
					$data['create_time'] = time();
					$data['update_time'] = time();
					$data['status'] = 0;
					
					if($id = $receivables->add($data)){
						if(intval($_POST['check_add_order']) == 1){
							$data_order['name'] = (trim($_POST['order_name']) && (trim($_POST['order_name']) != L('AUTOMATIC_GENERATION')))?trim($_POST['name']):'5kcrm'.date('Ymd').mt_rand(1000,9999);
							$data_order['money'] = trim($_POST['order_money'])?trim($_POST['order_money']):alert('error',L('PLEASE_FILL_IN_THE_AMOUNT'),$_SERVER['HTTP_REFERER']);
							$data_order['status'] = $_POST['order_status'];
							$data_order['description'] = trim($_POST['order_description']);
							$data_order['pay_time'] = strtotime($_POST['order_pay_time'])?strtotime($_POST['order_pay_time']):time();
							$data_order['creator_role_id'] = session('role_id');
							$data_order['owner_role_id'] = $data['owner_role_id'];
							$data_order['create_time'] = time();
							$data_order['receivables_id'] = $id;
							$ro_id = M('receivingorder')->add($data_order);
							actionLog($ro_id,'t=receivingorder');
							if($_POST['order_status'] == 1){
								$receivables = M('receivables')->where(array('receivables_id'=>$id))->find();
								
								if($data_order['money'] >= $receivables['price']){
									M('receivables')->where(array('receivables_id'=>$id))->setField('status','2');
								}elseif($data_order['money'] > 0){
									M('receivables')->where(array('receivables_id'=>$id))->setField('status','1');
								}
							}
						}
						if($_POST['submit'] == L('SAVE')){
							actionLog($id,'t=receivables');
							if($_POST['refer_url']){
								alert('success',L('ADD SUCCESS',array('')),$_POST['refer_url']);
							}else{
								alert('success',L('ADD SUCCESS',array('')),U('finance/index', 't=receivables'));
							}
						}else{
							alert('success',L('ADD SUCCESS',array('')),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('ADDING FAILS CONTACT THE ADMINISTRATOR',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->alert = parseAlert();
					$this->display('receivablesadd');
				}
				break;
			case 'payables' :
				if($_POST['submit']){
					$payables = M('payables');
					$data['name'] = trim($_POST['name'])?trim($_POST['name']):alert('error',L('PLEASE_FILL_IN_THE_NAME'),$_SERVER['HTTP_REFERER']);
					$data['price'] = $_POST['price'];
					if(empty($data['price'])){
						alert('error','请填写金额', $_SERVER['HTTP_REFERER']);
					}
					$data['customer_id'] = intval($_POST['customer_id'])?intval($_POST['customer_id']):alert('error',L('PLEASE_SELECT_CUSTOMERS'),$_SERVER['HTTP_REFERER']);
					$data['contract_id'] = intval($_POST['contract_id']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):session('role_id');
					$data['create_time'] = time();
					$data['update_time'] = time();
					$data['status'] = 0;
					
					if($id = $payables->add($data)){
						if(intval($_POST['check_add_order']) == 1){
							$data_order['name'] = (trim($_POST['order_name']) && (trim($_POST['order_name']) != L('AUTOMATIC_GENERATION')))?trim($_POST['name']):'5kcrm'.date('Ymd').mt_rand(1000,9999);
							$data_order['money'] = trim($_POST['order_money'])?trim($_POST['order_money']):alert('error',L('PLEASE_FILL_IN_THE_AMOUNT'),$_SERVER['HTTP_REFERER']);
							$data_order['status'] = $_POST['order_status'];
							$data_order['description'] = trim($_POST['order_description']);
							$data_order['pay_time'] = strtotime($_POST['order_pay_time'])?strtotime($_POST['order_pay_time']):time();
							$data_order['creator_role_id'] = session('role_id');
							$data_order['owner_role_id'] = $data['owner_role_id'];
							$data_order['create_time'] = time();
							$data_order['payables_id'] = $id;
							$po_id = M('paymentorder')->add($data_order);
							actionLog($po_id,'t=paymentorder');
							
							if($_POST['order_status'] == 1){
								$payables = M('payables')->where(array('payables_id'=>$id))->find();
								
								if($data_order['money'] >= $payables['price']){
									M('payables')->where(array('payables_id'=>$id))->setField('status','2');
								}elseif($data_order['money'] > 0){
									M('payables')->where(array('payables_id'=>$id))->setField('status','1');
								}
							}
						}	
						
						if($_POST['submit'] == L('SAVE')){
							actionLog($id,'t=payables');
							if($_POST['refer_url']){
								alert('success',L('ADD SUCCESS',array('')),$_POST['refer_url']);
							}else{
								alert('success',L('ADD SUCCESS',array('')),U('finance/index', 't=payables'));
							}
						}else{
							alert('success',L('ADD SUCCESS',array('')),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('ADDING FAILS CONTACT THE ADMINISTRATOR',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->alert = parseAlert();
					$this->display('payablesadd');
				}
				break;
			case 'receivingorder' :
				if($_POST['submit']){
					$receivingorder = M('receivingorder');
					$data['name'] = (trim($_POST['name']) && (trim($_POST['name']) != L('AUTOMATIC_GENERATION')))?trim($_POST['name']):'5kcrm'.date('Ymd').mt_rand(1000,9999);
					$data['money'] = $_POST['money'];
					$data['receivables_id'] = intval($_POST['receivables_id'])?intval($_POST['receivables_id']):alert('error',L('PLEASE_SELECT_RECEIVABLES'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					$data['create_time'] = time();
					$data['status'] = $_POST['status'];
					if($data['status'] == 1){
						$data['update_time'] = time();
					}
					
					if($receivingorder->add($data)){
						actionLog($id,'t=receivingorder');
						$receivables = M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->find();
						$moneys = $receivingorder->where(array('receivables_id'=>$data['receivables_id'],'status'=>1))->select();
						foreach($moneys as $money){
							$money_sum += $money['money'];
						}
						if($money_sum >= $receivables['price']){
							M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->save(array('status'=>2));
						}elseif($money_sum > 0){
							M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->save(array('status'=>1));
						}
						if($_POST['submit'] == L('SAVE')){
							alert('success', L('ADD SUCCESS',array('')), U('finance/index','t='.$this->type));
						}else{
							alert('success',L('ADD SUCCESS',array('')),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('ADDING FAILS CONTACT THE ADMINISTRATOR',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->alert = parseAlert();
					$this->display('receivingorderadd');
				}
				break;
			case 'paymentorder' :
				if($_POST['submit']){
					$paymentorder = M('paymentorder');
					$data['name'] = (trim($_POST['name']) && (trim($_POST['name']) != L('AUTOMATIC_GENERATION')))?trim($_POST['name']):'5kcrm'.date('Ymd').mt_rand(1000,9999);
					$data['money'] = $_POST['money'];
					$data['payables_id'] = intval($_POST['payables_id'])?intval($_POST['payables_id']):alert('error',L('PLEASE_SELECT_PAYABLES'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					$data['creator_role_id'] = session('role_id');
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					$data['create_time'] = time();
					$data['status'] = $_POST['status'];
					if($data['status'] == 1){
						$data['update_time'] = time();
					}
					
					if($paymentorder->add($data)){
						actionLog($id,'t=paymentorder');
						$payables = M('payables')->where(array('payables_id'=>$data['payables_id']))->find();
						$moneys = $paymentorder->where(array('payables_id'=>$data['payables_id'],'status'=>1))->select();
						foreach($moneys as $money){
							$money_sum += $money['money'];
						}
						if($money_sum >= $payables['price']){
							M('payables')->where(array('payables_id'=>$data['payables_id']))->save(array('status'=>2));
						}elseif($money_sum > 0){
							M('payables')->where(array('payables_id'=>$data['payables_id']))->save(array('status'=>1));
						}
						if($_POST['submit'] == L('SAVE')){
							alert('success', L('ADD SUCCESS',array('')),  U('finance/index','t='.$this->type));
						}else{
							alert('success',L('ADD SUCCESS',array('')),$_SERVER['HTTP_REFERER']);
						}
					}else{
						alert('error',L('ADDING FAILS CONTACT THE ADMINISTRATOR',array('')),$_SERVER['HTTP_REFERER']);
						
					}
				}else{
					$this->alert = parseAlert();
					$this->display('paymentorderadd');
				}
				break;
		}
	}
	public function edit(){
		$id = intval($_REQUEST['id']);
		if($id == 0) alert('error',L('PARAMETER_ERROR'),U('finance/index','t='.$this->type));
		switch ($this->type) {
			case 'receivables' :
				
				$receivables = D('ReceivablesView');
				$info = $receivables->where(array('receivables_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				if($_POST['submit']){
					$data['name'] = trim($_POST['name'])?trim($_POST['name']):alert('error',L('PLEASE_FILL_IN_THE_NAME'),$_SERVER['HTTP_REFERER']);
					$data['price'] = $_POST['price'];
					$data['customer_id'] = intval($_POST['customer_id'])?intval($_POST['customer_id']):alert('error',L('PLEASE_SELECT_CUSTOMERS'),$_SERVER['HTTP_REFERER']);
					$data['contract_id'] = intval($_POST['contract_id']);
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					
					if(M('receivables')->where(array('receivables_id'=>$id))->save($data)){
						actionLog($id,'t=receivables');
						alert('success',L('EDIT SUCCESS',array('')),U('finance/view','id='.$id.'&t='.$this->type));
					}else{
						alert('error',L('EDIT FAILED',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->assign('info',$info);
					$this->alert = parseAlert();
					$this->display('receivablesedit');
				}
				break;
			case 'payables' :
				$payables = D('PayablesView');
				$info = $payables->where(array('payables_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				if($_POST['submit']){
					$data['name'] = trim($_POST['name'])?trim($_POST['name']):alert('error',L('PLEASE_FILL_IN_THE_NAME'),$_SERVER['HTTP_REFERER']);
					$data['price'] = $_POST['price'];
					$data['customer_id'] = intval($_POST['customer_id'])?intval($_POST['customer_id']):alert('error',L('PLEASE_SELECT_CUSTOMERS'),$_SERVER['HTTP_REFERER']);
					$data['contract_id'] = intval($_POST['contract_id']);
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					
					if(M('payables')->where(array('payables_id'=>$id))->save($data)){
						actionLog($id,'t=payables');
						alert('success',L('EDIT SUCCESS',array('')),U('finance/view','id='.$id.'&t='.$this->type));
					}else{
						alert('error',L('EDIT FAILED',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->assign('info',$info);
					$this->alert = parseAlert();
					$this->display('payablesedit');
				}
				break;
			case 'receivingorder' :
				$receivingorder = D('ReceivingorderView');
				$info = $receivingorder->where(array('receivingorder_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				if($info['status'] == 1) alert('error',L('THE RECEIVABLES ORDER HAS BEEN CLOSING'),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				if($_POST['submit']){
					$data['name'] = trim($_POST['name']);
					$data['money'] = $_POST['money'];
					$data['receivables_id'] = intval($_POST['receivables_id'])?intval($_POST['receivables_id']):alert('error',L('PLEASE_SELECT_PAYABLES'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					if($info['owner_role_id'] == session('role_id')){
						$data['status'] = intval($_POST['status']);
					}
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					if($data['status'] == 1){
						$data['update_time'] = time();
					}
					
					if(M('receivingorder')->where(array('receivingorder_id'=>$id))->save($data)){
						actionLog($id,'t=receivingorder');
						$receivables = M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->find();
						$moneys = $receivingorder->where(array('receivables_id'=>$data['receivables_id']))->select();
						foreach($moneys as $money){
							$money_sum += $money['money'];
						}
						if($money_sum >= $receivables['price']){
							M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->save(array('status'=>2));
						}elseif($money > 0){
							M('receivables')->where(array('receivables_id'=>$data['receivables_id']))->save(array('status'=>1));
						}
						if($_POST['refer_url'])
						{
						   alert('success',L('EDIT SUCCESS',array('')),$_POST['refer_url']);
						}
						alert('success',L('EDIT SUCCESS',array('')),U('finance/view','id='.$id.'&t='.$this->type));

					}else{
						alert('error',L('EDIT FAILED',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->assign('info',$info);
					$this->refer_url=$_SERVER['HTTP_REFERER'];
					$this->alert = parseAlert();
					$this->display('receivingorderedit');
				}
				break;
			case 'paymentorder' :
				$paymentorder = D('PaymentorderView');
				$info = $paymentorder->where(array('paymentorder_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				if($info['status'] == 1) alert('error',L('THE PAYMENT ORDER HAS BEEN CLOSING'),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				if($_POST['submit']){
					$data['name'] = trim($_POST['name']);
					$data['money'] = $_POST['money'];
					$data['payables_id'] = intval($_POST['payables_id'])?intval($_POST['payables_id']):alert('error',L('PLEASE_SELECT_PAYABLES'),$_SERVER['HTTP_REFERER']);
					$data['description'] = trim($_POST['description']);
					$data['owner_role_id'] = intval($_POST['owner_role_id'])?intval($_POST['owner_role_id']):alert('error',L('PLEASE_SELECT_THE_PERSON_IN_CHARGE'),$_SERVER['HTTP_REFERER']);
					if($info['owner_role_id'] == session('role_id')){
						$data['status'] = intval($_POST['status']);
					}
					$data['pay_time'] = strtotime($_POST['pay_time'])?strtotime($_POST['pay_time']):time();
					if($data['status'] == 1){
						$data['update_time'] = time();
					}
					
					if(M('paymentorder')->where(array('paymentorder_id'=>$id))->save($data)){
						actionLog($id,'t=paymentorder');
						$payables = M('payables')->where(array('payables_id'=>$data['payables_id']))->find();
						$moneys = $paymentorder->where(array('payables_id'=>$data['payables_id']))->select();
						foreach($moneys as $money){
							$money_sum += $money['money'];
						}
						if($money_sum >= $payables['price']){
							M('payables')->where(array('payables_id'=>$data['payables_id']))->save(array('status'=>2));
						}elseif($money > 0){
							M('payables')->where(array('payables_id'=>$data['payables_id']))->save(array('status'=>1));
						}
						alert('success',L('EDIT SUCCESS',array('')),U('finance/view','id='.$id.'&t='.$this->type));
					}else{
						alert('error',L('EDIT FAILED',array('')),$_SERVER['HTTP_REFERER']);
					}
				}else{
					$this->assign('info',$info);
					$this->alert = parseAlert();
					$this->display('paymentorderedit');
				}
				break;
		}
	}
	public function view(){
		$id = intval($_GET['id']);
		if($id == 0) alert('error',L('PARAMETER_ERROR'),U('finance/index','t='.$this->type));
		switch ($this->type) {
			
			case 'receivables' :
				$receivables = D('ReceivablesView');
				$receivingorder = D('ReceivingorderView');
				$info = $receivables->where(array('receivables_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['receivingorder'] = $receivingorder->where('receivingorder.is_deleted <> 1 and receivingorder.receivables_id = %d', $id)->select();
				$num = 0;					//已收款金额
				$num_unCheckOut = 0;		//未结账状态的金额
				$num_unReceivables = 0;		//还剩多少金额未收款
				foreach($info['receivingorder'] as $k=>$v){
					if($v['status'] == 1){
						//计算已结账状态的金额
						$info['receivingorder'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
						$num = $num + $v['money'];
					}else{
						//未结账状态的金额
						$info['receivingorder'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
						$num_unCheckOut = $num_unCheckOut + $v['money'];
					}
				}
				$num_unReceivables = ($info['price'] - $num) < 0 ? 0 : ($info['price'] - $num);
				$info['num'] = $num;
				$info['num_unReceivables'] = $num_unReceivables;
				$info['num_unCheckOut'] = $num_unCheckOut;
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				$this->assign('info',$info);
				$this->alert = parseAlert();
				$this->display('receivablesview');
				break;
			case 'payables' :
				$payables = D('PayablesView');
				$paymentorder = D('PaymentorderView');
				$info = $payables->where(array('payables_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['paymentorder'] = $paymentorder->where('paymentorder.is_deleted <> 1 and paymentorder.payables_id = %d', $id)->select();
				$num = 0;					//已付款金额
				$num_unCheckOut = 0;		//未结账状态的金额
				$num_unPayment = 0;			//还剩多少金额未付款
				foreach($info['paymentorder'] as $k=>$v){
					if($v['status'] == 1 ){
						//计算已结账状态的金额
						$info['paymentorder'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
						$num += $v['money'];
					}else{
						//未结账状态的金额
						$info['paymentorder'][$k]['owner'] = getUserByRoleId($v['owner_role_id']);
						$num_unCheckOut += $v['money'];
					}
				}
				$num_unPayment = ($info['price'] - $num) < 0 ? 0 : ($info['price'] - $num);
				$info['num'] = $num;
				$info['num_unPayment'] = $num_unPayment;
				$info['num_unCheckOut'] = $num_unCheckOut;
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				$this->assign('info',$info);
				$this->alert = parseAlert();
				$this->display('payablesview');
				break;
			case 'receivingorder' :
				$receivingorder = D('ReceivingorderView');
				$info = $receivingorder->where(array('receivingorder_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				$contract_id = M('receivables')->where(array('receivables_id'=>$info['receivables_id']))->getField('contract_id');
				$info['other'] = D('ContractView')->where(array('contract_id'=>$contract_id))->find();
				$this->assign('info',$info);
				$this->alert = parseAlert();
				$this->display('receivingorderview');
				break;
			case 'paymentorder' :
				$paymentorder = D('PaymentorderView');
				$info = $paymentorder->where(array('paymentorder_id'=>$id))->find();
				if(empty($info)) alert('error',L('RECORD NOT EXIST',array('')),U('finance/index','t='.$this->type));
				$info['owner'] = getUserByRoleId($info['owner_role_id']);
				$contract_id = M('payables')->where(array('payables_id'=>$info['payables_id']))->getField('contract_id');
				$info['other'] = D('ContractView')->where(array('contract_id'=>$contract_id))->find();
				$this->assign('info',$info);
				$this->alert = parseAlert();
				$this->display('paymentorderview');
				break;
		}
	}
	public function delete(){
		switch ($this->type) {
			case 'receivables' :
				$receivables_ids = is_array($_REQUEST['receivables_id']) ? implode(',', $_REQUEST['receivables_id']) : $_REQUEST['id'];
				if($receivables_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				$receivables = M('receivables');
				$receivingorder = M('Receivingorder');
				//如果应收款下有收款单记录，提示先删除收款单
				$error_tip = '';
				$receivables_record = $receivables->where('is_deleted <> 1 and receivables_id in ('.$receivables_ids.')')->select();
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				foreach($receivables_record as $k=>$v){
					$receivingorder_record = $receivingorder->where('receivables_id = %d',$v['receivables_id'])->count();
					if($receivingorder_record == 0){
						
						if(!$receivables->where('receivables_id = %d', $v)->setField($data)){
							alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
						}
					}else{
						$error_tip .= $v['name'].',';
						actionLog($v,'t=receivables');
					}		
				}
				if($error_tip){
					alert('error',L('PARTIAL DELETION FAILED',array($error_tip)),$_SERVER['HTTP_REFERER']);
				}else{
					if($_GET['refer']){
						alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('success',L('DELETED SUCCESSFULLY'),U('finance/index','t='.$this->type));
					}
				}
				break;
			case 'payables' :
				$payables_ids = is_array($_REQUEST['payables_id']) ? implode(',', $_REQUEST['payables_id']) : $_REQUEST['id'];
				if($payables_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				
				$payables = M('Payables');
				$paymentorder = M('Paymentorder');
				//如果应付款下有付款单记录，提示先删除付款单
				$error_tip = '';
				$payables_record = $payables->where('is_deleted <> 1 and payables_id in ('.$payables_ids.')')->select();
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				foreach($payables_record as $k=>$v){
					$paymentorder_record = $paymentorder->where('payables_id = %d',$v['payables_id'])->count();
					if($paymentorder_record == 0){
						if(!$payables->where('payables_id = %d', $v)->setField($data)){
							actionLog($v,'t=payables');
							alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
						}
					}else{
						$error_tip .= $v['name'].',';
						actionLog($v,'t=payables');
					}
				}
				if($error_tip){
					alert('error',L('PARTIAL DELETION FAILED',array($error_tip)),$_SERVER['HTTP_REFERER']);
				}else{
					if($_GET['refer']){
						alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('success',L('DELETED SUCCESSFULLY'),U('finance/index','t='.$this->type));
					}
				}
				break;
			case 'receivingorder' : 
				$receivingorder_ids = is_array($_REQUEST['receivingorder_id']) ? implode(',', $_REQUEST['receivingorder_id']) : $_REQUEST['id'];
				if($receivingorder_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				$receivingorder = M('receivingorder');
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($receivingorder->where('receivingorder_id in (%s)', $receivingorder_ids)->setField($data)){
					$receivingorder_idsArr = explode(',',$receivingorder_ids);
					foreach($receivingorder_idsArr as $v){
						actionLog($v,'t=receivingorder');
					}
					if($_GET['refer']){
						alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('success',L('DELETED SUCCESSFULLY'),U('finance/index','t='.$this->type));
					}
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
			case 'paymentorder' :
				$paymentorder_ids = is_array($_REQUEST['paymentorder_id']) ? implode(',', $_REQUEST['paymentorder_id']) : $_REQUEST['id'];
				if($paymentorder_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				$paymentorder = M('paymentorder');
				$data = array('is_deleted'=>1, 'delete_role_id'=>session('role_id'), 'delete_time'=>time());
				if($paymentorder->where('paymentorder_id in (%s)', $paymentorder_ids)->setField($data)){
					$paymentorder_idsArr = explode(',',$paymentorder_ids);
					foreach($paymentorder_idsArr as $v){
						actionLog($v,'t=paymentorder');
					}
					if($_GET['refer']){
						alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('success',L('DELETED SUCCESSFULLY'),U('finance/index','t='.$this->type));
					}
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
		}
	}
	public function revert(){
		$id = intval($_GET['id']);
		if($id == 0) alert('error',L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
		switch ($this->type) {
			case 'receivables' :
				$receivables = M('receivables');
				$info = $receivables->where('receivables_id = %s', $id)->find();
				if (session('?admin') || $info['delete_role_id'] == session('role_id')) {
					if($receivables->where('receivables_id = %s', $id)->setField('is_deleted', 0)){
						actionLog($id,'t=receivables');
						alert('success',L('RESTORE SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('RESTORE FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				break;
			case 'payables' :
				$payables = M('payables');
				$info = $payables->where('payables_id = %s', $id)->find();
				if (session('?admin') || $info['delete_role_id'] == session('role_id')) {
					if($payables->where('payables_id = %s', $id)->setField('is_deleted', 0)){
						actionLog($id,'t=payables');
						alert('success',L('RESTORE SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('RESTORE FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				break;
			case 'receivingorder' :
				$receivingorder = M('receivingorder');
				$info = $receivingorder->where('receivingorder_id = %s', $id)->find();
				if (session('?admin') || $info['delete_role_id'] == session('role_id')) {
					if($receivingorder->where('receivingorder_id = %s', $id)->setField('is_deleted', 0)){
						actionLog($id,'t=receivingorder');
						alert('success',L('RESTORE SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('RESTORE FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				break;
			case 'paymentorder' :
				$paymentorder = M('paymentorder');
				$info = $paymentorder->where('paymentorder_id = %s', $id)->find();
				if (session('?admin') || $info['delete_role_id'] == session('role_id')) {
					if($paymentorder->where('paymentorder_id = %s', $id)->setField('is_deleted', 0)){
						actionLog($id,'t=paymentorder');
						alert('success',L('RESTORE SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
					}else{
						alert('error',L('RESTORE FAILURE'),$_SERVER['HTTP_REFERER']);
					}
				}else{
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
				break;
		}
	}
	public function completedelete(){
		if(!session('?admin')) alert('error',L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
		switch ($this->type) {
			case 'receivables' :
				$receivables_ids = is_array($_REQUEST['receivables_id']) ? implode(',', $_REQUEST['receivables_id']) : $_REQUEST['id'];
				if($receivables_ids == '') alert('error',L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
				$receivables = M('receivables');
				if($receivables->where('receivables_id in (%s)', $receivables_ids)->delete()){
					$receivables_idsArr = explode(',',$receivables_ids);
					foreach($receivables_idsArr as $v){
						actionLog($v,'t=receivables');
					}
					alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
			case 'payables' :
				$payables_ids = is_array($_REQUEST['payables_id']) ? implode(',', $_REQUEST['payables_id']) : $_REQUEST['id'];
				if($payables_ids == '') alert('error','没有选中任何信息',$_SERVER['HTTP_REFERER']);
				$payables = M('payables');
				if($payables->where('payables_id in (%s)', $payables_ids)->delete()){
					$payables_idsArr = explode(',',$payables_ids);
					foreach($payables_idsArr as $v){
						actionLog($v,'t=payables');
					}
					alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
			case 'receivingorder' :
				$receivingorder_ids = is_array($_REQUEST['receivingorder_id']) ? implode(',', $_REQUEST['receivingorder_id']) : $_REQUEST['id'];
				if($receivingorder_ids == '') alert('error',L('NOT CHOOSE ANY'),$_SERVER['HTTP_REFERER']);
				$receivingorder = M('receivingorder');
				if($receivingorder->where('receivingorder_id in (%s)', $receivingorder_ids)->delete()){
					$receivingorder_idsArr = explode(',',$receivingorder_ids);
					foreach($receivingorder_idsArr as $v){
						actionLog($v,'t=receivingorder');
					}
					alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
			case 'paymentorder' :
				$paymentorder_ids = is_array($_REQUEST['paymentorder_id']) ? implode(',', $_REQUEST['paymentorder_id']) : $_REQUEST['id'];
				if($paymentorder_ids == '') alert('error',L('NOT CHOOSE ANY'), $_SERVER['HTTP_REFERER']);
				$paymentorder = M('paymentorder');
				if($paymentorder->where('paymentorder_id in (%s)', $paymentorder_ids)->delete()){
					$paymentorder_idsArr= explode(',',$paymentorder_ids);
					foreach($paymentorder_idsArr as $v){
						actionLog($v,'t=paymentorder');
					}
					alert('success',L('DELETED SUCCESSFULLY'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('error',L('DELETE FAILED CONTACT THE ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
				}
				break;
		}
	}
	public function listdialog(){
		$receivables = D('ReceivablesView');
		$all_ids = implode(',', getSubRoleId());
		switch ($this->type) {
			case 'receivables' :
				$list = $receivables->where("receivables.is_deleted = 0 and receivables.status <> 2 and receivables.owner_role_id in($all_ids)")->order('receivables.update_time desc')->limit(10)->select();
				$count = $receivables->where("receivables.is_deleted = 0 and receivables.status <> 2 and receivables.owner_role_id in($all_ids)")->count();
	
				$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
				$this->count_num = $count;
				$this->assign('receivablesList',$list);
				$this->display('receivableslistdialog');
				break;
			case 'payables' :
				$payables = D('PayablesView');
				
				$this->payablesList = $payables->where("payables.is_deleted = 0 and payables.status <> 2 and payables.owner_role_id in($all_ids)")->order('payables.update_time desc')->limit(10)->select();
				$count = $payables->where("payables.is_deleted = 0 and payables.status <> 2 and payables.owner_role_id in($all_ids)")->count();
				$this->total = $count%10 > 0 ? ceil($count/10) : $count/10;
				$this->count_num = $count;
				$this->display('payableslistdialog');
				break;
		}
	}
	public function adddialog(){
		$contract_id = $this->_get('contract_id','intval',0);
		if($contract_id == 0){
			$id = $this->_get('id','intval',0);
			$this->assign('id',$id);
		}else{
			$contract_id = intval($_GET['contract_id']);
			$this->assign('contract_id',$contract_id);
			$business_id = M('contract')->where(array('contract_id'=>$contract_id))->getField('business_id');
			$customer_id = M('business')->where(array('business_id'=>$business_id))->getField('customer_id');
			$this->assign('customer_id',$customer_id);
		}
		switch ($this->type) {
			case 'receivables' :
				$this->refer_url = $_SERVER['HTTP_REFERER'];
				$this->display('receivablesadddialog');
				break;
			case 'payables' :
				$this->refer_url = $_SERVER['HTTP_REFERER'];
				$this->display('payablesadddialog');
				break;
			case 'receivingorder' :
				$m_receivables = M('Receivables');
				$m_receivingorder = M('Receivingorder');
				$receivables = $m_receivables->where('is_deleted <> 1 and receivables_id = %d',$id)->find();
				$receivingorder = $m_receivingorder->where('is_deleted <> 1 and receivables_id = %d',$receivables['receivables_id'])->select();
				
				$receivables_money = 0;//已收款总计
				foreach($receivingorder as $v){
					$receivables_money += $v['money'];
				}
				$this->assign('receivables_money',$receivables_money);
				$this->assign('receivables',$receivables);
				$this->display('receivingorderadddialog');
				break;
			case 'paymentorder' :
				$m_payables = M('Payables');
				$m_paymentorder = M('Paymentorder');
				$payables = $m_payables->where('is_deleted <> 1 and payables_id = %d',$id)->find();
				$paymentorder = $m_paymentorder->where('is_deleted <> 1 and payables_id = %d',$payables['payables_id'])->select();
				
				$payables_money = 0;//已收款总计
				foreach($paymentorder as $v){
					$payables_money += $v['money'];
				}
				$this->assign('payables_money',$payables_money);
				$this->assign('payables',$payables);
				$this->display('paymentorderadddialog');
				break;
		}
	}
	public function checkout(){
		switch ($this->type) {
			case 'receivingorder' :
				$receivingorder_ids = is_array($_REQUEST['receivingorder_id']) ? implode(',', $_REQUEST['receivingorder_id']) : $_REQUEST['id'];
				if($receivingorder_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				$receivingorder = M('receivingorder');
				$data = array('status'=>1);
				if($receivingorder->where('receivingorder_id in (%s)', $receivingorder_ids)->setField($data)){
					alert('success',L('SUCCESSFUL OPERATION'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('success',L('OPERATION FAILED'),$_SERVER['HTTP_REFERER']);
				}
				break;
			case 'paymentorder' :
				$paymentorder_ids = is_array($_REQUEST['paymentorder_id']) ? implode(',', $_REQUEST['paymentorder_id']) : $_REQUEST['id'];
				if($paymentorder_ids == '') alert('error',L('NOT CHOOSE ANY'),U('finance/index','t='.$this->type));
				$paymentorder = M('paymentorder');
				$data = array('status'=>1);
				if($paymentorder->where('paymentorder_id in (%s)', $paymentorder_ids)->setField($data)){
					alert('success',L('OPERATION SUCCESSFUL'),$_SERVER['HTTP_REFERER']);
				}else{
					alert('success',L('OPERATION FAILED'),$_SERVER['HTTP_REFERER']);
				}
				break;
		}
	}
	
	/**
	 * 根据receivables_id获取应收金额
	 *
	 **/
	public function getreceivablesmoney(){
		$id = $_GET['id'];
		if($id){
			$m_receivables = M('receivables');
			//应收款总额
			$receivables = $m_receivables->where('receivables_id = %d', $id)->getField('price');
			if(empty($receivables)){
				$receivables = 0;
			}
			//已收款金额
			$m_receivingorder = M('receivingorder');
			$receivingorder = $m_receivingorder->where('receivables_id = %d and status = 1', $id)->sum('money');
			if(empty($receivingorder)){
				$receivingorder = 0;
			}
			$this->ajaxReturn(array('total'=>$receivables, 'receivingorder'=>$receivingorder),'',1);
		}
	}
	
	/**
	 * 根据payables_id获取应付金额
	 *
	 **/
	public function getpayablesmoney(){
		$id = $_GET['id'];
		if($id){
			$m_payables = M('payables');
			//应收款总额
			$payables = $m_payables->where('payables_id = %d', $id)->getField('price');
			if(empty($payables)){
				$payables = 0;
			}
			//已收款金额
			$m_paymentorder = M('paymentorder');
			$paymentorder = $m_paymentorder->where('payables_id = %d and status = 1', $id)->sum('money');
			if(empty($paymentorder)){
				$paymentorder = 0;
			}
			$this->ajaxReturn(array('total'=>$payables, 'paymentorder'=>$paymentorder),'',1);
		}
	}
	
	public function analytics(){
		$m_shoukuan = M('receivables');
		$m_shoukuandan = M('receivingorder');
		$m_fukuan = M('payables');
		$m_fukuandan = M('paymentorder');
		if($_GET['role']) {
			$role_id = intval($_GET['role']);
		}else{
			$role_id = 'all';
		}
		if($_GET['department'] && $_GET['department'] != 'all'){
			$department_id = intval($_GET['department']);
		}else{
			$department_id = D('RoleView')->where('role.role_id = %d', session('role_id'))->getField('department_id'); 
		}
		if($_GET['start_time']) $start_time = strtotime(date('Y-m-d',strtotime($_GET['start_time'])));
		$end_time = $_GET['end_time'] ?  strtotime(date('Y-m-d 23:59:59',strtotime($_GET['end_time']))) : strtotime(date('Y-m-d 23:59:59',time()));
		if($role_id == "all") {
			$roleList = getRoleByDepartmentId($department_id);
			$role_id_array = array();
			foreach($roleList as $v2){
				$role_id_array[] = $v2['role_id'];
			}
			$where_role_id = array('in', implode(',', $role_id_array));
			$where_shoukuan['owner_role_id'] = $where_role_id;
		}else{
			$where_shoukuan['owner_role_id'] = $role_id;
		}
		$year = date('Y');
		$moon = 1;
		$shoukuan_moon_count = array();
		$fukuan_moon_count = array();
		$shijishoukuan_moon_count = array();
		$shijifukuan_moon_count = array();
		while ($moon <= 12){
			if($moon == 12) {
				$where_shoukuan['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime(($year+1).'-1-1')), 'and');
			} else {
				$where_shoukuan['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime($year.'-'.($moon+1).'-1')), 'and');
			}
			$shoukuanList = $m_shoukuan->where($where_shoukuan)->select();
			$fukuanList = $m_fukuan->where($where_shoukuan)->select();
			$total_shoukuan_money = 0;
			$total_shijishoukuan_money = 0;
			foreach($shoukuanList as $v){
				$total_shoukuan_money += $v['price'];
				$shoukuandan_list = $m_shoukuandan->where('receivables_id = %d', $v['receivables_id'])->getField('money', true);
				foreach($shoukuandan_list as $v2) {
					$total_shijishoukuan_money += $v2;
				}
			}

			$total_fukuan_money = 0;
			$total_shijifukuan_money = 0;
			foreach($fukuanList as $v){
				$total_fukuan_money += $v['price'];
				$fukuandan_list = $m_fukuandan->where('payables_id = %d', $v['payables_id'])->getField('money', true);
				foreach($fukuandan_list as $v2) {
					$total_shijifukuan_money += $v2;
				}
			}

			$shoukuan_moon_count[] = $total_shoukuan_money;
			$shijishoukuan_moon_count[] = $total_shijishoukuan_money;
			$fukuan_moon_count[] = $total_fukuan_money;
			$shijifukuan_moon_count[] = $total_shijifukuan_money;
			$moon ++;
		}
		$moon_count['shoukuan'] = '['.implode(',', $shoukuan_moon_count).']';
		$moon_count['shijishoukuan'] = '['.implode(',', $shijishoukuan_moon_count).']';
		$moon_count['fukuan'] = '['.implode(',', $fukuan_moon_count).']';
		$moon_count['shijifukuan'] = '['.implode(',', $shijifukuan_moon_count).']';
		$this->moon_count = $moon_count;
		
		$previous_year = $year-1;
		$moon = 1;
		$shoukuan_thisyear_count = array();
		$shoukuan_previousyear_count = array();
		$fukuan_thisyear_count = array();
		$fukuan_previousyear_count = array();
		while ($moon <= 12){
			if($moon == 12) {
				$where_thisyear_shoukuan['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime(($year+1).'-1-1')), 'and');
				$where_previousyear_shoukuan['pay_time'] = array(array('egt', strtotime($previous_year.'-'.$moon.'-1')), array('lt', strtotime(($previous_year+1).'-1-1')), 'and');
			} else {
				$where_thisyear_shoukuan['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime($year.'-'.($moon+1).'-1')), 'and');
				$where_previousyear_shoukuan['pay_time'] = array(array('egt', strtotime($previous_year.'-'.$moon.'-1')), array('lt', strtotime($previous_year.'-'.($moon+1).'-1')), 'and');
			}
			
			$thisyear_shoukuanList = $m_shoukuan->where($where_thisyear_shoukuan)->select();
			$previousyear_shoukuanList = $m_shoukuan->where($where_previousyear_shoukuan)->select();
			$thisyear_fukuanList = $m_fukuan->where($where_thisyear_shoukuan)->select();
			$previousyear_fukuanList = $m_fukuan->where($where_previousyear_shoukuan)->select();
			
			$total_thisyear_shoukuan_count = 0;
			$total_previousyear_shoukuan_count = 0;
			foreach($thisyear_shoukuanList as $v){
				$total_thisyear_shoukuan_count += $v['price'];
			}
			foreach($previousyear_shoukuanList as $v){
				$total_previousyear_shoukuan_count += $v['price'];
			}
			$shoukuan_thisyear_count[] = $total_thisyear_shoukuan_count;
			$shoukuan_previousyear_count[] = $total_previousyear_shoukuan_count;
			
			$total_thisyear_fukuan_count = 0;
			$total_previousyear_fukuan_count = 0;
			foreach($thisyear_fukuanList as $v){
				$total_thisyear_fukuan_count += $v['price'];
			}
			foreach($previousyear_fukuanList as $v){
				$total_previousyear_fukuan_count += $v['price'];
			}
			$fukuan_thisyear_count[] = $total_thisyear_fukuan_count;
			$fukuan_previousyear_count[] = $total_previousyear_fukuan_count;
			
			$moon ++; 
		}
		
		$year_count['shoukuan_previousyear'] = '['.implode(',', $shoukuan_previousyear_count).']';
		$year_count['shoukuan_thisyear'] = '['.implode(',', $shoukuan_thisyear_count).']';
		$year_count['fukuan_previousyear'] = '['.implode(',', $fukuan_previousyear_count).']';
		$year_count['fukuan_thisyear'] = '['.implode(',', $fukuan_thisyear_count).']';
		$this->year_count = $year_count;

		//统计表内容
		$role_id_array = array();
		if($role_id == "all"){
			if($department_id != "all"){
				$roleList = getRoleByDepartmentId($department_id);
				foreach($roleList as $v){
					$role_id_array[] = $v['role_id'];
				}
			}else{
				$role_id_array = getSubRoleId();
			}
		}else{
			$role_id_array[] = $role_id;
		}
		if($start_time){
			$create_time= array(array('elt',$end_time),array('egt',$start_time), 'and');
		}else{
			$create_time = array('elt',$end_time);
		}
		//应收款数 未收款 部分收款 应收金额 实际收款金额 应付款数 未付款 部分付款 应付金额 实际付款金额	
		$reportList = array();
		$shoukuan_count_total= 0; $weishou_count_total = 0; $bufenshoukuan_count_total = 0; $shoukuan_money_total = 0; $yishou_money_total = 0; $shoukuandan_count_total = 0;
		$fukuan_count_total= 0;  $weifu_count_total = 0; $bufenfukuan_count_total = 0; $fukuan_money_total = 0; $yifu_money_total = 0; $fukuandan_count_total = 0;
		foreach($role_id_array as $v){
			$user = getUserByRoleId($v);
			
			$shoukuan_count= 0; $weishou_count = 0; $bufenshoukuan_count = 0; $shoukuan_money = 0; $yishou_money = 0; $shoukuandan_count = 0;
			$fukuan_count= 0;  $weifu_count = 0; $bufenfukuan_count = 0; $fukuan_money = 0; $yifu_money = 0;
			$fukuandan_count = 0;
			
			$shoukuan_count = $m_shoukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count();

			$weishou_count = $m_shoukuan->where(array('is_deleted'=>0, 'status'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count(); 
			$bufenshoukuan_count = $m_shoukuan->where(array('is_deleted'=>0, 'status'=>1, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count(); 
			
			$shoukuandan_count = $m_shoukuandan->where(array('is_deleted'=>0,'owner_role_id'=>$v, 'pay_time'=>$create_time))->count();
			$shoukuan_money = round($m_shoukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->sum('price'),2);
			$shoukuan_id_array = $m_shoukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->getField('receivables_id', true);
			$shijishoukuan_money = 0;
			foreach($shoukuan_id_array as $v2){
				$shoukuandan_list = $m_shoukuandan->where('status = 1 and is_deleted=0 and receivables_id = %d', $v2)->getField('money', true);
				foreach($shoukuandan_list as $v3) {
					$shijishoukuan_money += $v3;
				}
			}
			$yishou_money =round($shijishoukuan_money,2);
			
			$fukuan_count = $m_fukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count();
			$weifu_count = $m_fukuan->where(array('is_deleted'=>0, 'status'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count(); 
			$bufenfukuan_count = $m_fukuan->where(array('is_deleted'=>0, 'status'=>1, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->count(); 			
			$fukuandan_count = $m_fukuandan->where(array('is_deleted'=>0,'owner_role_id'=>$v, 'pay_time'=>$create_time))->count(); 
			$fukuan_money = $n=round($m_fukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->sum('price'),2);
			$fukuan_id_array = $m_fukuan->where(array('is_deleted'=>0, 'owner_role_id'=>$v, 'pay_time'=>$create_time))->getField('payables_id', true);
			$shijifukuan_money = 0;
			foreach($fukuan_id_array as $v4){
				$fukuandan_list = $m_fukuandan->where('status = 1 and is_deleted=0 and payables_id = %d', $v4)->getField('money', true);
				foreach($fukuandan_list as $v5) {
					$shijifukuan_money += $v5;
				}
			}
			$yifu_money = round($shijifukuan_money,2);			
			
			$reportList[] = array("user"=>$user,"shoukuan_count"=>$shoukuan_count,"shoukuan_money"=>$shoukuan_money,"weishou_count"=>$weishou_count,"bufenshoukuan_count"=>$bufenshoukuan_count, "yishou_money"=>$yishou_money,"shoukuandan_count"=>$shoukuandan_count,
			"fukuan_count"=>$fukuan_count,'weifu_count'=>$weifu_count,"bufenfukuan_count"=>$bufenfukuan_count,"fukuan_money"=>$fukuan_money,"yifu_money"=>$yifu_money,"fukuandan_count"=>$fukuandan_count);

			$shoukuan_count_total += $shoukuan_count; $weishou_count_total += $weishou_count; $bufenshoukuan_count_total += $bufenshoukuan_count;
			$shoukuan_money_total += $shoukuan_money; $yishou_money_total += $yishou_money; $shoukuandan_count_total += $shoukuandan_count;
			$fukuan_count_total += $fukuan_count;  $weifu_count_total += $weifu_count;
			$bufenfukuan_count_total += $bufenfukuan_count; 
			$fukuan_money_total += $fukuan_money; $yifu_money_total += $yifu_money; 
			$fukuandan_count_total += $fukuandan_count;
		}
	
		$total_report = array("shoukuan_count"=>$shoukuan_count_total,"weishou_count"=>$weishou_count_total,"bufenshoukuan_count"=>$bufenshoukuan_count_total, "shoukuan_money"=>$shoukuan_money_total ,"yishou_money"=>$yishou_money_total, "shoukuandan_count"=>$shoukuandan_count_total, "fukuan_count"=>$fukuan_count_total, "weifu_count"=>$weifu_count_total, "bufenfukuan_count"=>$bufenfukuan_count_total, "fukuan_money"=>$fukuan_money_total, "yifu_money"=>$yifu_money_total, "fukuandan_count"=>$fukuandan_count_total);
		$this->reportList = $reportList;
		$this->total_report = $total_report;
		$idArray = getSubRoleId();
		$roleList = array();
		foreach($idArray as $roleId){				
			$roleList[$roleId] = getUserByRoleId($roleId);
		}
		$this->roleList = $roleList;
		
		$departments = M('roleDepartment')->select();
		$departmentList[] = M('roleDepartment')->where('department_id = %d', session('department_id'))->find();
		$departmentList = array_merge($departmentList, getSubDepartment(session('department_id'),$departments,''));
		$this->assign('departmentList', $departmentList);
		
		$this->display();
	}
	
	/**
	 * 首页应收款月度统计
	 * @ level 0:自己的数据  1:自己和下属的数据
	 **/
	public function getmonthlyreceive(){
		$m_receivables = M('receivables');
		$m_payables = M('payables');
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$where['creator_role_id'] = array('in',getSubRoleId());
		
		$year = date('Y');
		$moon = 1;
		$not_receive = array();//应收款
		$have_received = array();//实际收款
		$not_pay = array();//应付款
		$have_paid = array();//实际付款
		$where['is_deleted'] = array('eq', 0);
		while ($moon <= 12){
			if($moon == 12) {
				$where['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime(($year+1).'-1-1')), 'and');
			} else {
				$where['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime($year.'-'.($moon+1).'-1')), 'and');
			}
	
			$not_receiveList = $m_receivables->where($where)->select();//应收款数组
			$monthly_not_receive = 0;
			foreach($not_receiveList as $v){
				$monthly_not_receive = floatval(bcadd($monthly_not_receive, $v['price'], 2));//单月应收款总额
			}
			$not_receive[] = $monthly_not_receive;
			
			$condition = $where;
			$condition['status'] = array('neq', 0);
			$have_receivedList = $m_receivables->where($condition)->select();//(部分)已收款数组
			$monthly_have_received = 0;
			foreach($have_receivedList as $v){
				$monthly_have_received += M('receivingorder')->where('receivables_id = %d and is_deleted = 0',$v['receivables_id'])->sum('money');//单月实收款总额
			}
			$have_received[] = $monthly_have_received;
			
			$not_payList = $m_payables->where($where)->select();//应付款数组
			$monthly_not_pay = 0;
			foreach($not_payList as $v){
				$monthly_not_pay = floatval(bcadd($monthly_not_pay, $v['price'], 2));//单月实收款总额
			}
			$not_pay[] = $monthly_not_pay;
			
			$have_paidList = $m_payables->where($condition)->select();//(部分)已收款数组
			$monthly_have_paid = 0;
			foreach($have_paidList as $v){
				$monthly_have_paid += M('paymentorder')->where('payables_id = %d and is_deleted = 0',$v['payables_id'])->sum('money');//单月实收款总额
			}
			$have_paid[] = $monthly_have_paid;
			
			$moon ++;
		}
		$financeDate['not_receive'] = $not_receive;
		$financeDate['have_received'] = $have_received;
		$financeDate['not_pay'] = $not_pay;
		$financeDate['have_paid'] = $have_paid;
		$this->ajaxReturn($financeDate,'success',1);
	}
	
	/**
	 * 首页应收款年度对比统计
	 * @ level 0:自己的数据  1:自己和下属的数据
	 **/
	public function getYearReceiveComparison(){
		$m_receivables = M('receivables');
		$dashboard = M('user')->where('user_id = %d', session('user_id'))->getField('dashboard');
		$widget = unserialize($dashboard);
		$where['creator_role_id'] = array('in',getSubRoleId());

		$year = date('Y');
		$prev_year = $year-1;
		$moon = 1;
		$receive_this_year_money = array();
		$receive_prev_year_money = array();
		$where['is_deleted'] = array('eq', 0);
		$where_this_year = $where;
		$where_prev_year = $where;
		while ($moon <= 12){
			if($moon == 12) {
				$where_this_year['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime(($year+1).'-1-1')), 'and');
				$where_prev_year['pay_time'] = array(array('egt', strtotime($prev_year.'-'.$moon.'-1')), array('lt', strtotime(($year).'-1-1')), 'and');
			} else {
				$where_this_year['pay_time'] = array(array('egt', strtotime($year.'-'.$moon.'-1')), array('lt', strtotime($year.'-'.($moon+1).'-1')), 'and');
				$where_prev_year['pay_time'] = array(array('egt', strtotime($prev_year.'-'.$moon.'-1')), array('lt', strtotime($prev_year.'-'.($moon+1).'-1')), 'and');
			}

			$receive_this_year_price = $m_receivables->where($where_this_year)->sum('price');//今年月度收款金额总和
			$receive_prev_year_price = $m_receivables->where($where_prev_year)->sum('price');//去年月度收款金额总和
			$receive_this_year_money[] = empty($receive_this_year_price) ? 0 : round($receive_this_year_price,2);
			$receive_prev_year_money[] = empty($receive_prev_year_price) ? 0 : round($receive_prev_year_price,2);
			$moon ++; 
		}
		
		$total_money = array('this_year'=>$receive_this_year_money, 'prev_year'=>$receive_prev_year_money);
		$this->ajaxReturn($total_money,'success',1);
	}
}