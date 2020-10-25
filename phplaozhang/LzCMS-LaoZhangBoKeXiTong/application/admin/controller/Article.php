<?php
namespace app\admin\controller;

/**
* 文章控制器
*/
class Article extends Init
{
	
	function _initialize()
	{
		parent::_initialize();
		$this->model = model('common/article');
		$this->category_model = model('common/category');
	}

	function index(){
		$params = input('param.');
		$category_id = $params['category_id'];
		$page_size = $params['page_size'];
		$map = array('category_id'=>$category_id);
		$order = array('id'=>'desc');
		if($params['search'] && is_array($params['search'])){
			foreach ($params['search'] as $k => $v) {
				if($v){
					$map[$k] = array('like','%'.$v.'%'); 
				}
			}
		}
		if($params['order'] && is_array($params['order'])){
			$sort = end($params['order']) ? each($params['order']) : each($params['order']);
			$order[$sort['key']] = $sort['value'];
			$order = array($sort['key']=>$sort['value']);
		}
		$url_params = parse_url(request()->url(true))['query'];
		$articles = $this->model->get_list( $map,$order,$page_size ,2);
		return view('list',['category_id'=>$category_id,'articles'=>$articles['data'],'total'=>$articles['total'],'per_page'=>$articles['per_page'],'current_page'=>$articles['current_page'],'search'=>$params['search'],'order'=>$order,'url_params'=>$url_params]);
	}

	function add(){
		if(request()->isPost()){
			$params = input('post.');
			if(empty($params['description'])){
				$params['description'] = mb_substr(strip_tags($params['content']), 0,250,'utf-8');
			}
			if(!trim($params['url'])){
				unset($params['url']);
			}
			$result = $this->model->add($params);
			if($result){
				if(!trim($params['url'])){
					$this->model->edit(array('url'=>url('index/article/show',['id'=>$this->model->id]),'id'=>$this->model->id));
				}
				return json(array('code'=>200,'msg'=>'添加成功'));
			}else{
				return json(array('code'=>0,'msg'=>'添加失败'));
			}
		}
		$category_id = input('param.category_id');
		$model_category_select_option = $this->category_model->get_model_category_select_no_option($category_id);
		return view('add',['model_category_select_option'=>$model_category_select_option]);
	}

	function edit(){
		if(request()->isPost()){
			$params = input('post.');
			if(empty($params['description'])){
				$params['description'] = mb_substr(strip_tags($params['content']), 0,250,'utf-8');
			}
			if(!trim($params['url'])){
				$params['url'] = url('index/article/show',['id'=>$params['id']]);
			}
			$result = $this->model->edit($params);
			if($result){
				return json(array('code'=>200,'msg'=>'修改成功'));
			}else{
				return json(array('code'=>0,'msg'=>'修改失败'));
			}
		}
		$article = $this->model->where('id',input('param.id'))->find();
		$model_category_select_option = $this->category_model->get_model_category_select_no_option($article['category_id']);
		return view('edit',array('article'=>$article->toArray(),'model_category_select_option'=>$model_category_select_option));
	}

	function del(){
		$result = $this->model->destroy(input('post.id'));
		if($result){
			return json(array('code'=>200,'msg'=>'删除成功'));
		}else{
			return json(array('code'=>0,'msg'=>'删除失败'));
		}
	}

	//批量删除
	function batches_delete(){
		$params = input('post.');
		$ids = implode(',', $params['ids']);
		$result = $this->model->batches('delete',$ids);
		if($result){
			return json(array('code'=>200,'msg'=>'批量删除成功'));
		}else{
			return json(array('code'=>0,'msg'=>'批量删除失败'));
		}
	} 

	//批量移动
	function batches_move(){
		$params = input('post.');
		$params['ids'] = implode(',', $params['ids']);
		$result = $this->model->batches('move',$params);
		if($result){
			return json(array('code'=>200,'msg'=>'批量移动成功'));
		}else{
			return json(array('code'=>0,'msg'=>'批量移动失败'));
		}
	} 

	//置顶
	function to_top(){ 
		$id = input('post.id');
		$data['id'] = $id;
		$data['is_top'] = array('exp','1-is_top');
		$result = $this->model->edit($data);
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}
	//推荐
	function to_recommend(){ 
		$id = input('post.id');
		$data['id'] = $id;
		$data['is_recommend'] = array('exp','1-is_recommend');
		$result = $this->model->edit($data);
		if($result){
			return json(array('code'=>200,'msg'=>'操作成功'));
		}else{
			return json(array('code'=>0,'msg'=>'操作失败'));
		}
	}

	//导出excel
	function export(){
		$category_id = input('param.category_id');
		$category = cache('categorys')[$category_id];
		$articles = $this->model->get_list(['category_id'=>$category_id],'id desc',0,0);
		$fileName = $category['name'].'-文章列表'.date('Y-m-d',time());
		import('PHPExcel.PHPExcel', EXTEND_PATH);
		$PHPExcel = new \PHPExcel();
        $PHPExcel->getActiveSheet()->setTitle($fileName);
        $PHPExcel->setActiveSheetIndex(0);

        //填入表头
        $PHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
        $PHPExcel->getActiveSheet()->setCellValue('B1', '栏目ID');
        $PHPExcel->getActiveSheet()->setCellValue('C1', '标题');
        $PHPExcel->getActiveSheet()->setCellValue('D1', '关键词');
        $PHPExcel->getActiveSheet()->setCellValue('E1', '描述');
        $PHPExcel->getActiveSheet()->setCellValue('F1', '图片地址');
        $PHPExcel->getActiveSheet()->setCellValue('G1', '内容');
        $PHPExcel->getActiveSheet()->setCellValue('H1', '创建时间');
        $PHPExcel->getActiveSheet()->setCellValue('I1', '修改时间');
        $PHPExcel->getActiveSheet()->setCellValue('J1', '点击量');
        $PHPExcel->getActiveSheet()->setCellValue('K1', '是否推荐');
        $PHPExcel->getActiveSheet()->setCellValue('L1', '是否置顶');
        $PHPExcel->getActiveSheet()->setCellValue('M1', '链接');

        foreach ($articles as $k => $v) {
        	$PHPExcel->getActiveSheet()->setCellValue('A'.($k+2), $v['id']);
        	$PHPExcel->getActiveSheet()->setCellValue('B'.($k+2), $v['category_id']);
        	$PHPExcel->getActiveSheet()->setCellValue('C'.($k+2), $v['title']);
        	$PHPExcel->getActiveSheet()->setCellValue('D'.($k+2), $v['keywords']);
        	$PHPExcel->getActiveSheet()->setCellValue('E'.($k+2), $v['description']);
        	$PHPExcel->getActiveSheet()->setCellValue('F'.($k+2), $v['image_url']);
        	$PHPExcel->getActiveSheet()->setCellValue('G'.($k+2), $v['content']);
        	$PHPExcel->getActiveSheet()->setCellValue('H'.($k+2), $v['create_time']);
        	$PHPExcel->getActiveSheet()->setCellValue('I'.($k+2), $v['update_time']);
        	$PHPExcel->getActiveSheet()->setCellValue('J'.($k+2), $v['hits']);
        	$PHPExcel->getActiveSheet()->setCellValue('k'.($k+2), $v['is_recommend']);
        	$PHPExcel->getActiveSheet()->setCellValue('L'.($k+2), $v['is_top']);
        	$PHPExcel->getActiveSheet()->setCellValue('M'.($k+2), $v['url']);
        }
        $objWriter = new \PHPExcel_Writer_Excel5($PHPExcel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        //多浏览器下兼容中文标题
        $encoded_filename = urlencode($fileName);
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '.xls"');
        } else {
            header('Content-Disposition: attachment; filename="' . $fileName . '.xls"');
        }
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');
	}

	//导入excel
	function import(){
		$params = input('post.');
		$filePath = ROOT_PATH.$params['path'];
		import('PHPExcel.PHPExcel', EXTEND_PATH);
		$PHPReader = new \PHPExcel_Reader_Excel2007();
	    if (!$PHPReader->canRead($filePath)) {
	      $PHPReader = new \PHPExcel_Reader_Excel5();
	      if (!$PHPReader->canRead($filePath)) {
	        return json(array('code'=>0,'msg'=>'格式无法识别'));
	      }
	    }
	    $PHPExcel = $PHPReader->load($filePath);
	    $currentSheet = $PHPExcel->getSheet(0); /*     * 读取excel文件中的第一个工作表 */
		$allRow = $currentSheet->getHighestRow(); /*     * 取得一共有多少行 */
		if ($allRow <= 1) {
	      return json(array('code'=>0,'msg'=>'导入数据不存在'));
	    };

	    $import_data = array();
	    for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
	    	if($currentSheet->getCell('C' . $currentRow)->getValue()){
	    		$import_data[$currentRow] = array(
	    		//'id'             => $currentSheet->getCell('A' . $currentRow)->getValue(),
	    		'category_id'    => $params['category_id'],
	    		'title'     	 => $currentSheet->getCell('C' . $currentRow)->getValue(),
	    		'keywords'     	 => $currentSheet->getCell('D' . $currentRow)->getValue(),
	    		'description'    => $currentSheet->getCell('E' . $currentRow)->getValue(),
	    		'image_url'      => $currentSheet->getCell('F' . $currentRow)->getValue(),
	    		'content'     	 => $currentSheet->getCell('G' . $currentRow)->getValue(),
	    		'create_time'    => $currentSheet->getCell('H' . $currentRow)->getValue(),
	    		'update_time'    => $currentSheet->getCell('I' . $currentRow)->getValue(),
	    		'hits'     	     => $currentSheet->getCell('J' . $currentRow)->getValue(),
	    		'is_recommend'   => $currentSheet->getCell('K' . $currentRow)->getValue(),
	    		'is_top'     	 => $currentSheet->getCell('L' . $currentRow)->getValue(),
	    		'url'     	     => $currentSheet->getCell('M' . $currentRow)->getValue(),
	    		);
	    	}
	    	
	    }
	    @unlink($filePath);
	    $result = $this->model->saveAll($import_data);
		if($result){
			@file_get_contents(url("category/update_content_links"));
			return json(array('code'=>200,'msg'=>'导入成功'));
		}else{
			return json(array('code'=>0,'msg'=>'导入失败'));
		}
	}

}