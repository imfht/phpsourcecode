<?php
/**
 *
 * 模块/广告
 * @author Lain
 *
 */
namespace Admin\Controller;
use Admin\Controller\AdminController;

class FormguideController extends AdminController {
	public function _initialize() {
		$action = array(
			// 'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
			//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}

	public function manage() {
		$map['type'] = 3;
		$page_list = D('Model')->where($map)->select();
		$this->assign('page_list', $page_list);
		$this->display();
	}

	public function add() {
		if (IS_POST) {
			$info = I('post.info');
            $result = D('Model')->addFormModel($info);
            if($result){
                $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功','tabid'=>'Formguide_manage'));
            }else{
                $this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
            }
		} else {
			$this->display('edit');
		}
	}

	//模型编辑
    public function edit(){
        $DB = D('Model');
        $this->modelid = $modelid = I('get.modelid','','intval');
        $detail = $DB->where('modelid='.$modelid)->find();

        $this->assign('Detail', $detail);
        $this->display();
    }

    //模型删除
    public function delete(){
        $modelid = I('get.modelid', 0, 'intval');
        if(!$modelid){
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
        }
        $result = D('Model')->deleteFormModel($modelid);
        if($result){
            $this->ajaxReturn(array('statusCode'=>200,'message'=>'保存成功'));
        }else{
            $this->ajaxReturn(array('statusCode'=>300,'message'=>'保存失败。ErrorNo:0003'));
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
            $tablename = C('DB_PREFIX').D('model')::PRE_FORM.$model_table;
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
            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Formguide_modelField'));
        }else{
            require APP_PATH.'Admin/Conf/formguide_fields.inc.php';
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

            $tablename = C('DB_PREFIX').D('model')::PRE_FORM.$model_table;

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
            $this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>true,'message'=>'保存成功', 'tabid'=>'Formguide_modelField'));
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
        $tablename = D('Model')::PRE_FORM.$model_table;

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
        
	    $table_name = C('DB_PREFIX').D('model')::PRE_FORM.$tablename;
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

    public function ajax_checkModelTableName(){
        if(IS_GET){
            $info = I('get.info');
            $tablename = $info['tablename'];
            $map['tablename'] = $tablename;
            $map['type'] = D('Model')::TYPE_FORM;
            $exist_table = D('Model')->where($map)->find();
            if($exist_table){
                echo '{"error":"模型表键名已存在"}';
            }else {
                echo '{"ok":""}';
            }
            exit;
        }
    }
}