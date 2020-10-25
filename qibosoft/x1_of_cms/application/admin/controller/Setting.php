<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\model\Config AS ConfigModel;
use plugins\config_set\model\Group AS GroupModel;
use app\common\traits\AddEditList;
use think\Cache;
use app\common\fun\Cfgfield;
use think\Db;


class Setting extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items = [];
    protected $list_items;
    protected $tab_ext;
    protected $group = 'base';
    //系统强制要补上的字段
    protected $_config = [
        [
            'c_key'=>'sms_type',
            'title'=>'短信接口标志',
            'c_descrip'=>'留空则用阿里云短信接口,其它接口请输入短信接口关键字',
            'form_type'=>'text',
            'ifsys'=>1,
            'type'=>1,
            'list'=>-1,
        ],
    ];
    protected $config = null;    //频道或插件强制要补上的字段

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new ConfigModel();
//         $this->tab_ext = [ 'help_msg'=>'系统参数配置',];
        $this->tab_ext['page_title'] = '系统参数配置';
        $this->add_module_config();
    }
    
    /**
     * 模块里要强制补上的配置参数
     * 提醒,如果你的频道不想要下面的字段,就需要在你频道那里设置 protected $config = [];
     */
    protected function add_module_config(){
        if ($this->config!==null || defined('IN_PLUGIN') || empty(config('system_dirname'))) {
            return ;
        }
        $this->config = [
                [
                        'c_key'=>'module_pc_index_template',
                        'title'=>'频道主页PC版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/pc_index.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'module_wap_index_template',
                        'title'=>'频道主页WAP版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/wap_index.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],                
                [
                        'c_key'=>'module_pc_list_template',
                        'title'=>'频道列表页PC版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/content/pc_list.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'module_wap_list_template',
                        'title'=>'频道列表页WAP版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/content/wap_list.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],                
                [
                        'c_key'=>'module_pc_show_template',
                        'title'=>'频道内容页PC版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/content/pc_show.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'module_wap_show_template',
                        'title'=>'频道内容页WAP版风格模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/content/wap_show.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                
                
                [
                        'c_key'=>'module_pc_index_layout',
                        'title'=>'频道主页PC版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/pc_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                [
                        'c_key'=>'module_wap_index_layout',
                        'title'=>'频道主页WAP版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/wap_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                [
                        'c_key'=>'module_pc_list_layout',
                        'title'=>'频道列表页PC版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/pc_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                [
                        'c_key'=>'module_wap_list_layout',
                        'title'=>'频道列表页WAP版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/wap_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                [
                        'c_key'=>'module_pc_show_layout',
                        'title'=>'频道内容页PC版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/pc_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                [
                        'c_key'=>'module_wap_show_layout',
                        'title'=>'频道内容页WAP版头部布局模板',
                        'c_descrip'=>'请把模板放在此目录下: /template/index_style/ 然后输入相对路径,比如 xxx/index/wap_layout.htm',
                        'form_type'=>'text',
                        'ifsys'=>0,
                        'list'=>-2,
                ],
                
                [
                        'c_key'=>'module_wap_default_layout',
                        'title'=>'WAP端频道个性风格',
                        'c_descrip'=>'优化级高于系统风格,但低于模板个性设置',
                        'form_type'=>'select',
                        'options'=>'app\\common\\util\\Style@get_indexstyle_template@["layout","wap"]',
                        'ifsys'=>0,
                        'list'=>0,
                ],
                [
                        'c_key'=>'module_pc_default_layout',
                        'title'=>'PC端频道个性风格',
                        'c_descrip'=>'优化级高于系统风格,但低于模板个性设置',
                        'form_type'=>'select',
                        'options'=>'app\\common\\util\\Style@get_indexstyle_template@["layout","pc"]',
                        'ifsys'=>0,
                        'list'=>0,
                ],
                
                
                [
                        'c_key'=>'group_create_num',
                        'title'=>'发布主题数量对应用户组的限制',
                        'c_descrip'=>'针对总数限制，非按天限制。留空或为0则不限制',
                        'form_type'=>'usergroup',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'group_post_money',
                        'title'=>'发布主题对应用户组的虚拟币变化',
                        'c_descrip'=>'填负数才是扣积分，否则就是奖励积分，0或留空则不做处理',
                        'form_type'=>'usergroup',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
                [
                        'c_key'=>'group_topic_jftype',
                        'title'=>'发布主题奖励哪种虚拟币',
                        'c_descrip'=>'',
                        'c_value'=>'0',
                        'form_type'=>'jftype',
                        'ifsys'=>0,
                        'list'=>-1,
                ],
				[
                        'c_key'=>'forbid_post_topic_phone_noyz',
                        'title'=>'未验证手机是否禁止发主题',
                        'c_value'=>'0',
                        'form_type'=>'radio',
                        'options'=>"0|不限\r\n1|未验证不允许发布",
                        'ifsys'=>0,
                        'list'=>-2,
                ],
        ];
    }
    
    /**
     * 补全系统强制要加上的字段
     * @param number $group 分组ID
     */
    protected function add_config($group=0){        
        if (empty($group)) {
            return ;
        }
        $gdb = GroupModel::where('id',$group)->find();
        if($gdb['sys_id']==0){                  //分组属于系统,不属于任何频道或插件
            $array = $this->_config;
        }else{                                          //分组属于频道或插件
            $array = $this->config;
        }
        
        foreach ($array AS $rs){
            $realut = ConfigModel::where(['c_key'=>$rs['c_key'],'sys_id'=>$gdb['sys_id'],])->find();
            if(empty($realut)){     //数据表中不存在强制要加的字段,就强制补上
                $rs['sys_id'] = $gdb['sys_id'];
                $rs['type'] = $group;
                $rs['ifsys'] = $gdb['sys_id']>0 ? 0 : $rs['ifsys'];
                ConfigModel::create($rs);
            }
        }
    }
    
    /**
     * 清除缓存
     */
    public function clearcache(){
        delete_dir(RUNTIME_PATH.'temp');
        delete_dir(RUNTIME_PATH.'log');
        Cache::clear();
        cache2('qbTagCacheKey__*',null);    //标签缓存
        
        $this->success('清除成功','index/welcome');
    }
    
    /**
     * 设置分组导航
     * @param unknown $group
     */
    protected function setNav($group){
//         $this->tab_ext = [
//                 'nav'=>[
//                         GroupModel::getNav(true),   //分组导航
//                         $group
//                 ],
//         ];
        $this->tab_ext['nav'] = [
                GroupModel::getNav(true),   //分组导航
                $group
        ];
    }
    
    /**
     * 修改后台入口文件名
     * @param string $filename
     */
    private function rename_adminfile($filename=''){

        if ($filename!=''){
            if (!preg_match('/^[\w\.]+$/i', $filename)) {
                $this->error('后台入口文件名格式有误!');
            }
            if (!preg_match('/\.php$/i', $filename)) {
                $filename .= '.php';
            }            
        }
        if ($filename!=''&&$filename!=config('admin.filename')) {
            if (!is_file(APP_PATH.'extra/admin.php')) {
                write_file(APP_PATH.'extra/admin.php', '<?php ');
            }
            if(!is_writable(APP_PATH.'extra/admin.php')){
                $this->error('修改后台入口失败,此文件不可写:'.APP_PATH.'extra/admin.php');
            }elseif(is_file(ROOT_PATH.$filename)){
                $this->error('此文件已存在,请更换一个:'.ROOT_PATH.$filename);
            }elseif(!preg_match('/^([\w]+)\.php$/', $filename)){
                $this->error('文件名不符合规划!');
            }
            if(config('admin.filename')==''){
                config('admin.filename','admin.php');
            }
            //rename有时候不能改动正在使用的文件
            if(copy(ROOT_PATH.config('admin.filename'),ROOT_PATH.$filename) && unlink(ROOT_PATH.config('admin.filename')) ){
                $array = config('admin');
                $array['filename'] = $filename;
                write_file(APP_PATH.'extra/admin.php', '<?php return '.var_export($array,true).';');
                $listdb = Db::name('admin_menu')->order('id desc')->column(true);
                foreach($listdb AS $rs){
                    if(strstr($rs['url'],config('admin.filename'))){
                        $rs['url'] = str_replace(config('admin.filename'), $filename, $rs['url']);
                        Db::name('admin_menu')->where('id',$rs['id'])->update(['url'=>$rs['url']]);
                    }
                }
                $this->success('后台入口名修改成功,请重新登录后台',get_url('/'.$filename.'/admin/index/quit.html'));
            }else{
                $this->error('后台入口名修改失败,请进服务器手工改名');
            }
        }
    }
    
    
    /**
     * 参数设置
     * @param string $group 分组ID
     * @return mixed|string
     */
    public function index($group='1')
    {
        // 保存数据
        if ($this->request->isPost()) {
            // 表单数据
            $data = $this->request->post();
            if( $this->model->save_group_data($data,$data['group']?$data['group']:$group) ){
                if ($group==1) {
                    $this->rename_adminfile($data['admin_filename']);
                }
                cache('webdb',null);
               
                $this->get_hook('setting_post',$data,[],[],false);     //扩展增强文件接口
                
                $this->success('修改成功');
            }            
        }        
        
        $this->get_hook('setting_get',$data=[],[],[],false);     //扩展增强文件接口
        
        $this->add_config($group);      //补全字段
        
        //某分类下的所有参数选项
        $list_data = empty($group) ? [] : $this->model->getListByGroup($group);
        
        
        //联动字段
        $this->tab_ext['trigger'] = $this->getTrigger($list_data);
        
        //创建表格
        $this->setNav($group);
        $tab_list = [
                ['hidden','group',$group]
        ];
        $this->form_items = array_merge($tab_list,Cfgfield::toForm($list_data));
        $this->set_form_group();
        
        $data = [];
        foreach($list_data AS $rs){
            $data[$rs['c_key']] = $rs['c_value'];
        }
        if ($group==1) {
            $data['admin_filename'] = config('admin.filename')=='admin.php'?'':str_replace('.php','',config('admin.filename'));
        }
        $this->mid = $group;    //纯属为了模板考虑的
		return $this->editContent($data);
    }
    
    
    /**
     * 获得某些字段要关联其它字段
     * @param array $field_array
     * @return string[][]|unknown[][]
     */
    protected function getTrigger($field_array=[]){
        $array = [];
        foreach ($field_array AS $rs){
            if($rs['form_type']=='select'||$rs['form_type']=='radio'||$rs['form_type']=='checkbox'){
                $detail = explode("\r\n",$rs['options']);
                foreach($detail AS $value){
                    list($v,$b,$otherFields) = explode("|",$value);
                    if($otherFields){
                        $_fs = explode(',',$otherFields);
                        foreach($_fs AS $otherField ){
                            $array[$rs['c_key']][$otherField][] = $v;
                        }
                    }
                }
            }
        }
        $tri = [];
        foreach($array as $name=>$ar){
            foreach($ar AS $otherField=>$rs){
                $tri[] = [$name,implode(',', $rs),$otherField];
            }
        }
        return $tri;
    }
    
    /**
     * 分组设置
     */
    protected function set_form_group(){
        $array_a = $array_b = [];
        foreach($this->form_items AS $rs){
            if (in_array($rs[1], ['module_pc_index_template','module_wap_index_template','module_pc_list_template','module_wap_list_template','module_pc_show_template','module_wap_show_template','module_pc_index_layout','module_wap_index_layout','module_pc_list_layout','module_wap_list_layout','module_pc_show_layout','module_wap_show_layout'])) {
                list(,$pcwap,$filename,$type) = explode('_', $rs[1]);
                if ($type=='layout') {
                    $filename = 'layout';
                }
                $rs[3] = $rs['c_descrip'] = \app\common\util\Style::select_indexstyle_template($filename,$pcwap,$rs[1]).$rs[3];
                $array_b[] = $rs;
            }else{
                $array_a[] = $rs;
            }
        }
        if ($array_b) {
            $this -> tab_ext['group']['基础设置'] = $array_a;
            $this -> tab_ext['group']['模板个性设置'] = $array_b;
        }        
    }

}
