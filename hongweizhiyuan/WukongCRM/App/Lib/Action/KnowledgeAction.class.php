<?PHP 
class KnowledgeAction extends Action{
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array('index')
		);
		B('Authenticate', $action);
	}
	public function announce(){
		if($this->isPost()){
			$title = trim($_POST['title']);
			if ($title == '' || $title == null) {
				alert('error',L('TITLE CAN NOT NULL'),$_SERVER['HTTP_REFERER']);
			}
			$knowledge = M('knowledge');
			if($knowledge->create()){
				$knowledge->update_time = time();
				if($knowledge->save()){
					if($_POST['submit'] == L('SAVE')) {
						alert('success', L('ANNOUNCEMENT_SAVE_SUCCESS'), U('index/index'));
					}
				} else {
					alert('error', L('MODIFY_FAILY_DATA_UNCHANGE'),$_SERVER['HTTP_REFERER']);
				}
			}else{
				alert('error', L('MODIFY_FAILY_PLEASE_CONTACT_ADMINISTRATOR'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($this->isGet()){
			$m_knowledge = M('Knowledge');
			$knowledge =  $m_knowledge->where('knowledge_id = 1')->find();

			$this -> knowledge = $knowledge;
			$this->display();
		}
	}
	public function index(){
		$d_knowledge = D('KnowledgeView'); // 实例化User对象
		import('@.ORG.Page');// 导入分页类
		$where = array();
		$params = array();
		
		$order = "create_time desc";
		if($_GET['desc_order']){
			$order = trim($_GET['desc_order']).' desc';
		}elseif($_GET['asc_order']){
			$order = trim($_GET['asc_order']).' asc';
		}
		
		if ($_REQUEST["field"]) {
			$field = trim($_REQUEST['field']) == 'all' ? 'title|content' : $_REQUEST['field'];
			$search = empty($_REQUEST['search']) ? '' : trim($_REQUEST['search']);
			$condition = empty($_REQUEST['condition']) ? 'is' : trim($_REQUEST['condition']);
			if	('create_time' == $field || 'update_time' == $field) $search = is_numeric($search)?$search:strtotime($search);
			switch ($condition) {
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
			$params = array('field='.$field, 'condition='.$condition, 'search='.trim($_REQUEST["search"]));
		}
		$p = isset($_GET['p'])?$_GET['p']:1;
		if ($_REQUEST['category_id']) {
			$idArray = Array();
			$categoryList = getSubCategory($_GET['category_id'],M('knowledge_category')->select(),'');
			foreach ($categoryList as $value) {
				$idArray[] = $value['category_id'];
			}
			$idList =empty($idArray) ? $_GET['category_id'] : $_GET['category_id'] . ',' . implode(',', $idArray);
			$where['knowledge.category_id'] = array('in',$idList);
			
			if(trim($_GET['act']) == 'excel'){		
				$knowledgeList = $d_knowledge->order($order)->where($where)->select();
				$this->excelExport($knowledgeList);
			}
			
			$count = $d_knowledge->where($where)->count();
			$list = $d_knowledge->order($order)->where($where)->Page($p.',15')->select();
			$params['category_id'] = 'category_id=' . trim($_REQUEST['category_id']);		
		} else {
			
			if(trim($_GET['act']) == 'excel'){		
				if(vali_permission('knowledge', 'export')){
					$knowledgeList = $d_knowledge->order($order)->where($where)->select();
					$this->excelExport($knowledgeList);
				}else{
					alert('error', L('HAVE NOT PRIVILEGES'), $_SERVER['HTTP_REFERER']);
				}
			}
			
			$count = $d_knowledge->count();// 查询满足要求的总记录数
			$list = $d_knowledge->where($where)->order($order)->Page($p.',15')->select();
		} 
		$this->parameter = implode('&', $params);
		if ($_GET['desc_order']) {
			$params[] = "desc_order=" . trim($_GET['desc_order']);
		} elseif($_GET['asc_order']){
			$params[] = "asc_order=" . trim($_GET['asc_order']);
		}		
		$Page = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->parameter = implode('&', $params);
		$userRole = M('userRole');
		foreach($list as $k => $v){
			$list[$k]['owner'] = D('RoleView')->where('role.role_id = %d', $v['role_id'])->find();
		}
		
		$category = M('knowledge_category');
		$category_list = $category->select();
		$this->categoryList = getSubCategory(0, $category_list, '');		
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$Page->show());// 赋值分页输出
		$this->alert=parseAlert();
		$this->display(); // 输出模板
	}
	public function add(){
		if($_POST['submit']){
			$title = trim($_POST['title']);
			if ($title == '' || $title == null) {
				alert('error',L('TITLE CAN NOT NULL'),$_SERVER['HTTP_REFERER']);
			}
			$knowledge = D('Knowledge');
			if($knowledge->create()){
				$knowledge->create_time = time();
				$knowledge->update_time = time();
				$konwledge_id = $knowledge->add();
				if($_POST['submit'] == L('SAVE')) {
					alert('success', L('ARTICLE_ADD_SUCCESS'), U('Knowledge/index'));
					alert('success', L('ARTICLE_SAVE_SUCCESS'), U('knowledge/index','id='.$konwledge_id));
				} else {
					alert('success', L('ADD_SUCCESS'), U('Knowledge/add'));
				}
			}else{
				$this->error($knowledge->getError());
			}

		}else{
			$knowledge_category = M('knowledge_category');
			$category_list = $knowledge_category->select();
			$this->assign('category_list', getSubCategory(0, $category_list, ''));
			$this->alert = parseAlert();
			$this->display();
		}
	}
	public function view(){		
		if($_GET['id']){
			$knowledge = M('Knowledge');
			$knowledge->where('knowledge_id=%d',$_GET['id'])->setInc('hits');
			$knowledge = $knowledge->where('knowledge_id = %d ', $_GET['id'])->find();
			$knowledge['owner'] = D('RoleView')->where('role.role_id = %d', $knowledge['role_id'])->find();
			$m_userRole = M('userRole');
			$knowledge['username']  = $m_userRole->where('role_id = %d',$knowledge['role_id'])->getField('name');
			$this->knowledge = $knowledge;
			$this->alert = parseAlert();
			$this->display();
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	public function edit(){
		if($this->isPost()){
			$title = trim($_POST['title']);
			if ($title == '' || $title == null) {
				alert('error',L('TITLE CAN NOT NULL'),$_SERVER['HTTP_REFERER']);
			}
			$knowledge = M('knowledge');
			if($knowledge->create()){
				$knowledge->update_time = time();
				if($knowledge->save()){
					if($_POST['submit'] == L('SAVE')) {
						alert('success', L('ARTICLE_SAVE_SUCCESS'), U('knowledge/index','id='.$_POST['konwledge_id']));
					} else {
						alert('success', L('SAVE_SUCCESS_CONTINUE_INPUT'), U('knowledge/add'));
					}
				} else {
					alert('error', L('MODIFY_FAILY_DATA_UNCHANGE'),U('knowledge/index'));
				}
			}else{
				alert('error',L('MODIFY_FAILY_PLEASE_CONTACT_ADMINISTRATOR'),U('knowledge/index'));
			}
		}elseif($_GET['id']){
			$m_knowledgeCategory = M('knowledgeCategory');
			$category_list = $m_knowledgeCategory->select();
			$this->assign('category_list', getSubCategory(0, $category_list, ''));
			$m_knowledge = M('Knowledge');
			$this -> knowledge = $m_knowledge->where('knowledge_id = %d',$_GET['id'])->find();
			$this->display();
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	public function delete(){
		$knowledge = M('Knowledge');
		$knowledge_idarray = $_POST['knowledge_id'];
		if (is_array($knowledge_idarray)) {
			if (!session('?admin')) {
				foreach ($knowledge_idarray as $v) {
					if (!$knowledge->where('knowledge_id = %d and role_id = %d', $v, session('role_id'))->find()){
						alert('error', L('DONOT_HAVE_PERMISSIONS_ONLY_AUTHOR_CAN_DELETE'),$_SERVER['HTTP_REFERER']);
					}
				}
			}
			if ($knowledge->where('knowledge_id in ("%s")', join(',', $knowledge_idarray))->delete()) {
				alert('success',L('DELETED SUCCESSFULLY'),U('knowledge/index'));
			} else {
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		} elseif($_GET['id']) {
			if (!session('?admin')) {
				if (!$knowledge->where('knowledge_id = %d and role_id = %d', $_GET['id'], session('role_id'))->find()){
					alert('error', L('DONOT_HAVE_PERMISSIONS_ONLY_AUTHOR_CAN_DELETE'),$_SERVER['HTTP_REFERER']);
				}
			}
			
			if($knowledge->where('knowledge_id = %d', $_GET['id'])->delete()){
				alert('success', L('DELETED SUCCESSFULLY'),U('knowledge/index'));
			}else{
				$this->error(L('DELETE FAILED CONTACT THE ADMINISTRATOR'));
			}
		} else {
			alert('error', L('PLEASE_SELECT_DELETE_ARTICLE'),$_SERVER['HTTP_REFERER']);
		}
	}
	public function category(){
		$knowledge_category = M('knowledge_category');
		$category_list = $knowledge_category->select();
		$category_list = getSubCategory(0, $category_list, '');

		foreach($category_list as $key=>$value){
			$knowledge = M('knowledge');
			$count = $knowledge->where('category_id = %d', $value['category_id'])->count();
			$category_list[$key]['count'] = $count;
			$category_list[$key]['list'] = $knowledge->where('category_id = %d', $value['category_id'])->select();
		}
		$this->alert=parseAlert();
		$this->assign('category_list', $category_list);
		$this->display();
	}
	public function categoryAdd(){
		if (isset($_POST['submit'])) {
			$category = D('KnowledgeCategory');
			if ($t = $category->create()) {
				if ($category->add()) {
					alert('success', L('ADD_SUCCESS'),$_SERVER['HTTP_REFERER']);
				} else {
					alert('error', L('PARAMETER_ERROR_ADD_FAILY'),$_SERVER['HTTP_REFERER']);
				}
			} else {
				exit($category->getError());
			}
		}else{
			$category = M('knowledge_category');
			$category_list = $category->select();
			$this->assign('category_list', getSubCategory(0, $category_list, ''));
			$this->display();
		}
	}
	public function categoryEdit(){
		if($_GET['id']){
			$knowledge_category = M('knowledgeCategory');
			$category_list = $knowledge_category -> select();
			$this->assign('category_list', getSubCategory(0, $category_list, ''));
			$this->knowledge_category =$knowledge_category->where('category_id = ' . $_GET['id'])->find();
			$this->display();
		}elseif($_POST['submit']){
			$knowledge_category = M('knowledgeCategory');
			$knowledge_category -> create();
			if($knowledge_category -> save()){
				alert('success',L('UPDATE_CATEGORY_INFO_SUCCESS'),U('knowledge/category'));
			}else{
				alert('error',L('DATA_UNCHANGE_UPDATE_CATEGORY_INFO_FAILY'),$_SERVER['HTTP_REFERER']);
			}
		}else{
			$this->error(L('PARAMETER_ERROR'));
		}
	}
	public function categoryDelete(){
		$knowledge_category = M('KnowledgeCategory');
		$knowledge = M('knowledge');
		if($_POST['category_list']){
			foreach($_POST['category_list'] as $value){
				if($knowledge->where('category_id = %d',$value)->select()){
					$name = $knowledge_category->where('category_id = %d',$value)->getField('name');
					alert('error', L('DELETE_FAILED_REMOVE_THIS_KNOWLEDGE',array($name)),$_SERVER['HTTP_REFERER']);
				}
				if($knowledge_category->where('parent_id = %d',$value)->select()){
					$name = $knowledge_category->where('category_id = %d',$value)->getField('name');
					alert('error', L('DELETE_FAILED_REMOVE_THIS_CATEGORY',$name),$_SERVER['HTTP_REFERER']);
				}
			}
			if($knowledge_category->where('category_id in (%s)', join($_POST['category_list'],','))->delete()){
				alert('success', L('DELETE_CATEGORY_SUCCESS'),$_SERVER['HTTP_REFERER']);
			}else{
				alert('error', L('DELETE_CATEGORY_FAILY'),$_SERVER['HTTP_REFERER']);
			}
		}elseif($_GET['id']){
			if($knowledge->where('category_id = %d',$_GET['id'])->select()){
				$this->error(L('DELETE_FAILED_REMOVE_KNOWLEDGE'));
				alert('error', L('PARAMETER_ERROR_ADD_FAILY'),$_SERVER['HTTP_REFERER']);	
			}
			if($knowledge->where('parent_id = %d',$_GET['id'])){
				alert('error', L('PLEASE_REMOVE_THIS_CATEGORY'),$_SERVER['HTTP_REFERER']);	
			}else{
				$this->error(L('PARAMETER_ERROR'));
			}
		}else{
			$this->error(L('DELETE_FAILY'));
		}	
	}
	public function excelExport($knowledgeList=false){
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();    
		$objProps = $objPHPExcel->getProperties();    
		$objProps->setCreator("5kcrm");    
		$objProps->setLastModifiedBy("5kcrm");    
		$objProps->setTitle("5kcrm Konwledge");    
		$objProps->setSubject("5kcrm Konwledge Data");    
		$objProps->setDescription("5kcrm Konwledge Data");    
		$objProps->setKeywords("5kcrm Konwledge");    
		$objProps->setCategory("5kcrm");
		$objPHPExcel->setActiveSheetIndex(0);     
		$objActSheet = $objPHPExcel->getActiveSheet(); 
		   
		$objActSheet->setTitle('Sheet1');
		$objActSheet->setCellValue('A1', L('TITLE'));
		$objActSheet->setCellValue('B1', L('CATEGORY'));
		$objActSheet->setCellValue('C1', L('CONTENT'));
		$objActSheet->setCellValue('D1', L('CLICK_NUM'));
		$objActSheet->setCellValue('E1', L('CREATOR_ROLE'));
		$objActSheet->setCellValue('F1', L('CREATOR_TIME'));
		
		if(is_array($knowledgeList)){
			$list = $knowledgeList;
		}else{
			$list = D('KnowledgeView')->select();
		}
		
		$i = 1;
		foreach ($list as $k => $v) {
			$i++;
			$creator = D('RoleView')->where('role.role_id = %d', $v['role_id'])->find();
			$objActSheet->setCellValue('A'.$i , $v['title']);
			$objActSheet->setCellValue('B'.$i, $v['name']);
			$objActSheet->setCellValue('C'.$i, $v['content']);
			$objActSheet->setCellValue('D'.$i, $v['hits']);
			$objActSheet->setCellValue('E'.$i, $creator['user_name'].'['.$creator['department_name'] . '-' . $creator['role_name'] .']');
			$objActSheet->setCellValue('F'.$i, date("Y-m-d H:i:s", $v['create_time']));
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=5kcrm_knowledge_".date('Y-m-d',mktime()).".xls");
        header("Pragma:no-cache");
        header("Expires:0");
        $objWriter->save('php://output'); 
	}
	public function excelImport(){
		C('TOKEN_ON',false);
		$m_knowledge = M('knowledge');
		if($_POST['submit']){
			if (isset($_FILES['excel']['size']) && $_FILES['excel']['size'] != null) {
				import('@.ORG.UploadFile');
				$upload = new UploadFile();
				$upload->maxSize = 20000000;
				$upload->allowExts  = array('xls');
				$dirname = UPLOAD_PATH . date('Ym', time()).'/'.date('d', time()).'/';
				if (!is_dir($dirname) && !mkdir($dirname, 0777, true)) {
					alert('error', L('ATTACHMENTS TO UPLOAD DIRECTORY CANNOT WRITE'), U('knowledge/index'));
				}
				$upload->savePath = $dirname;
				if(!$upload->upload()) {
					alert('error', $upload->getErrorMsg(), U('knowledge/index'));
				}else{
					$info =  $upload->getUploadFileInfo();
				}
			}
			if(is_array($info[0]) && !empty($info[0])){
				$savePath = $dirname . $info[0]['savename'];
			}else{
				alert('error', L('UPLOAD FAILED'), U('knowledge/index'));
			}
			import("ORG.PHPExcel.PHPExcel");
			$PHPExcel = new PHPExcel();
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($savePath)){
				$PHPReader = new PHPExcel_Reader_Excel5();
			}
			$PHPExcel = $PHPReader->load($savePath);
			$currentSheet = $PHPExcel->getSheet(0);
			$allRow = $currentSheet->getHighestRow();
			if ($allRow <= 1) {
				alert('error', L('UPLOAD A FILE WITHOUT A VALID DATA'), U('knowledge/index'));
			} else {
				for($currentRow = 3;$currentRow <= $allRow;$currentRow++){
					$data = array();
					$data['category_id'] = intval($_POST['category_id']);
					$data['role_id'] = session('role_id');
					$data['create_time'] = time();
					$data['update_time'] = time();
					$title = (string)$currentSheet->getCell('A'.$currentRow)->getValue();
					if($title != '' && $title != null) $data['title'] = $title;
					
					$category = (String)$currentSheet->getCell('B'.$currentRow)->getValue();
					$category_id = M('KnowledgeCategory')->where('name = "%s"' ,trim($category))->getField('category_id');
					if($category){
						if($category_id > 0){
							$data['category_id'] = $category_id;
						} else {
							if($this->_post('error_handing','intval',0) == 0){
								alert('error', L('IMPORT_FAILY_SOURCE_NOT_EXIST',array($currentRow,$category)), U('knowledge/index'));
							}else{
								$error_message .= L('FAILY_SOURCE_NOT_EXIST',array($currentRow,$category));
							}
							break;
						}
					}
					
					$content = (string)$currentSheet->getCell('C'.$currentRow)->getValue();
					if($content != '' && $content != null) $data['content'] = $content;
					if (!$m_knowledge->add($data)) {
						if($this->_post('error_handing','intval',0) == 0){
							alert('error', L('IMPORT_FAILY_SOURCE',array($currentRow)), U('knowledge/index'));
						}else{
							$error_message .= L('FAILY_SOURCE',array($currentRow,$m_knowledge->getError()));
							$m_knowledge->clearError();
						}
						
						break;
					}
					
				}
				alert('success', $error_message .L('IMPORT SUCCESS'), U('knowledge/index'));
			}
		}else{
			$this->category_list = getSubCategory(0, M('KnowledgeCategory')->select(), '');
			$this->display();
		}
	}
}
