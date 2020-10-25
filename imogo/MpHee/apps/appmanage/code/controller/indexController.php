<?php
class indexController extends adminController{
	protected $layout = 'layout';
	
	public function demolist(){
		$this->demolist = model('demo')->demolist(array('ppid'=>$ppid));
		$this->display();
	}
	
	public function demoadd(){
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			if( model('demo')->demoadd($data)){
				$this->alert('添加成功', url('index/demolist'));
			}else{
				$this->alert('添加失败');
			}		
		}
	}
	
	public function demoedit(){
		if( !$this->isPost() ){
			$id = intval($_GET['id']);
			$demoinfo = model('demo')->demoinfo( array('id'=>$id) );
			if( empty($demoinfo) ){
				$this->alert('该条数据不存在或者已被删除');
			}
			$this->demoinfo = $demoinfo;
			$this->display();
		}else{
			$id = intval($_POST['id']);
			$data = $_POST;
			if( model('demo')->demoupdate(array('id'=>$id), $data) ){
				$this->alert('修改成功', url('index/demolist'));
			}else{
				$this->alert('修改失败');
			}
		}
	}
	
	public function demodel(){
		$id = intval($_GET['id']);
		$demoinfo = model('demo')->find( array('id'=>$id) );
		if( empty($demoinfo) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('demo')->demodelete( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/demolist'));
		}else{
			$this->alert('删除失败');
		}
	}
		
	public function democonfig(){
		$democonfiginfo = appConfig( 'demo' );
		if( !$this->isPost() ){
			$this->democonfiginfo = $democonfiginfo;
			$this->display();
		}else{
			$data = $_POST;
			$data['APP_SORT'] = intval($_POST['APP_SORT']);
			$app_path = BASE_PATH . 'apps/demo/';
			$filearray = array('demoApi');
			foreach($filearray as $value){
				$file = $app_path . $value . '.php';
				file_put_contents($file, str_replace($democonfiginfo['APP_NAME'], $data['APP_NAME'], file_get_contents($file)) );
			}
			if( save_config( 'demo', $data ) ){
				$this->alert('配置写入成功！', url('index/demolist'));
			}else{
				$this->alert('配置写入失败！');
			}
		}
	}
}