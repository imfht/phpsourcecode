<?php
namespace Admin\Controller;
/**
 * 扩展后台管理页面
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AddonsController extends CommonController {

 
    //创建向导首页
    public function create(){
        if(!is_writable(ZS_ADDON_PATH))
            $this->error('您没有创建目录写入权限，无法使用此功能');

        //$hooks = M('Hooks')->field('name,description')->select();
        //$this->assign('Hooks',$hooks);
       // $this->meta_title = '创建向导';
        $this->display('create');
    }

    public function gethooks(){
    	$hooks = M('Hooks')->field(array('description'=>'label','name'=>'value'))->select();
    	
    	
        $this->ajaxReturn($hooks);
    }
  

    public function checkForm(){
        $data                   =   $_POST;
        $data['info']['name']   =   trim($data['info']['name']);
        if(!$data['info']['name'])
            $this->error('插件标识必须');
        //检测插件名是否合法
        $addons_dir             =   ZS_ADDON_PATH;
        if(file_exists("{$addons_dir}{$data['info']['name']}")){
            $this->error('插件已经存在了');
        }
        $this->success('可以创建');
    }
  //预览
    public function preview($output = true){
        $data                   =   $_POST;
        $data['info']['status'] =   (int)$data['info']['status'];
        $extend                 =   array();
        $custom_config          =   trim($data['custom_config']);
        if($data['has_config'] && $custom_config){
            $custom_config = <<<str


        public \$custom_config = '{$custom_config}';
str;
            $extend[] = $custom_config;
        }

        $admin_list = trim($data['admin_list']);
        if($data['has_adminlist'] && $admin_list){
            $admin_list = <<<str


        public \$admin_list = array(
            {$admin_list}
        );
str;
           $extend[] = $admin_list;
        }

        $custom_adminlist = trim($data['custom_adminlist']);
        if($data['has_adminlist'] && $custom_adminlist){
            $custom_adminlist = <<<str


        public \$custom_adminlist = '{$custom_adminlist}';
str;
            $extend[] = $custom_adminlist;
        }

        $extend = implode('', $extend);
        $hook = '';
        foreach ($data['hook'] as $value) {
            $hook .= <<<str
        //实现的{$value}钩子方法
        public function {$value}(\$param){

        }

str;
        }

        $tpl = <<<str
<?php

namespace Addons\\{$data['info']['name']};
use Common\Controller\Addon;

/**
 * {$data['info']['title']}插件
 * @author {$data['info']['author']}
 */

    class {$data['info']['name']}Addon extends Addon{

        public \$info = array(
            'name'=>'{$data['info']['name']}',
            'title'=>'{$data['info']['title']}',
            'description'=>'{$data['info']['description']}',
            'status'=>{$data['info']['status']},
            'author'=>'{$data['info']['author']}',
            'version'=>'{$data['info']['version']}'
        );{$extend}

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

{$hook}
    }
str;
        if($output)
            exit($tpl);
        else
            return $tpl;
    }
    public function build(){
        $data                   =   $_POST;
        $data['info']['name']   =   trim($data['info']['name']);
        $addonFile              =   $this->preview(false);
        $addons_dir             =   ZS_ADDON_PATH;
        //创建目录结构
        $files          =   array();
        $addon_dir      =   "$addons_dir{$data['info']['name']}/";
        $files[]        =   $addon_dir;
        $addon_name     =   "{$data['info']['name']}Addon.class.php";
        $files[]        =   "{$addon_dir}{$addon_name}";
        if($data['has_config'] == 1);//如果有配置文件
            $files[]    =   $addon_dir.'config.php';

        if($data['has_outurl']){
            $files[]    =   "{$addon_dir}Controller/";
            $files[]    =   "{$addon_dir}Controller/{$data['info']['name']}Controller.class.php";
            $files[]    =   "{$addon_dir}Model/";
            $files[]    =   "{$addon_dir}Model/{$data['info']['name']}Model.class.php";
        }
        $custom_config  =   trim($data['custom_config']);
        if($custom_config)
            $data[]     =   "{$addon_dir}{$custom_config}";

        $custom_adminlist = trim($data['custom_adminlist']);
        if($custom_adminlist)
            $data[]     =   "{$addon_dir}{$custom_adminlist}";

        create_dir_or_files($files);

        //写文件
        file_put_contents("{$addon_dir}{$addon_name}", $addonFile);
        if($data['has_outurl']){
            $addonController = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Controller;
use Home\Controller\AddonsController;

class {$data['info']['name']}Controller extends AddonsController{

}

str;
            file_put_contents("{$addon_dir}Controller/{$data['info']['name']}Controller.class.php", $addonController);
            $addonModel = <<<str
<?php

namespace Addons\\{$data['info']['name']}\Model;
use Think\Model;

/**
 * {$data['info']['name']}模型
 */
class {$data['info']['name']}Model extends Model{

}

str;
            file_put_contents("{$addon_dir}Model/{$data['info']['name']}Model.class.php", $addonModel);
        }

        if($data['has_config'] == 1)
            file_put_contents("{$addon_dir}config.php", $data['config']);
        $this->mtReturn(200, '创建成功');
        //$this->success('创建成功',U('index'));
    }

    /**
     * 插件列表
     */
    public function index(){
        $model=D('Addons');
        $numPerPage = empty($_REQUEST['numPerPage']) ? C('PAGE_LISTROWS') : $_REQUEST['numPerPage'];
        $currentPage= !empty($_REQUEST[C('VAR_PAGE')]) ? $_REQUEST[C('VAR_PAGE')] : 1;
        $lists       =   $model->getList();
       
        $total      =   $lists? count($lists) : 1 ;
        
        //$volist=array_slice($lists, $numPerPage*($currentPage-1),$numPerPage,true);
       
        $this->assign('list',  $lists);
        
       // $this->assign('totalCount', $total);//数据总数
		//$this->assign('currentPage', $currentPage);//当前的页数，默认为1
		//$this->assign('numPerPage', $numPerPage); //每页显示多少条
		cookie('_currentUrl_', __SELF__);
        
        $this->display();

    }

    /**
     * 插件后台显示页面
     * @param string $name 插件名
     */
    public function adminList($name){
        // 记录当前列表页的cookie
       // Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        
        $class = get_addon_class($name);
        if(!class_exists($class))
        {
        	$this->mtReturn(300, '插件不存在');
        }
        
        $addon  =   new $class();
        $this->assign('addon', $addon);
        $param  =   $addon->admin_list;
        
        if(!$param)
        {
        	$this->mtReturn(300, '插件列表信息不正确');
        }
        
        extract($param);
        
       $this->assign('title', $addon->info['title']);
        $this->assign($param);
        if(!isset($fields))
            $fields = '*';
        if(!isset($map))
            $map = array();
        if(isset($model))
            $this->_list(D("Addons://{$model}/{$model}")->field($fields),$map);
         
      // dump( D("{$model}"));
        //D("Addons://{$model}/{$model}")->_after_find();
        //$this->assign('list',$list);
        if($addon->custom_adminlist)
            $this->assign('custom_adminlist', $this->fetch($addon->addon_path.$addon->custom_adminlist));
       if($addon->custom_searchbar)
            $this->assign('custom_searchbar', $this->fetch($addon->addon_path.$addon->custom_searchbar));
       if($addon->custom_hiddeninput)
            $this->assign('custom_hiddeninput', $this->fetch($addon->addon_path.$addon->custom_hiddeninput));
       // dump($this->fetch($addon->addon_path.$addon->custom_adminlist));
        $this->display();
    }

  

    /**
     * 设置插件页面
     */
    public function config(){
        $id     =   (int)I('id');
        $addon  =   M('Addons')->find($id);
        if(!$addon)
        {
        	$this->mtReturn(300, '插件未安装');
        }
           
        $addon_class = get_addon_class($addon['name']);
        if(!class_exists($addon_class))
            trace("插件{$addon['name']}无法实例化,",'ADDONS','ERR');
        $data  =   new $addon_class;
        $addon['addon_path'] = $data->addon_path;
        $addon['custom_config'] = $data->custom_config;
        
        $db_config = $addon['config'];
        $addon['config'] = include $data->config_file;
        if($db_config){
            $db_config = json_decode($db_config, true);
            foreach ($addon['config'] as $key => $value) {
                if($value['type'] != 'group'){
                    $addon['config'][$key]['value'] = $db_config[$key];
                }else{
                    foreach ($value['options'] as $gourp => $options) {
                        foreach ($options['options'] as $gkey => $value) {
                            $addon['config'][$key]['options'][$gourp]['options'][$gkey]['value'] = $db_config[$gkey];
                        }
                    }
                }
            }
        }
        $this->assign('data',$addon);
        if($addon['custom_config'])
            $this->assign('custom_config', $this->fetch($addon['addon_path'].$addon['custom_config']));
        $this->display();
    }

    /**
     * 保存插件设置
     */
    public function saveConfig(){
        $id     =   (int)I('id');
        $config =   I('config');
        $flag = M('Addons')->where("id={$id}")->setField('config',json_encode($config));
        if($flag !== false){
           
            $this->mtReturn(200, '保存插件设置成功!');
        }else{
        	$this->mtReturn(300, '保存插件设置失败!');
           
        }
    }

    /**
     * 安装插件
     */
    public function install(){
        //$addon_name     =   trim(I('addon_name'));
         $addon_name     =   $_REQUEST['addon_name'];//trim(I('addon_name'));
        $class          =   get_addon_class($addon_name);
       
        if(!class_exists($class))
        {
        	$this->mtReturn(300,'插件不存在'.$addon_name);
            //$this->error('插件不存在');
        }
        $addons  =   new $class;
        $info = $addons->info;
        if(!$info || !$addons->checkInfo())//检测信息的正确性
        {
        	$this->mtReturn(300,'插件信息缺失');
        }
         
         
        session('addons_install_error',null);
        $install_flag   =   $addons->install();
        if(!$install_flag){
        	 $this->mtReturn(300,'执行插件预安装操作失败'.session('addons_install_error'));
            //$this->error('执行插件预安装操作失败'.session('addons_install_error'));
        }
        $addonsModel    =   D('Addons');
        $data           =   $addonsModel->create($info);
      if(is_array($addons->admin_list) && $addons->admin_list !== array()){
            $data['has_adminlist'] = 1;
        }else{
            $data['has_adminlist'] = 0;
        }
        if(!$data)
        {
        	 $this->mtReturn(300,$addonsModel->getError());
        	//$this->error($addonsModel->getError());
        }
            
         if($addonsModel->add($data)){
            $config         =   array('config'=>json_encode($addons->getConfig()));
            $addonsModel->where("name='{$addon_name}'")->save($config);
             $hooks_update   =   D('Hooks')->updateHooks($addon_name);
            if($hooks_update){
                S('hooks', null);
                $this->mtReturn(200, '"'.$info['title'].'"插件安装成功!','','forward',cookie('_currentUrl_'));
                //$this->success('安装成功');
            }else{
            	$map['name']=$addon_name;
                $addonsModel->where($map)->delete();
                $this->mtReturn(300,'更新钩子处插件失败,请尝试重新安装或首先添加相应钩子');
                //$this->error('更新钩子处插件失败,请卸载后尝试重新安装');
            }

        }else{
        	 $this->mtReturn(300,'写入插件数据失败');
           // $this->error('写入插件数据失败');
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
        $addonsModel    =   M('Addons');
        $id             =   $_REQUEST['id'];
        $db_addons      =   $addonsModel->find($id);
        $class          =   get_addon_class($db_addons['name']);
        $this->assign('jumpUrl',U('index'));
        if(!$db_addons || !class_exists($class)){
        	//$this->error('插件不存在');
            $this->mtReturn(300,'插件不存在');
        }
            
        session('addons_uninstall_error',null);
        $addons =   new $class;
        $uninstall_flag =   $addons->uninstall();
        if(!$uninstall_flag)
            //$this->error('执行插件预卸载操作失败'.session('addons_uninstall_error'));
            $this->mtReturn(300,'执行插件预卸载操作失败'.session('addons_uninstall_error'));
        $hooks_update   =   D('Hooks')->removeHooks($db_addons['name']);
        if($hooks_update === false){
            //$this->error('卸载插件所挂载的钩子数据失败');
            $this->mtReturn(300,'卸载插件所挂载的钩子数据失败');
        }
        S('hooks', null);
        $delete = $addonsModel->where("name='{$db_addons['name']}'")->delete();
        if($delete === false){
           // $this->error('卸载插件失败');
            $this->mtReturn(300,'卸载插件失败');
        }else{
            //$this->success('卸载成功');
            $this->mtReturn(200,'"'.$db_addons['title'].'"插件卸载成功','','forward',cookie('_currentUrl_'));
        }
    }

 

    public function execute($_addons = null, $_controller = null, $_action = null){
        if(C('URL_CASE_INSENSITIVE')){
            $_addons        =   ucfirst(parse_name($_addons, 1));
            $_controller    =   parse_name($_controller,1);
        }
       

        if(!empty($_addons) && !empty($_controller) && !empty($_action)){
            $Addons = A("Addons://{$_addons}/{$_controller}")->$_action();
        } else {
            $this->mtReturn(300,'没有指定插件名称，控制器或操作！');
        }
    }

}
