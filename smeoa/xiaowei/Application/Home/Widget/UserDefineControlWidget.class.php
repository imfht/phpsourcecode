<?php
namespace Home\Widget;
use Think\Controller;

class UserDefineControlWidget extends Controller {
	protected $config = array('app_type' => 'public');

	public function edit($data) {

		if (empty($data['val'])) {
			$data['val'] = null;
		}
		
		if(strpos($data['val'],'|')!==false){
			$data['val'] = explode('|', $data['val']);
		}

		$data['data'] = $this -> _conv_data($data['data']);
		$this -> assign($data);
		//dump($data);
		$type = $data['type'];
		$this -> display("Widget:UserDefineControl/$type");
	}

	private function _conv_data($val) {
		$new = array();
		if (strpos($val, 'SYSTEM_CONFIG:') !== false) {
			$new = get_system_config(substr($val, 14));
			return $new;
		}
		if (strpos($val, 'SYSTEM:') !== false) {
			$data_type = substr($val, 7);

			if ($data_type = 'DEPT') {
				$dept_menu = M('Dept') -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();

				$dept_tree = list_to_tree($dept_menu);

				$list = tree_to_list($dept_tree);
				foreach ($list as $val) {
					$new[$val['id']] = str_pad("", $val['level'] * 3, "│") . "├─" . "{$val['name']}";
				}
			}
			if ($data_type = 'RPODUCT') {
				$dept_menu = M('Dept') -> field('id,pid,name') -> where("is_del=0") -> order('sort asc') -> select();

				$dept_tree = list_to_tree($dept_menu);

				$list = tree_to_list($dept_tree);
				foreach ($list as $val) {
					$new[$val['id']] = str_pad("", $val['level'] * 3, "│") . "├─" . "{$val['name']}";
				}
			}
			return $new;
		}

		if (strpos($val, "|") !== false) {
			$arr_tmp = explode(",", $val);
			foreach ($arr_tmp as $item) {
				$tmp = explode("|", $item);
				$new[$tmp[0]] = $tmp[1];
			}
			return $new;
		}

		$new = $val;

		return $new;
	}

}
?>