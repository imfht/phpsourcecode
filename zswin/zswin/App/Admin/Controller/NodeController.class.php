<?php
namespace Admin\Controller;
class NodeController extends CommonController {

	public function _filter(&$map) {
		if (!empty($_GET['group_id'])) {
			$map['group_id'] = $_GET['group_id'];
			$this->assign('nodeName', '分组');
		} elseif (empty($_POST['search']) && !isset($map['pid'])) {
			$map['pid'] = 1;
		}
		if ($_REQUEST['pid'] != '') {
			$map['pid'] = $_REQUEST['pid'];
		}
		$_SESSION['currentNodeId'] = $map['pid'];
		//获取上级节点
		$node = M("Node");
		if (isset($map['pid'])) {
			if ($node->getById($map['pid'])) {
				$this->assign('level', $node->level + 1);
				$this->assign('nodeName', $node->name);
				$this->assign('rebackid', $node->pid);
				$this->assign('currentid', $_REQUEST['pid']);
			} else {
				$this->assign('level', 1);
				$this->assign('rebackid', 1);
			}
		}
		
	}

	public function _before_index() {
		$model = M("Group");
		$list = $model->where('status=1')->getField('id,title');
		$this->assign('groupList', $list);

		
	}

	// 获取配置类型
	public function _before_add() {
		$model = M("Group");
		$list = $model->where('status=1')->select();
		$this->assign('list', $list);
		$node = M("Node");
		$node->getById($_SESSION['currentNodeId']);
		$this->assign('pid', $node->id);
		$this->assign('level', $node->level + 1);
	}

	public function _before_patch() {
		$model = M("Group");
		$list = $model->where('status=1')->select();
		$this->assign('list', $list);
		$node = M("Node");
		$node->getById($_SESSION['currentNodeId']);
		$this->assign('pid', $node->id);
		$this->assign('level', $node->level + 1);
	}

	public function _before_edit() {
		$model = M("Group");
		$list = $model->where('status=1')->select();
		$this->assign('list', $list);
	}

public function after_insert($id=''){
	
	        $map['id']=$id;
	        $action=D('Node')->where($map)->find();
	        $actionName = $action['name'];
	        
	        $map1['name']=$actionName;
		    $map1['pid']=1;
		    $count=D('Node')->where($map1)->count();
		    
	        if($count == 1){
	         if($action['remark'] == '' || $action['remark'] == 'index'){
	        	$actionName = ucfirst($actionName);

			/*--------硬编码测试--------*/
			//新建控制器
			$str_tmp = "<?php \n namespace Admin\Controller; \n class " . $actionName . "Controller extends CommonController { \n public function index() {\n \$this->display(); \n }} \n ?>";
			$newFile = dirname(__FILE__).'/'.$actionName."Controller.class.php"; //文件名
			$fp=fopen($newFile, "w"); //写方式打开文件
			fwrite($fp,$str_tmp); //存入内容
			fclose($fp); //关闭文件

			/*--------硬编码测试（默认模板）--------*/
			//新建模板
			$str_tmp = '<extend name="Public:common" /><block name="main">具体页面还需要各位制作。zswin一直在努力！</block>';
			$newPath = "./App/Admin/View/".$actionName."/";
			$newFile = $newPath."index.html"; //文件名
			//目录不存在则自动创建
			if(!fopen($newPath, 'r')){
				mkdir($newPath);
			}
			$fp2=fopen($newFile, "w"); //写方式打开文件
			fwrite($fp2,$str_tmp); //存入内容
			fclose($fp2); //关闭文件
	        }
	        }
	       
	        
			
	
}

	

}

?>