<?php 
namespace Addons\Diyform\Controller;

class AdminDiyformController extends \Admin\Controller\AdminController{

	//新增自定义表单
	public function addform(){
		$model = M();
		//获取下一个自增ID
		$result = $model->query("SHOW TABLE STATUS LIKE 'sent_diyform'");
		$nextid = 'diyform'.$result[0]['auto_increment'];
		if(IS_POST){
			$diyform = D('Addons://Diyform/Diyform');
			if($diyform->create()){
				if($diyform->add()){
					//创建表
					$sql = "CREATE TABLE IF NOT EXISTS `".C('DB_PREFIX').'form_'.I('post.falsetable')."`(`id` MEDIUMINT(8) NOT NULL AUTO_INCREMENT COMMENT 'ID',PRIMARY KEY(`id`))ENGINE=MyISAM DEFAULT CHARSET=utf8;";
					//写入表单详情表
					$forminfo = D('Addons://Diyform/FormInfo');
					$array['textname'] = 'id';
					$array['texttitle'] = 'id';
					$array['texttype'] = 'hidden';
					$array['fieldname'] = 'id';
					$array['fieldtype'] = 'mediumint';
					$array['fieldlength'] = 8;
					$array['fielddefault'] = '';
					$array['fieldisnull'] = 0;
					$array['fieldcomment'] = 'ID';
					$array['tableid'] = $result[0]['auto_increment'];
					$forminfo->add($array);
					$model->execute($sql);
					$this->success('添加表单成功',U('Addons/adminList/name/Diyform'));
				}else{
					$this->error('添加表单失败！');
				}
			}else{
				$this->error($diyform->getError());
			}
		}
		
		$data = array('falsetable'=>$nextid,'template_lists'=>'diyform_list','template_detail'=>'diyform_detail','template_add'=>'diyform_add',);
		$builder = new \OT\Builder('config');
        $builder->title('添加表单元素')
            ->keyText('title', '名称')->keyText('falsetable','表名','前缀为默认'.C('DB_PREFIX'))->keyText('template_lists','列表页模板','列表页模板.html')
            ->keyText('template_detail','内容页目录','内容显示模板.html')
            ->buttonSubmit(U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/addform'))->buttonBack()
            ->data($data)
            ->display();
	}

	//修改自定义表单信息
	public function edit(){
		$model = D('Addons://Diyform/Diyform');
		if(IS_POST){
			if($model->create()){
				if($model->save()){
					$this->success('更新成功',U('Addons/adminList/name/Diyform'));
				}else{
					$this->error($model->getLastSql());
				}
			}
		}
		$map['id'] = I('get.id');
		$data = $model->where($map)->find();
		$builder = new \OT\Builder('config');
        $builder->title('添加表单元素')
            ->keyText('title', '名称')->keyReadOnly('table','表名')->keyText('template_lists','列表页模板','列表页模板.html')
            ->keyText('template_detail','内容页目录','内容显示模板.html')->keyText('template_add','发布页模板','添加页面模板.html')
            ->keyHidden('id')
            ->buttonSubmit(U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/edit'))->buttonBack()
            ->data($data)
            ->display();
	}

	//显示字段方法
	public function listfield(){
		$map['id'] = I('get.id');
		//实例化
		$model = D('Addons://Diyform/Diyform');
		$result = $model->where($map)->find();
		$forminfo = D('Addons://Diyform/Addons://Diyform/FormInfo');
		$fieldlist = $forminfo->where('tableid='.$result['id'])->order('id asc')->select();
		$builder = new \OT\Builder();
		$builder->title('显示字段列表')
            ->setStatusUrl(U('setRuleStatus'))->buttonNew('/admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/addfield/id/'.$map['id'],'新增字段')
            ->keyTitle('fieldname','字段名')->keyTitle('fieldtype','字段类型')->keyTitle('fieldlength','字段长度')->keyTitle('fielddefault','默认值')->keyTitle('textextra','额外内容')->keyTitle('fieldcomment','描述信息')
            ->keyDoAction('addons/execute?_addons=Diyform&_controller=AdminDiyform&_action=editfield&table='.$result['table'].'&field={$fieldname}','编辑')->keyDoAction('addons/execute?_addons=Diyform&_controller=AdminDiyform&_action=deleteField&table='.$result['table'].'&field={$fieldname}','删除')
            ->data($fieldlist)
            ->display();
	}

	//新增字段方法
	public function addfield(){
		//实例化
		$model = D('Addons://Diyform/Diyform');
		$map['id'] = I('get.id');
		if(IS_POST){
			//获取M
			$m = M();
			//获取表名
			$table = C('DB_PREFIX').I('post.table');
			//判断字段是否为空
			$default = I('post.fieldisnull') ? "NOT NULL DEFAULT'".I('post.fielddefault')."'" : 'NULL';
			$length = I('post.fieldlength') ? "(".I('post.fieldlength').")" : '';
			$sql = "ALTER TABLE `".$table."` ADD `".I('post.fieldname')."` ".I('post.fieldtype').$length.$default." COMMENT '".I('post.fieldcomment')."'";
			
			//获取前端显示值
			$info = D('Addons://Diyform/FormInfo');
			//对传递的数据进行验证
			if(!$info->create()){
				$this->error($info->getError());
			}
			//执行添加字段sql
			$m->execute($sql);
			//查询之前是否存在数值
			$info->add();
			$this->success('新增字段成功！',U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/listfield/id/'.I('post.tableid')));
		}
		//查询数据
		$map['id'] = I('get.id');
		$data = $model->where($map)->field('id as tableid,table')->find();
		$builder = new \OT\Builder('config');
		$builder->title('新增字段')
            ->keyText('texttitle', '表单标题','在发布页面显示的提示信息')->keySelect('texttype','表单类型','页面input类型',array('text'=>'text','password'=>'password','hidden'=>'hidden','radio'=>'radio','checkbox'=>'checkbox','select'=>'select','textarea'=>'textarea'))->keyText('textextra','文本框额外参数','只有在表单类型为radio、select、checkbox时有用，参数值为：radio的value,radio显示的文字|隔开')->keyText('fieldname', '字段名称','数据表中的唯一识别名')->keySelect('fieldtype','字段类型','选择一个类型',array('varchar'=>'VARCHAR','int'=>'INT','text'=>'TEXT','datetime'=>'DATETIME','char'=>'CHAR','mediumint'=>'MEDIUMINT'))
            ->keyText('fieldlength','字段长度','字段的最大长度，字段类型为Text时可以不填写')
            ->keyRadio('fieldisnull','是否为空','',array(0=>'为空',1=>'不为空'))->keyText('fielddefault','默认值','字段的默认值,如果不为空则填写')->keyText('fieldcomment','字段描述','描述信息')
            ->keyHidden('table')->keyHidden('tableid')
            ->buttonSubmit(U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/addfield'))->buttonBack()
            ->data($data)
            ->display();
	}

	//编辑字段方法
	public function editfield(){
		$forminfo = D('Addons://Diyform/FormInfo');
		if(IS_POST){
			$length = I('post.fieldlength') ? "(".I('post.fieldlength').")" : '';
			//判断是否为空
			$isnull = I('post.fieldisnull') ? "NOT NULL DEFAULT '".I('post.fielddefault')."'" : "NULL" ;
			$sql = "ALTER TABLE `".C('DB_PREFIX').I('post.table')."` CHANGE `".I('post.0')."` `".I('post.fieldname')."` ".I('post.fieldtype').$length." ".$isnull." COMMENT '".I('post.fieldcomment')."'";
			if(I('post.fieldname') == 'id'){
				$sql = "ALTER TABLE `".C('DB_PREFIX').I('post.table')."` CHANGE `".I('post.0')."` `".I('post.fieldname')."` ".I('post.fieldtype').$length." ".$isnull." AUTO_INCREMENT COMMENT '".I('post.fieldcomment')."'";
			}
			//修改字段信息表的数据
			$map['id'] = I('post.textid');
			$result = $forminfo->where($map)->find();
			if($result){
				$data = $forminfo->create(); //对传递的数据进行验证
				M()->execute($sql);
				if($forminfo->where($map)->save()){
					$this->success('修改字段信息成功！',U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/listfield/id/'.$result['tableid']));
				}else{
					$this->error('未修改数据！');
				}
			}
		}
		$table = I('get.table');
		$field = I('get.field');
		//查询表ID
		$tableid = $this->getTableID($table);
		$map['tableid'] = $tableid['id'];
		$map['textname'] = $field;
		//提取表单详细信息
		$result = $forminfo->where($map)->field('id as textid,texttitle,texttype,fieldname,fieldtype,fieldlength,fieldisnull,fielddefault,fieldcomment,textextra')->find();
		//把原来的字段信息写入数据数组中
		$data = array_merge($result,$tableid);
		array_push($data, I('get.field'));
		$builder = new \OT\Builder('config');
		$builder->title('编辑字段')
            ->keyText('texttitle', '表单标题','在发布页面显示的提示信息')->keySelect('texttype','表单类型','页面input类型',array('text'=>'text','password'=>'password','hidden'=>'hidden','radio'=>'radio','checkbox'=>'checkbox','select'=>'select','textarea'=>'textarea'))->keyText('textextra','文本框额外参数','只有在表单类型为radio、select、checkbox时有用')->keyText('fieldname', '字段名称','数据表中的唯一识别名')->keySelect('fieldtype','字段类型','选择一个类型',array('varchar'=>'VARCHAR','int'=>'INT','text'=>'TEXT','datetime'=>'DATETIME','char'=>'CHAR','mediumint'=>'MEDIUMINT'))
            ->keyText('fieldlength','字段长度','字段的最大长度，字段类型为Text时可以不填写')
            ->keyRadio('fieldisnull','是否为空','',array(0=>'为空',1=>'不为空'))->keyText('fielddefault','默认值','字段的默认值,如果不为空则填写')->keyText('fieldcomment','字段描述','描述信息')
            ->keyHidden('table')->keyHidden('textid')->keyHidden('0')
            ->buttonSubmit(U('/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/editfield'))->buttonBack()
            ->data($data)
            ->display();
	}

	//删除字段方法
	public function deleteField(){
		$map['table'] = C('DB_PREFIX').I('get.table');
		$map['field'] = I('get.field');
		$sql = "ALTER TABLE `".$map['table']."` DROP `".$map['field']."`";
		if($map['field'] == 'id'){
			$this->error('此字段不能删除！');
		}
		//查找表名ID
		$table = D('Addons://Diyform/Diyform');
		$tableid = $this->getTableID(I('get.table'));
		//查找包含此字段的info信息
		$forminfo = D('Addons://Diyform/FormInfo');
		$info['tableid'] = $tableid['id'];
		$info['textname'] = $map['field'];
		$forminfo->where($info)->delete();
		M()->execute($sql);
		$this->success('删除字段成功！');
	}

	//获取表名ID
	public function getTableID($tablename){
		$table = D('Addons://Diyform/Diyform');
		$where['table'] = $tablename;
		return $table->where($where)->field('id,table')->find();
	}

	//删除自定义表单
	public function delete(){
		//实例化MODEL
		$model = D('Addons://Diyform/Diyform');
		$map['id'] = I('get.id');
		//查找这个自定义表单是否存在
		$data = $model->where($map)->find();
		if($data){
			//先删除表
			$table = C('DB_PREFIX').$data['table'];
			//删除语句
			$sql = "DROP TABLE IF EXISTS ".$table;
			M()->execute($sql);
			$model->where($map)->delete() ? $this->success('删除成功',U('Addons/adminList/name/Diyform')) : $this->error('删除失败');		
		}else{
			$this->error('此表单不存在！');
		}
	}

	//数据管理
	public function dataManage(){
		$map['id'] = I('get.id');
		$model = D('Addons://Diyform/Diyform');
		$table = $model->where($map)->find();
		$myform = D($table['table']);
		$data = $myform->select();
		//查询字段
		$forminfo = D('Addons://Diyform/FormInfo');
		$field = $forminfo->where('tableid='.$table['id'])->field('fieldname,texttitle')->select();
		$builder = new \OT\Builder();
		$builder->title('显示数据列表');
		foreach ($field as $key => $value) {
			$builder->keyTitle($value['fieldname'],$value['texttitle']);
		}
        $builder->setStatusUrl(U('setRuleStatus'))->buttonDelete('/admin.php?s=/addons/execute/_addons/Diyform/_controller/AdminDiyform/_action/deleteAllData/table/'.$table['table'],'全部删除',array('class'=>'am-btn am-btn-danger ajax-post'))
        	->keyDoAction('addons/execute?_addons=Diyform&_controller=AdminDiyform&_action=deleteDataManage&table='.$table['table'].'&id=###','删除')
            ->data($data)
            ->display();
	}

	//删除数据操作
	public function deleteDataManage(){
		$map['id'] = I('get.id');
		$data = D(I('get.table'))->where($map)->find();
		if(empty($data)){
			$this->error('数据不存在或暂时不可删除');
		}else{
			D(I('get.table'))->where($map)->delete();
			$this->success('删除数据成功！');
		}
	}

	//数据全部删除
	public function deleteAllData(){
		$ids = implode(',',I('post.ids'));
		$map['id'] = array('IN',$ids);
		D(I('get.table'))->where($map)->delete();
		$this->success('全部删除成功！');
	} 
}