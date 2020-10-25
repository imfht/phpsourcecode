<?php
namespace Wpf\App\Admin\Common\Controllers;
class CommonController extends \Wpf\Common\Controllers\CommonController{
    
    /* 全局允许访问的控制器，优先级最高 */
    static protected $allow_class = array("Wpf\App\Admin\Controllers\GlobalController","Wpf\App\Admin\Controllers\AjaxController","Wpf\App\Admin\Controllers\TestController");
    
    /* 保存禁止通过url访问的公共方法,例如定义在控制器中的工具方法 ;deny优先级高于allow*/
    static protected $deny  = array();

    /* 保存允许访问的公共方法 */
    static protected $allow = array();
    
    
    
    
    public function initialize(){
        parent::initialize();
        
    }
    
    public function onConstruct(){
        parent::onConstruct();
        
        
        
        $adminuser = $this->isLogin();
        if($adminuser['id']){
            define('ADMIN_UID',$adminuser['id']); 
        }else{
            define('ADMIN_UID',0); 
        }
        
        $this->view->setVar('__adminuser__', $adminuser);
        
        
        $now_path = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
        
        define('IS_ADMIN_ROOT',   $this->isAdministrator());
        
        $access =   $this->accessControl();

        if( !ADMIN_UID && $access === null){// 还没登录 跳转到登录页面
            $this->response->redirect("Global/login")->getHeaders()->send();
            exit;
        }
        elseif( $access === false ) {
            $this->error('403:禁止访问');
        }elseif( $access === null ){
            $dynamic        =   $this->checkDynamic();//检测分类栏目有关的各项动态权限
            
            
            if( $dynamic === null ){
                //检测非动态权限
                //$rule  = strtolower(CONTROLLER_CLASS);
                $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);
                if ( !$this->checkRule($rule,array(1,2)) ){
                    $this->error('提示:无权访问,您可能需要联系管理员为您授权!');
                }

            }elseif( $dynamic === false ){
                $this->error('提示:无权访问,您可能需要联系管理员为您授权!');
            }
        }

        $this->view->setVar('__controller__', $this);
        
        $__base_menu__ = $this->getMenus();
        
        $this->view->setVar('__base_menu__', $__base_menu__);

        
        $this->headercssurl
            ->addCss(STATIC_URL."/css/google/font.css",false,false)
            ->addCss(STATIC_URL."/theme/assets/global/plugins/font-awesome/css/font-awesome.min.css",false,false)
            ->addCss(STATIC_URL."/theme/assets/global/plugins/simple-line-icons/simple-line-icons.min.css",false,false);
            //->addCss(STATIC_URL."/"."theme/assets/global/css/components.css",false,false,array("id"=>"style_components"))
            //->addCss(STATIC_URL."/"."theme/assets/admin/layout/css/themes/darkblue.css",false,false,array("id"=>"style_color"));
        
        $this->headercss
            ->setPrefix(STATIC_URL."/")      
            //->addCss("css/google/font.css")
            //->addCss("theme/assets/global/plugins/font-awesome/css/font-awesome.min.css")
            //->addCss("theme/assets/global/plugins/simple-line-icons/simple-line-icons.min.css")
            ->addCss("theme/assets/global/plugins/bootstrap/css/bootstrap.min.css")
            ->addCss("theme/assets/global/plugins/uniform/css/uniform.default.css")
            ->addCss("theme/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css");
             
        $this->headercss__    
            ->setPrefix(STATIC_URL."/")     
            ->addCss("theme/assets/global/css/components.css",false,false,array("id"=>"style_components"))           
            ->addCss("theme/assets/global/css/plugins.css")
            ->addCss("theme/assets/admin/layout/css/layout.css")
            ->addCss("theme/assets/admin/layout/css/themes/darkblue.css",false,false,array("id"=>"style_color"))
            ->addCss("theme/assets/admin/layout/css/custom.css");
            
            
        $this->headerjs
            ->setPrefix(STATIC_URL."/")     
            ->addJs("theme/assets/global/plugins/jquery.min.js");
            
            
        $this->footerjs
            ->setPrefix(STATIC_URL."/")     
            ->addJs("theme/assets/global/plugins/jquery-migrate.min.js")
            ->addJs("theme/assets/global/plugins/jquery-ui/jquery-ui.min.js")
            
            ->addJs("theme/assets/global/plugins/bootstrap/js/bootstrap.min.js")
            ->addJs("theme/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js")
            ->addJs("theme/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js")
            
            ->addJs("theme/assets/global/plugins/jquery.blockui.min.js")
            ->addJs("theme/assets/global/plugins/jquery.cokie.min.js")
            ->addJs("theme/assets/global/plugins/uniform/jquery.uniform.min.js")
            ->addJs("theme/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js")
            
            
            ->addJs("theme/assets/global/scripts/metronic.js")
            ->addJs("theme/assets/admin/layout/scripts/layout.js")
            ->addJs("theme/assets/admin/layout/scripts/quick-sidebar.js")
            ->addJs("theme/assets/admin/layout/scripts/demo.js");
            

    }
    
    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        // $menus  =   session('ADMIN_MENU_LIST'.$controller);
        if(empty($menus)){
			// 获取主菜单
			$where[]	=	"pid = 0";
			$where[]	=	"hide = 0";
			if(!$this->config->DEVELOP_MODE){ // 是否开发者模式
				$where[]	=	"is_dev = 0";
			}
            $where = implode(" and ",$where);
            $AdminMenuModel = new \Wpf\App\Admin\Models\AdminMenu();
            $menus['main'] = $AdminMenuModel->find(array(
                "conditions" => $where,
                "order" => "sort asc"
            ))->toArray();
            
            //$menus['main']  =	M(C("BASE_DB_NAME").".".'Menu')->where($where)->order('sort asc')->select();

            $menus['child'] = array(); //设置子节点
            
            //高亮主菜单
            $current = $AdminMenuModel->findFirst(array(
                "url like '%{$controller}/".ACTION_NAME."%' and pid<>0",
                "columns" => "id"
            ));
            
            if( ! $current ){
                $current = $AdminMenuModel->findFirst(array(
                    "url like '%{$controller}/".ACTION_NAME."%'",
                    "columns" => "id"
                ));
                //$current = M(C("BASE_DB_NAME").".".'Menu')->where("url like '%{$controller}/".ACTION_NAME."%'")->field('id')->find();
            }
            
            if($current){
                $current = $current->toArray();
            }
            
            
            $nav = $AdminMenuModel->getPath($current['id']);
            
            $nav_first_title = $nav[0]['title'];

            foreach ($menus['main'] as $key => $item) {
                if (!is_array($item) || empty($item['title']) || empty($item['url']) ) {
                    $this->error('控制器基类$menus属性元素配置有误');
                }
                if( stripos($item['url'],MODULE_NAME)!==0 ){
                    $item['url'] = MODULE_NAME.'/'.$item['url'];
                }
                // 判断主菜单权限
                if ( !IS_ADMIN_ROOT && !$this->checkRule($item['url'],\Wpf\App\Admin\Models\AdminAuthRule::RULE_MAIN,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }

				// 获取当前主菜单的子菜单项
                if($item['title'] == $nav_first_title){
                    $menus['main'][$key]['class']='active';
                    //生成child树
                    
                    $groups = $AdminMenuModel->find(array(
                        "conditions"=>"pid = {$item['id']}",
                        "order" => "sort asc",
                        "columns" => "distinct [group]",
                    ))->toArray();
                    
					if($groups){
						$groups = array_column($groups, 'group');
					}else{
						$groups	=	array();
					}
                    

                    //获取二级分类的合法url
					$where			=	array();
					$where[]	=	"pid = ".$item['id'];
					$where[]	=	"hide = 0";
					if(!$this->config->DEVELOP_MODE){ // 是否开发者模式
						$where[]	=	"is_dev = 0";
					}
                    $where = implode(" and ",$where);
                    $second = $AdminMenuModel->find(array(
                        $where,
                        "columns" => "id,url"
                    ))->toArray();
                    foreach($second as $value){
                        $second_urls[$value['id']] = $value['url'];
                    }

                    
                    //$second_urls = M(C("BASE_DB_NAME").".".'Menu')->where($where)->getField('id,url');
                    
                    // trace($second_urls);
					if(!IS_ADMIN_ROOT){
						// 检测菜单权限
						$to_check_urls = array();
						foreach ($second_urls as $key=>$to_check_url) {
							if( stripos($to_check_url,MODULE_NAME)!==0 ){
								$rule = MODULE_NAME.'/'.$to_check_url;
							}else{
								$rule = $to_check_url;
							}
							if($this->checkRule($rule, \Wpf\App\Admin\Models\AdminAuthRule::RULE_URL,null))
								$to_check_urls[] = $to_check_url;
						}
					}

					// 按照分组生成子菜单树
                    foreach ($groups as $g) {
                        //$map = array('group'=>$g);
                        $map = array();
                        $map[] = "[group] = '{$g}'";
                        if(isset($to_check_urls)){
							if(empty($to_check_urls)){
								// 没有任何权限
								continue;
							}else{
							     $to_check_urls = "'".implode("','",$to_check_urls)."'";
		                          $map[] = "url in ({$to_check_urls})";
								//$map['url'] = array('in', $to_check_urls);
							}
						}
						$map[]	=	"pid=".$item['id'];
						$map[]	=	"hide = 0";
						if(!$this->config->DEVELOP_MODE){ // 是否开发者模式
							$map[]	=	"is_dev=0";
						}
                        $map = implode(" and ",$map);
                        
                        
                        
                        $menuList = $AdminMenuModel->find(array(
                            $map,
                            "columns" => "id,pid,title,url,tip",
                            "order" => "sort asc"
                        ))->toArray();
                       
                        //$menuList = M(C("BASE_DB_NAME").".".'Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                        foreach($menuList as &$value){
                            if($value['id'] == $nav[1]['id']){
                                $value["class"] = "active";
                            }
                        }
                        $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                    }

                    if($menus['child'] === array()){
                        //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                    }
                }
            }

            // session('ADMIN_MENU_LIST'.$controller,$menus);
        }
        return $menus;
    }
    
    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=Wpf\App\Admin\Models\AdminAuthrule::RULE_URL, $mode='url'){
        if(IS_ADMIN_ROOT){
            return true;//管理员允许访问任何页面
        }
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \Auth();
        }

        
        if(!$Auth->check($rule,ADMIN_UID,$type,$mode)){
            return false;
        }
        return true;
    }
    
    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){
        if(IS_ADMIN_ROOT){
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
    }
    
    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        if(IS_ADMIN_ROOT){
            return true;//管理员允许访问任何页面
        }
        $controller = CONTROLLER_CLASS;
        
        if(in_array($controller,self::$allow_class)){
            return true;   //全局允许的控制器
        }
        
        
        
        if ( !is_array($controller::$deny)||!is_array($controller::$allow) ){
            $this->error("内部错误:{$controller}控制器 deny和allow属性必须为数组");
        }
        $deny  = $this->getDeny();
        $allow = $this->getAllow();
        if ( !empty($deny)  && in_array(ACTION_NAME,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array(ACTION_NAME,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }
    
    /**
     * 获取控制器中允许禁止任何人(超管除外)通过url访问的方法
     * @param  string  $controller   控制器类名(不含命名空间)
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final static protected function getDeny($controller=CONTROLLER_CLASS){
        $data       =   array();
        if ( is_array( $controller::$deny) ) {
            $deny   =   array_merge( $controller::$deny, self::$deny );
            foreach ( $deny as $key => $value){
                if ( is_numeric($key) ){
                    $data[] = strtolower($value);
                }else{
                    //可扩展
                }
            }
        }
        return $data;
    }
    
    /**
     * 获取控制器中允许所有管理员通过url访问的方法
     * @param  string  $controller   控制器类名(不含命名空间)
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final static protected function getAllow($controller=CONTROLLER_CLASS){
        $data       =   array();
        if ( is_array( $controller::$allow) ) {
            $allow  =   array_merge( $controller::$allow, self::$allow );
            foreach ( $allow as $key => $value){
                if ( is_numeric($key) ){
                    $data[] = strtolower($value);
                }else{
                    //可扩展
                }
            }
        }
        return $data;
    }
    
    
    public function isAdministrator($uid = null){
        $uid = is_null($uid) ? ADMIN_UID : $uid;
        return $uid && (in_array(intval($uid),$this->config->ADMIN_ADMINISTRATOR->toArray()));
    }
    
    
    public function isLogin(){
        if ($this->cookies->has($this->config->cookie_name->WPF_ADMIN_AUTH)) {
            $rememberMe = $this->cookies->get($this->config->cookie_name->WPF_ADMIN_AUTH);
            $value = $rememberMe->getValue();
            $value = unserialize($value);
            
            if($value['id']){
                return $value;
            }else{
                $this->cookies->delete($this->config->cookie_name->WPF_ADMIN_AUTH);
                return false;
            }
        }else{
            return false;
        }

    }
    
}