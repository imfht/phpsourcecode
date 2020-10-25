<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Groupcfg AS GroupcfgModel;
use app\common\field\Post AS FieldPost;

use app\common\traits\AddEditList;

class GroupCfg extends AdminBase
{
	use AddEditList;	
	protected $validate = '';
	protected $model;
	protected $form_items = [];
	protected $list_items;
	protected $tab_ext;
	protected $system_field = ['money','rmb','rmb_freeze','lastvist','lastip','regdate','regip','bday','introduce','qq','address','mobphone','idcard','idcardpic','truename','introducer_1','introducer_2','introducer_3'];
	
	protected function _initialize()
    {
		parent::_initialize();
		$this->model = new GroupcfgModel();
	}
	
	/**
	 * 填写表单参数选项
	 * @param number $group 用户组ID
	 */
	protected function set_items($group=0){	    
	    $array = [];
	    foreach ( getGroupByid() AS $key => $name) {
	        $array[$key] = $name;
	    }
	    
	    $this->form_items = [
	            ['hidden', 'id'],
	            ['text', 'title', '字段中文名称',''],
	            ['text', 'c_key', '字段英文变量名','创建后不要随意修改，不同的用户组相同的功能，变量名最好一样，方便会员升级保持不变'],	            
	            ['radio', 'type', '所属分组','',$array,$group],
	            ['select', 'form_type', '表单类型','',config('form'),'text'],
	            ['textarea', 'options', '表单参数项','每条参数换一行,参数与名称用“|”线隔开，比如“1|正确”'],
	            ['text', 'c_descrip', '介绍描述'],
	            ['checkbox', 'allowview', '有权限查看此字段的用户组','不选择而所有用户都有权限查看此字段',$array],
	            ['radio', 'ifmust', '是否为升级用户组的必填字段','升级的时候必填',['否','是']],
	            ['radio', 'forbid_edit', '是否禁止用户修改','',['自由修改','严禁修改']],
	            //['textarea', 'htmlcode', '额外HTML代码'],
	    ];
	    
	    $this->tab_ext['trigger'] = [
	            ['form_type','checkbox,checkboxtree,radio,select','options'],
	            //['type',implode(',',$plugin_array),'ifsys'],
	    ];
	    
	    return $this->form_items;  //提交表单的时候要用到返回参数
	}

	//删除，这是重写AddEditList的方法
	protected function deleteContent($ids){
	    
	    if(empty($ids)){
	        $this->error('ID有误');
	    }
	    
	    $ids = is_array($ids)?$ids:[$ids];
	    
	    //if ($this->model->destroy($ids)) {
	    if ($this->model->where('id','in',$ids)->delete()) {	    
	        return true;
	    } else {
	        return false;
	    }
	}
	
	public function delete($ids = null)
	{
	    //$data = get_post();
	    //$ids=$data['ids'];
	    $info = $this->model->get($ids);
	    if( $this->deleteContent($ids) ){
	        $this->success('删除成功',auto_url('index',['group'=>$info['type']]));
	    }else{	        
	        $this->error('删除失败');
	    }
	}
	
	public function edit($id = null)
	{
	    if (empty($id)) $this->error('缺少参数');
	    
	    if(IS_POST){
	        $data = get_post();
	        $result = $this->validate($data,
	                [
	                        'c_key'  => 'require|regex:^[_a-zA-Z]\w{0,39}$',
	                        'title'   => 'require|max:50',
	                ]);
	        if($result !== true ){
	            $this->error($result);
	        }
	        
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        $rs = $this->model->where($map)->find();
	        if ( ($rs && $rs['id']!=$id) || (!in_array($data['c_key'], $this->system_field)&&table_field('memberdata',$data['c_key'])) ) {
	            $this->error('变量名已经存在了，请更换一个');
	        }
	        if (!empty($this -> validate)) {   // 验证表单
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        //$data = $this->check_post($data);
	        $data = FieldPost::format_php_all_field($data,$this->set_items());
	        
	        if ($this -> model -> update($data)) {
	            $this->success('数据更新成功',auto_url('index',['group'=>$data['type']]));
	        } else {
	            $this->error('数据更新失败');
	        }
	    }
	    
	    $info = $this->model->where('id',$id)->find();
	    
	    $info['form_type'] || $info['form_type']='text';    
	    
	    $this->set_items();
	    
	    return $this->editContent($info,auto_url('index',['group'=>$info['type']]));
	}
	
	/**
	 * 批量导入默认字段
	 * @param number $group
	 */
	public function autoadd($group=0){
	    if (empty($group)) {
	        $this->error('必须要指定一个用户组');
	    }
	    $sql = "INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '会员积分', 'money', '', 'text', '', '', '', 100, '', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '可用余额', 'rmb', '', 'text', '', '', '', 98, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '冻结余额', 'rmb_freeze', '', 'text', '', '', '', 96, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '最后登录时间', 'lastvist', '', 'datetime', '', '', '', 94, '', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '最后登录IP', 'lastip', '', 'text', '', '', '', 90, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '注册日期', 'regdate', '', 'datetime', '', '', '', 92, '', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '注册IP', 'regip', '', 'text', '', '', '', 88, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '生日', 'bday', '', 'date', '', '', '', 86, '', 0, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '自我介绍', 'introduce', '', 'textarea', '', '', '', 84, '', 1, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, 'QQ号码', 'qq', '', 'text', '', '', '', 82, '', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '联系地址', 'address', '', 'text', '', '', '', 80, '3', 1, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '手机号码', 'mobphone', '', 'text', '', '', '', 78, '3', 0, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '证件号码', 'idcard', '', 'text', '', '', '个人为身份证号码，企业为执照号', 76, '3', 0, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '证件扫描件', 'idcardpic', '', 'image', '', '', '介绍描述', 74, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '真实姓名', 'truename', '', 'text', '', '', '个人填姓名，企业填完整的企业名称', 72, '3', 1, 0, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '1级推荐人', 'introducer_1', '', 'text', '', '', '', 70, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '2级推荐人', 'introducer_2', '', 'text', '', '', '', 68, '3', 0, 1, '', '', '', '', '');
INSERT INTO `qb_groupcfg` (`id`, `type`, `title`, `c_key`, `c_value`, `form_type`, `options`, `htmlcode`, `c_descrip`, `list`, `allowview`, `ifmust`, `forbid_edit`, `nav`, `input_width`, `input_height`, `match`, `css`) VALUES(0, {$group}, '3级推荐人', 'introducer_3', '', 'text', '', '', '', 66, '3', 0, 1, '', '', '', '', '');
";
	    into_sql($sql);
	    $this->success('添加成功');
	}
	
	/**
	 * 手工加字段
	 * @param number $group
	 * @return mixed|string
	 */
	public function add($group=0)
	{
	    if(IS_POST){
	        $data = get_post();
	        
	        $result = $this->validate($data,
	                [
	                        'c_key'  => 'require|regex:^[a-z]\w{0,39}$',
	                        'title'   => 'require|max:50',
	                ]);
	        if($result !== true ){
	            $this->error($result);
	        }elseif(!$data['type']){
	            $this->error('请先选择一个分类');
	        }
	        
	        $map = [
	                'c_key'=>$data['c_key'],
	                'type'=>$data['type'],
	        ];
	        
	        if ($this->model->where($map)->find() || table_field('memberdata',$data['c_key'])) {
	            $this->error('变量名已经存在了，请更换一个');
	        }
	        if (!empty($this -> validate)) {   // 验证表单         
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        //$data = $this->check_post($data);
	        $data = FieldPost::format_php_all_field($data,$this->set_items());
	        if ( $this -> model -> create($data)) {
	            $this->success('字段创建成功',auto_url('index',['group'=>$data['type']]));
	        } else {
	            $this->error('数据插入失败');
	        }
	    }
	    $this->set_items($group);
	    return $this->addContent();
	}
	
	/**
	 * 对于一些多选项之类的字段进行转义处理
	 * @param array $data
	 */
// 	protected function check_post(&$data=[]){
// 	    foreach($this->set_items() AS $cfg){
// 	        $form_type = $cfg[0];  //字段表单类型
// 	        $name = $cfg[1]; //字段变更名
// 	        if (!isset($data[$name])) {
// 	            switch ($form_type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 0;
// 	                    break;
// 	                case 'checkbox':
// 	                    $data[$name] = '';
// 	                case 'checkboxtree':
// 	                    $data[$name] = '';
// 	                    break;
// 	            }
// 	        } else {
// 	            // 如果值是数组则转换成字符串，适用于复选框等类型
// 	            if (is_array($data[$name])) {
// 	                $data[$name] = implode(',', $data[$name]);
// 	                //continue;
// 	            }
// 	            switch ($form_type) {
// 	                // 开关
// 	                case 'switch':
// 	                    $data[$name] = 1;
// 	                    break;
// 	                    // 日期时间
// 	                case 'date':
// 	                case 'time':
// 	                case 'datetime':
// 	                    $data[$name] = strtotime($data[$name]);
// 	                    break;
// 	            }
// 	        }
// 	    }
// 	    return $data;
// 	}
	
	/**
	 * 列出所有分类
	 * @return string[][]
	 */
	private function nav(){
	    $tab_list = [];
	    foreach ( getGroupByid() AS $key => $name) {
	        $tab_list[$key]['title'] = $name;
	        $tab_list[$key]['url']   = auto_url('index', ['group' => $key]);
	    }	    
	    $tab_list[0]   = [
	            'title'=>'所有字段',
	            'url'=>auto_url('index', ['group' => '0']),
	    ];	    
	    return $tab_list;
	}

	public function index($group=0)
    {
		$this->tab_ext = [
				'nav'=>[ self::nav() , $group],
				'help_msg'=>'暂无介绍!',
				];

		$this->list_items = [
				['c_key', '关键字变量名', 'text'],              
				['title', '字段名称', 'text.edit'],
				['form_type', '表单类型', 'select2',config('form')],
		        ['ifmust', '认证必填', 'yesno',['非必填','升填']],
		        ['forbid_edit', '禁止修改', 'yesno',['自由修改','禁止修改']],
		        ['list', '排序值', 'text.edit'],
			];
		
		$this->tab_ext['top_button'] =[
		        [
		                'title' => '手工新增字段',
		                'icon'  => 'fa fa-plus',
		                'class' => 'btn btn-primary',
		                'href'  => auto_url('add',['group'=>$group])
		        ],
		        [
		                'title' => '批量导入默认字段',
		                'icon'  => 'fa fa-plus-square-o',
		                'class' => 'btn btn-primary',
		                'href'  => auto_url('autoadd',['group'=>$group])
		        ],
		        [
		                'title' => '批量删除',
		                'icon'  => 'fa fa-times-circle',
		                'type'  => 'delete'
		        ],
		];
		
		$map = $group ? ['type'=>$group] : [];
		$data = $this->model->where($map)->order('list','desc')->paginate(50);		
		return $this->getAdminTable( $data );
    }

}
