<?php
namespace App\Controller;

use App\BasicController;
use Swoole;

class Interfaces extends BasicController {

	function index() {
		$numPerPage=  getRequest('numPerPage',20,true);
		$pageNum=  getRequest('pageNum',1,true);
		$interface=  table('Interface','status_center');
		$params= [
			'order'=>'id',
			'limit'=>($pageNum-1)*$numPerPage.','.$numPerPage
		];	
		$total=$interface->count(['where'=>1]);
		$page=[
			'numPerPage'=>$numPerPage,
			'pageNum'=>$pageNum,
			'total'=>$total,
		];

		$data=$interface->gets($params);
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->display('interface/index.php');
	}

	function analyzeInterface(){
		$data=[];
		$page=[];
		$date=getRequest('data',"");
		$module_id=getRequest('module_id',123);
		$numPerPage=  getRequest('numPerPage',20,true);
		$pageNum=  getRequest('pageNum',1,true);
		if($date){
			$limit_str="limit ".($pageNum-1)*$numPerPage.",".$numPerPage;
			$table="stats_".$date;
			$db=Swoole::$php->db("status_center");
			$flag=$db->query(" show tables like '$table'")->fetch();
			if(!$flag){
				jsonReturn($this->ajaxFromReturn('数据不存在', 300));
			}
			$field="a.interface_id,a.module_id,b.`name` as interface_name,sum(a.total_count) as sum,sum(a.fail_count) as fail_sum ";
			$sql="SELECT $field from $table as  a
			  left join interface as b on a.interface_id=b.id
			  where a.module_id=$module_id
			  GROUP BY a.interface_id $limit_str";
			$data=$db->query($sql)->fetchall();
			$count_sql="Select count(1) as sum from (SELECT 1 from $table as  a
			  left join interface as b on a.interface_id=b.id
			  where a.module_id=$module_id
			  GROUP BY a.interface_id) as x ";
			$count=$db->query($count_sql)->fetch();
			$page=[
					'numPerPage'=>$numPerPage,
					'pageNum'=>$pageNum,
					'total'=>$count['sum'],
			];
		}
		$this->assign('data', $data);
		$this->assign('page', $page);
		$this->display("interface/analyze_index.php");
	}

	function addInterface() {
		if(isPost()){
			$interface=  table('Interface','status_center');
			$data=$interface->getData();
			if($interface->create($data)){
				jsonReturn($this->ajaxFromReturn('添加成功',200,'closeCurrent','','interface'));
			}else{
				jsonReturn($this->ajaxFromReturn('添加失败',300));
			}
		}
		$this->assign('title', '添加');
		$this->display("interface/add_interface.php");
	}

	function updateInterface() {
		$interface=  table('Interface','status_center');
		if(isPost()){
			$data=$interface->getData();
			if($interface->set($data['id'],$data)){
				jsonReturn($this->ajaxFromReturn('修改成功',200,'closeCurrent','','interface'));
			}else{
				jsonReturn($this->ajaxFromReturn('修改失败',300));
			}
		}
		$id=  getRequest('id');
		$data=$interface->get($id);
		$this->assign('data', $data);
		$this->assign('title', '修改');
		$this->display("interface/update_interface.php");
	}

	function deleteInterface() {
		$id = getRequest('id');
		$interface = table('Interface','status_center');
		if ($interface->del($id)) {
			jsonReturn($this->ajaxFromReturn('删除成功',200,'','','interface'));
		} else {
			jsonReturn($this->ajaxFromReturn('删除失败', 300));
		}
	}

	function searchInterface() {
		
	}

}	
