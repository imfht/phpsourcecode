<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;

/**
 * 文章管理
 * @author Lain
 *
 */
class ArticleController extends AdminController {
	private $categorys;
	//初始化
	public function _initialize(){
		$action = array(
				//'permission'=>array('changePassword'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
		
		//获取栏目信息
		if(!$this->categorys = F('category_content')){
			D('Category')->file_cache();
			$this->categorys = F('category_content');
		}
        $this->db = D('Content');
	}
    public function index(){
    	//取出文章分类
    	//$this->categoryList = list_to_tree($this->categorys,'catid','parentid');
    	foreach ($this->categorys as $key => $category){
    		$data[$key] = $category;
    		$data[$key]['name'] = $category['catname'];
    		if($category['type'] == 0){	//内部栏目, 显示列表
    			$data[$key]['url'] = U('Article/manage?catid='.$category['catid']);
    		}else{		//单网页, 显示编辑页
    			$data[$key]['url'] = U('Article/pageEdit?catid='.$category['catid']);
    			$data[$key]['icon'] = 'Public/images/page_edit.png';
    		}
    	}
    	$nodes = list_to_tree($data, 'catid', 'parentid', 'children');
    	$this->assign('json_nodes', json_encode($nodes));
    	
    	$this->display();
    }
    
    //文章内容管理
    public function manage(){
    	$categorys = $this->categorys;
		//取出所在分类
		$this->catid = $catid = I('get.catid','','intval');
		if(!$catid)
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'缺少必要的参数'));
        $modelid = $categorys[$catid]['modelid'];
        $this->db->set_model($modelid);
		// 检索条件
		$map['status'] = 99;
		//取出子集下的文章
		$map['catid'] = array('in', $categorys[$catid]['arrchildid']);
		$map['_string'] = 1;
			
   	 	if(isset($_POST['start_time']) && $_POST['start_time']) {
			$this->start_time = $_POST['start_time'];
			$start_time = strtotime($_POST['start_time']);
			$map['_string'] .= " AND `inputtime` > '$start_time'";
		}
		if(isset($_POST['end_time']) && $_POST['end_time']) {
			$this->end_time = $_POST['end_time'];
			$end_time = strtotime($_POST['end_time']) + 3600*24;
			$map['_string'] .= " AND `inputtime` < '$end_time'";
		}

		if(I('post.keyword')) {
			
			$type_array = array('title','description','username');
			$this->keyword = $keyword = I('post.keyword');
			$this->searchtype = $searchtype = I('post.searchtype');
			if($searchtype < 3) {
				$searchtype = $type_array[$searchtype];
				$map[$searchtype] = array('like', "%$keyword%");
			} elseif($searchtype == 3) {
				$keyword = intval($_POST['keyword']);
			}
		}
		//排序
		if(I('post.orderField')){
			$this->orderField = $orderField = I('post.orderField');
			$this->orderDirection = $orderDirection = I('post.orderDirection') ? I('post.orderDirection') : 'asc';
			$order = $orderField . ' ' . $orderDirection;
		}else{
			$order = 'id desc';
		}
		
		// 分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		// var_dump($this->db->tableName);exit;
		$totalCount = $this->db->where($map)->count();
		$page ['totalCount'] = $totalCount;

		// 取数据
		$page_list = $this->db->where($map)->page($page['pageCurrent'], $page['pageSize'])->order($order)->select();
		$this->assign('page_list', $page_list);
		$this->assign('page', $page);
		$this->assign('categorys', $categorys);
		$this->display ();
	}

	public function add(){
		if(IS_POST){
			$info = I('post.info');
            $catid = intval($info['catid']);
            $category = $this->categorys[$catid];
            if($category['type']==0) {
                $modelid = $this->categorys[$catid]['modelid'];
                $this->db->set_model($modelid);
                $info['status'] = 99;
                $result = $this->db->add_content($info);
            }
			// $info['content'] = trim_script(addslashes($info['content']));
			// //后台发布不用审核
			// $info['status'] = 99;
			// $info['catid'] = $catid;
			
			// //验证规则
			// $DB = D('Content');
			// if(!$DB->create($info)){
			// 	//如果不通过 ，输出错误报告
			// 	$this->ajaxReturn(array('statusCode'=>300,'message'=>$DB->getError()));
			// }else{
			// 	$result = $DB->add_content($info);
			// }
			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'divid'=>'layout_article','message'=>'保存成功'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败ERROR:003'));
			}
		}else{
            $catid = I('get.catid','','intval');
            $category = $this->categorys[$catid];
            if($category['type'] == 0){
                $modelid = $category['modelid'];
                //获取表单信息
                $content_form = new \Lain\Phpcms\content_form($modelid,$catid,$this->categorys);
                $forminfos = $content_form->get();
            }
			$this->assign('catid', $catid);
			$this->assign('categorys',$this->categorys);
            $this->assign('forminfos', $forminfos);
			$this->display('edit');
		}
	}
	public function edit(){
		//取出该文章信息
		// $detail = D('Content')->getDetail($id);
		
		if(IS_POST){
            $id = I('post.id');
			$info = I('post.info');
            $catid = intval($info['catid']);
            if(!$catid){
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
            }
            $modelid = $this->categorys[$catid]['modelid'];
            $this->db->set_model($modelid);
            $result = $this->db->edit_content($info, $id);

			if($result){
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','divid'=>'layout_article'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败ERROR:003'));
			}
		}else{
            $id = I('get.id','','intval');
            $catid = I('get.catid','','intval');
            if(!$catid){
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
            }
            $category = $this->categorys[$catid];
            $modelid = $category['modelid'];
            $this->db->set_model($modelid);
            $data = $this->db->getDetail($id);
            if(!$data){
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'文章不存在'));
            }
            $content_form = new \Lain\Phpcms\content_form($modelid,$catid,$this->categorys);
            $forminfos = $content_form->get($data);
            $this->assign('id', $id);
			$this->assign('catid', $catid);
			$this->assign('forminfos', $forminfos);
			$this->assign('categorys',$this->categorys);
			$this->display();
		}
	}
	//批量删除文章
	public function delete(){
        $catid = I('get.catid', 'intval');
        if(!$catid){
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        }
        $modelid = $this->categorys[$catid]['modelid'];
        $this->db->set_model($modelid);
		$ids = I('get.ids');  //获取ids字符串  '1130,1127'
		if(!$ids)
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'请选择要删除的文章'));
		$idsList = explode(',', $ids);
		//循环删除文章
		foreach ($idsList as $id){
			//删除内容
			$this->db->delete_content($id);
			//其他相关操作
		}
		$this->ajaxReturn(array('statusCode'=>200,'message'=>'删除成功','divid'=>'layout_article'));
		
	}
    //文章分类列表
    public function category(){
        $models = D('Model')->getAllModels();
    	$tree = new \Lain\Phpcms\tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	$models = D('Model')->getAllModels();
    	$result = $this->categorys;
    	if(!empty($result)){
    		foreach ($result as $r){
    			$categoryList[$r['catid']] = $r;
                $categoryList[$r['catid']]['typename'] = $r['type'] == 1 ? L('category_type_page') : L('category_type_system');
                $categoryList[$r['catid']]['model_name'] = $r['modelid'] ? $models[$r['modelid']]['name'] : '';
    			$categoryList[$r['catid']]['str_manage'] = '<a class="btn btn-green" href="'.U('Article/categoryAdd?parentid='.$r['catid']).'" data-toggle="dialog" data-width="520" data-height="320" data-id="dialog-mask" data-mask="true">'.L('add_sub_category').'</a> <a class="btn btn-green" href="'.U('Article/categoryEdit?catid='.$r['catid']).'" data-toggle="dialog" data-width="520" data-height="320" data-id="dialog-mask" data-mask="true">'.L('edit').'</a> <a href="'.U('Article/categoryDelete?catid='.$r['catid']).'" class="btn btn-red" data-toggle="doajax" data-confirm-msg="确定要删除该栏目吗？">'.L('delete').'</a> ';
    		}
    	}
    	$str  = "<tr target='rid' rel='\$catid'>
    				<td>\$catid</td>
    				<td>\$spacer\$catname</td>
                    <td>\$typename</td>
                    <td>\$model_name</td>
                    <td>\$items</td>
    				<td>\$listorder</td>
    				<td align='center'>\$str_manage</td>
				</tr>";
    	$tree->init($categoryList);
    	$this->categoryList = $tree->get_tree(0, $str);
    	
    	/* foreach ( $this->categorys as $key => $category ) {
			$data [$key] = $category;
			$data [$key] ['name'] = $category ['catname'];
			$data [$key] ['name'] = $category ['catname'];
			// 如果有子分类的父级, 去掉url
			if ($category ['parentid'] == 0 && $category ['child'] != 0) {
				unset ( $data [$key] ['url'] );
			}
		}
		$nodes = list_to_tree ( $data, 'catid', 'parentid', 'children' );
		$this->assign ( 'json_nodes', json_encode ( $nodes ) ); */
    	
    	$this->display();
    }
    //更新栏目缓存 
    public function categoryCache(){
    	D('Category')->public_cache();
    	$this->ajaxReturn(array('statusCode'=>200,'message'=>'更新缓存成功','tabid'=>'Article_category'));
    }

    /**
     * 栏目添加
     * @DateTime 2019-04-04
     */
    public function categoryAdd(){
        $parentid = I('get.parentid') ? I('get.parentid') : 0;
    	if(IS_POST){
    		$DB = D('Category');
    		$info = I('post.info');
    		$setting = I('post.setting');
    		$info['setting'] = serialize($setting);
    		if(!$DB->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>$DB->getError()));
    		}else{
    			$catid = $DB->add($info);
    			//如果是单网页, 则需要添加到page表
                if($info['type'] == 1){
                    $data['title'] = $info['catname'];
                    $data['catid'] = $catid;
                    D('Page')->add($data);
                }
    		}
    		if($catid){
    			//更新缓存
    			$DB->public_cache();
    			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Article_category'));
    		}else{
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
    		}
    	}else{
            $type = I('get.type');
            $this->assign('parentid', $parentid);

            $template_list = template_list($this->siteid, 0);
            foreach ($template_list as $k=>$v) {
                $template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($template_list[$k]);
            }
            $this->assign('template_list', $template_list);

            if($type == 0){
                //取出模型列表
                $models = D('Model')->getAllModels();
                $model_datas = array2select($models, 'modelid');

                $this->assign('model_datas', $model_datas);
                $this->display('categoryEdit');
            }elseif($type == 1){
                $this->display('categoryPageEdit');
            }
            
    	    
            // $this->assign('parentid', $parentid);
    		
    	}
    }
    /*
     * 栏目分类编辑
    */
    public function categoryEdit(){

    	$DB = D('Category');
    	if(IS_POST){
    		$catid = I('post.catid','','intval');
    		$info = I('post.info');
    		$setting = I('post.setting');
    		$info['setting'] = serialize($setting);
    		if(!$DB->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>$DB->getError()));
    		}else{
    			$result = $DB->where('catid='.$catid)->save($info);
    		}
    		if($result){
    			//更新缓存
    			$DB->public_cache();
    			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Article_category'));
    		}else{
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
    		}
    	}else{
    		$this->catid = $catid = I('get.catid','','intval');
    		$detail = $DB->where('catid='.$catid)->find();

    		$setting = unserialize($detail['setting']);
    		
            $template_list = template_list($this->siteid, 0);
            foreach ($template_list as $k=>$v) {
                $template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($template_list[$k]);
            }
            $this->assign('template_list', $template_list);

            //取出模型列表
            $models = D('Model')->getAllModels();
            $model_datas = array2select($models, 'modelid');

            $this->assign('model_datas', $model_datas);
    		$this->assign('setting', $setting);
            $this->assign('Detail', $detail);
    		$this->assign('parentid', $detail['parentid']);
            if($detail['type'] == 1){
                $this->display('categoryPageEdit');
            }else{
                $this->display();
            }
    		
    	}
    }

    /*
     * 删除栏目分类
    */
    public function categoryDelete(){
        $DB = D('Category');
        $catid = I('get.catid','','intval');
        if (!$catid)
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        //判断栏目是否有文章
        //删除子栏目
        $delete_catids = $DB->delete_child($catid);
        //删除栏目
        $DB->where('catid='.$catid)->delete();
        //更新缓存
        $DB->public_cache();
        $this->ajaxReturn(array('statusCode'=>200,'message'=>'删除成功','tabid'=>'Article_category'));
    }

    /**
     * 获取
     * @DateTime 2019-04-04
     */
    public function ajax_public_tpl_file_list(){
        $style = isset($_GET['style']) && trim($_GET['style']) ? trim($_GET['style']) : exit(0);
        $catid = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : 0;
        $batch_str = isset($_GET['batch_str']) ? '['.$catid.']' : '';
        if ($catid) {
            $cat = getcache('category_content_'.$this->siteid,'commons');
            $cat = $cat[$catid];
            $cat['setting'] = string2array($cat['setting']);
        }
        pc_base::load_sys_class('form','',0);
        if($_GET['type']==1) {
            $html = array('page_template'=>form::select_template($style, 'content',(isset($cat['setting']['page_template']) && !empty($cat['setting']['page_template']) ? $cat['setting']['page_template'] : 'category'),'name="setting'.$batch_str.'[page_template]"','page'));
        } else {
            $html = array('category_template'=> form::select_template($style, 'content',(isset($cat['setting']['category_template']) && !empty($cat['setting']['category_template']) ? $cat['setting']['category_template'] : 'category'),'name="setting'.$batch_str.'[category_template]"','category'), 
                'list_template'=>form::select_template($style, 'content',(isset($cat['setting']['list_template']) && !empty($cat['setting']['list_template']) ? $cat['setting']['list_template'] : 'list'),'name="setting'.$batch_str.'[list_template]"','list'),
                'show_template'=>form::select_template($style, 'content',(isset($cat['setting']['show_template']) && !empty($cat['setting']['show_template']) ? $cat['setting']['show_template'] : 'show'),'name="setting'.$batch_str.'[show_template]"','show')
            );
        }
        if ($_GET['module']) {
            unset($html);
            if ($_GET['templates']) {
                $templates = explode('|', $_GET['templates']);
                if ($_GET['id']) $id = explode('|', $_GET['id']);
                if (is_array($templates)) {
                    foreach ($templates as $k => $tem) {
                        $t = $tem.'_template';
                        if ($id[$k]=='') $id[$k] = $tem;
                        $html[$t] = form::select_template($style, $_GET['module'], $id[$k], 'name="'.$_GET['name'].'['.$t.']" id="'.$t.'"', $tem);
                    }
                }
            }
            
        }
        if (CHARSET == 'gbk') {
            $html = array_iconv($html, 'gbk', 'utf-8');
        }
        echo json_encode($html);
    }
    
    //模型管理
    public function model(){
        $map['type'] = 0;
        $page_list = D('Model')->where($map)->select();
        if($page_list){
            //获取模型文章数量
            $categorys = $this->categorys;
            foreach ($page_list as $key => $value) {
                $items = 0;
                foreach ($categorys as $catid => $cat) {
                    if(intval($cat['modelid']) == intval($value['modelid'])){
                        $items += $cat['items'];
                    }
                }
                $page_list[$key]['items'] = $items;
            }
        }
        

        $this->assign('page_list', $page_list);
        $this->display();
    }
    //模型添加
    public function modelAdd(){
        if(IS_POST){
            $info = I('post.info');
            $result = D('Model')->addModel($info);
            if($result){
                $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Article_model'));
            }else{
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
            }
        }else{
            $this->display('modelEdit');
        }
        
    }
    //模型编辑
    public function modelEdit(){
        $DB = D('Model');
        $this->modelid = $modelid = I('get.modelid','','intval');
        $detail = $DB->where('modelid='.$modelid)->find();

        $this->assign('Detail', $detail);
        $this->display();
    }
    //模型删除
    public function modelDelete(){
        $modelid = I('get.modelid', 0, 'intval');
        if(!$modelid){
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        }
        $result = D('Model')->deleteModel($modelid);
        if($result){
            $this->ajaxReturn(array('statusCode'=>200,'message'=>'保存成功'));
        }else{
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
        }
    }

    public function ajax_checkModelTableName(){
        if(IS_GET){
            $info = I('get.info');
            $tablename = $info['tablename'];
            $exist_table = D('Model')->tableExists($tablename);
            if($exist_table){
                echo '{"error":"模型表键名已存在"}';
            }else {
                echo '{"ok":""}';
            }
            exit;
        }
    }

    //模型字段管理
    public function modelField(){
        $modelid = I('get.modelid','','intval');
        $map['modelid'] = $modelid;
        $page_list = D('ModelField')->where($map)->order('listorder, fieldid')->select();

        $this->assign('forbid_fields', D('ModelField')->forbid_fields);
        $this->assign('forbid_delete', D('ModelField')->forbid_delete);
        $this->assign('page_list', $page_list);
        $this->assign('modelid', $modelid);
        $this->display();
    }
    //添加字段
    public function modelFieldAdd(){
        if(IS_POST){
            $info = I('post.info');
            $modelid = $info['modelid'] = intval($info['modelid']);
            //取出该模型表名
            $model_table = D('model')->getModelTablename($modelid);
            $tablename = $info['issystem'] ? C('DB_PREFIX').$model_table : C('DB_PREFIX').$model_table.'_data';
            $field = $info['field'];
            $minlength = $info['minlength'] ? $info['minlength'] : 0;
            $maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
            $field_type = $info['formtype'];
            require APP_PATH.'Admin/Conf/fields/'.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
            if(isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }

            //修改表字段
            require APP_PATH.'Admin/Conf/fields/add.sql.php';
            //附加属性值
            $info['setting'] = array2string($_POST['setting']);
            $info['siteid'] = $this->siteid;
            $info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
            $info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
            //添加model_field数据
            M('model_field')->add($info);
            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Article_modelField'));
        }else{
            require APP_PATH.'Admin/Conf/fields.inc.php';
            //取出已经存在在字段
            $modelid = I('get.modelid');
            $f_datas = M('model_field')->where(array('modelid' => $modelid))->field('field,name')->select();
            foreach($f_datas as $_k=>$_v) {
                $exists_field[] = $_v['field'];
            }

            $all_field = array();
            foreach($fields as $_k=>$_v) {
                if(in_array($_k,$not_allow_fields) || in_array($_k,$exists_field) && in_array($_k,$unique_fields)) continue;
                $all_field[$_k] = $_v;
            }

            $this->assign('fields', $all_field);
            $this->assign('modelid', $modelid);
            $this->display('modelFieldAdd');
        }
        
    }
    //字段修改
    public function modelFieldEdit(){
        if(IS_POST){
            $info = I('post.info');
            $modelid = $info['modelid'] = intval($info['modelid']);
            $model_table = D('model')->getModelTablename($modelid);

            $tablename = I('post.issystem') ? C('DB_PREFIX').$model_table : C('DB_PREFIX').$model_table.'_data';

            $field = $info['field'];
            $minlength = $info['minlength'] ? $info['minlength'] : 0;
            $maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
            $field_type = $info['formtype'];

            require APP_PATH.'Admin/Conf/fields/'.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';

            if(isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }
            $oldfield = $_POST['oldfield'];
            require APP_PATH.'Admin/Conf/fields/edit.sql.php';
            //附加属性值
            $info['setting'] = array2string($_POST['setting']);
            $fieldid = intval($_POST['fieldid']);

            $info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
            $info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
            M('model_field')->where(['fieldid' => $fieldid])->save($info);
            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Article_modelField'));
        }else{
            $fieldid = I('get.fieldid', 0, 'intval');
            $map['fieldid'] = $fieldid;
            $detail = D('ModelField')->where($map)->find();
            require APP_PATH.'Admin/Conf/fields.inc.php';
            //取出已经存在在字段
            $modelid = $detail['modelid'];
            $f_datas = M('model_field')->where(array('modelid' => $modelid))->field('field,name')->select();
            foreach($f_datas as $_k=>$_v) {
                $exists_field[] = $_v['field'];
            }

            $all_field = array();
            foreach($fields as $_k=>$_v) {
                if(in_array($_k,$not_allow_fields) || in_array($_k,$exists_field) && in_array($_k,$unique_fields)) continue;
                $all_field[$_k] = $_v;
            }

            $this->assign('fields', $all_field);
            $this->assign('modelid', $modelid);
            $this->assign($detail);
            $this->display();
        }

    }
    /**
     * 模型字段删除
     * @DateTime 2019-04-04
     */
    public function modelFieldDelete(){
        $fieldid = I('get.fieldid', 0, 'intval');
        $map['fieldid'] = $fieldid;
        $detail = D('ModelField')->where($map)->find();
        D('ModelField')->where($map)->delete();

        $model_cache = D('Model')->getAllModels();
        $modelid = $detail['modelid'];
        $model_table = $model_cache[$modelid]['tablename'];
        $tablename = $detail['issystem'] ? $model_table : $model_table.'_data';

        D('ModelField')->drop_field($tablename, $detail['field']);
        $this->ajaxReturn(array('statusCode'=>200,'message'=>'操作成功'));
    }
    public function ajax_field_setting(){
        $fieldtype = I('get.fieldtype');
        require APP_PATH.'Admin/Conf/fields/'.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
        ob_end_clean();
        ob_start();
        include APP_PATH.'Admin/Conf/fields/'.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
        $data_setting = ob_get_contents();
        //$data_setting = iconv('gbk','utf-8',$data_setting);
        ob_end_clean();
        $settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
        $this->ajaxReturn($settings);
    }

    public function ajax_checkfield(){
        $info = I('get.info');

        $field = strtolower($info['field']);
        $oldfield = strtolower($info['oldfield']);
        if($field==$oldfield) exit('{"ok":"ok"}');
        $modelid = intval($info['modelid']);
        $tablename = D('model')->getModelTablename($modelid);
        $issystem = intval(I('get.issystem'));
        
        if($issystem) {
            $table_name = C('DB_PREFIX').$tablename;
        } else {
            $table_name = C('DB_PREFIX').$tablename.'_data';
        }
        $fields = M()->db()->getFields($table_name);
        
        if(array_key_exists($field,$fields)) {
            exit('{"error":"已存在"}');
        } else {
            exit('{"ok":"ok"}');
        }
    }
    //修改字段状态
    public function modelFieldDisable(){
        $fieldid = I('get.fieldid');
        $map['fieldid'] = $fieldid;
        $field_detail = D('ModelField')->where($map)->find();
        $data['disabled'] = $field_detail['disabled'] == 1 ? 0 : 1;
        D('ModelField')->where($map)->save($data);
        $this->ajaxReturn(array('statusCode'=>200,'message'=>'操作成功'));
    }

    public function modelTrans(){
        D('Model')->transModel();
        $this->ajaxReturn(array('statusCode'=>200,'message'=>'操作成功'));
    }
    
    //单网页编辑
    public function pageEdit(){
		$catid = I('get.catid','','intval');
		//$this->catid = $catid = I('get.catid','','intval');
		
		//取出该文章信息
		$detail = D('Page')->getDetailByCatid($catid);
		if(!$detail){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'文章不存在'));
		}
		
		if(IS_POST){
			//验证规则
			$info = I('post.info');
			if(!D('Page')->create($info)){
				//如果不通过 ，输出错误报告
				$this->ajaxReturn(array('statusCode'=>300,'message'=>D('Page')->getError()));
			}else{
				D('Page')->where('catid='.$catid)->save($info);
			}
	
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功'));
		}else{
			$this->assign('catid', $detail['catid']);
			$this->assign('Detail', $detail);
			$this->display();
		}
	}

    //推荐位管理
    public function position(){
        $page_list = D('Position')->select();
        $this->assign('page_list', $page_list);
        // $this->category = $this->categorys;
        $this->assign('category', $this->categorys);
        $this->assign('model', D('Model')->getAllModels());
        $this->display();
    }

    //推荐位信息管理
    public function positionItem(){
        $posid = I('get.posid');
        $page_list = D('Position')->getList($posid);
        $this->assign('page_list', $page_list);
        $this->assign('category', $this->categorys);
        $this->display();
    }

    public function positionItemEdit(){
        $posid = I('get.posid');
        $modelid = I('get.modelid');
        $id = I('get.id');

        
        if(IS_POST){
            $info = I('post.info');
            $synedit = I('post.synedit');
            D('Position')->itemEdit($id, $modelid, $posid, $info, $synedit);
            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功'));
        }else{
            $pos_detail = D('Position')->getPosition($posid, $modelid, $id);
            if(empty($pos_detail)){
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
            }
            $pos_detail['data'] = string2array($pos_detail['data']);
            // $pos_detail = array_merge(string2array($pos_detail['data']), $pos_detail);
            // var_dump($pos_detail);exit;

            $this->assign('Detail', $pos_detail);
            $this->display();
        }

        
    }
}