<?php
include_once("controller.php");

class AdministratorController extends Controller{
    function __construct($modelPath, $viewPath, $dbc=null){
        parent::__construct($modelPath, $viewPath, $dbc);
        
        define("INCLUDES", "includes");
        define("MAX_ICON_SIZE", 1*1024*1024);
        define("ICON_PATH", "storage/icons");
        define("LOGIN_PAGE", "index.php?file=administrator_controller&class=AdministratorController&fun=login");
        
        session_start();
    }
    
    //登录
    function login(){
        $data = array();
        $data['page'] = "管理员登录-WebGrid";
        
        $submitted = $this->getPost("submitted", true);
        if(!$submitted){
            $this->loadView("administrator_login", $data);
        }else{
            $errors = array();
            
            $name = $this->getPost("name", true);
            $password = $this->getPost("password", true);
            
            if(!$name || trim($name) == ""){
                $errors[] = "请填写用户名";
            }
            if(!$password || trim($password) == ""){
                $errors[] = "请填写密码";
            }
            
            if(count($errors) > 0){
                $data['errors'] = $errors;
                $this->loadView("administrator_login", $data);
                return;
            }
            
            $administratorModel = $this->loadModel("Administrator", "Administrator");
            $admin = $administratorModel->login($name, $password);
            if($admin == null){
                $errors[] = "用户名或密码错误，请重新输入";
                $data['errors'] = $errors;
                $this->loadView("administrator_login", $data);
                return;
            }
            
            $_SESSION['adm_id'] = $admin['id'];
            $_SESSION['adm_name'] = $admin['name'];
            
            $data['level'] = LEVEL_SUCCESS;
            $data['content'] = "登录成功";
            $data['jump'] = "index.php?file=administrator_controller&class=AdministratorController&fun=addSite";
            
            $this->loadView("show_message", $data);
        }
    }
    
    //添加图标页面
    function addIcon(){
        $data = array();
        $data['page'] = "添加图标-WebGrid";
        
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            $data['level'] = LEVEL_INFO;
            $data['content'] = "您尚未登录，请登录后再试";
            $data['jump'] = LOGIN_PAGE;
            
            $this->loadView("show_message", $data);
            return;
        }
        
        //显示界面
        if(!isset($_POST['submitted'])){
            $this->loadView("administrator_addicon", $data);
            return;
        }
        
        //获取参数
        $errors = array();
        $icon_name = $this->getPost("icon_name", true);
        $icon_file = $_FILES['icon_file'];
        
        $icon_name = trim($icon_name);
        
        if($icon_name == ""){
            $errors[] = "请填写图标名称";
        }
        if(!$icon_file){
            $errors[] = "请选择要上传的文件";
        }elseif($icon_file['error'] > 0){
            switch($icon_file['error']){
                case UPLOAD_ERR_INI_SIZE:
                $errors[] = "上传文件大小超出服务器限制";
                break;
                
                case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "上传文件大小超出限制";
                break;
                
                case UPLOAD_ERR_PARTIAL:
                $errors[] = "没有成功上传文件";
                break;
                
                case UPLOAD_ERR_NO_FILE:
                $errors[] = "没有文件被上传";
                break;
            }
        }elseif($icon_file['size'] > MAX_ICON_SIZE){
            $errors[] ="文件大小不能超过1M";
        }else{
            $sufix = array_search($icon_file['type'], $this->getIconTypes());
            if(!$sufix){
                $errors[] = "文件类型有误";
            }
        }
        
        //显示错误
        if(!empty($errors)){
            $data['errors'] = $errors;
            $this->loadView("administrator_addicon", $data);
            return;
        }
        
        //处理上传的文件
        $file_name = $this->getRandStr(12).".".$sufix;
        $this->saveFile(ICON_PATH, $icon_file['tmp_name'], $file_name);
        
        //保存到数据库
        $iconModel = $this->loadModel("Icon", "Icon");
        $iconModel->addIcon($file_name, $icon_name);
        
        $data['level'] = LEVEL_SUCCESS;
        $data['content'] = "上传图标成功";
        $data['jump'] = "index.php?file=administrator_controller&class=AdministratorController&fun=addIcon";
        $this->loadView("show_message", $data);
    }
    
    //添加网址
    function addsite(){
        $data = array();
        $data['page'] = "添加网址-WebGrid";
        
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            $data['level'] = LEVEL_INFO;
            $data['content'] = "您尚未登录，请登录后再试";
            $data['jump'] = LOGIN_PAGE;
            
            $this->loadView("show_message", $data);
            return;
        }
        
        $categoryModel = $this->loadModel("Category", "Category");
        
        //获取分类
        $categories = $categoryModel->getCategories();
        
        //显示界面
        $data['categories'] = $categories;
        $this->loadView("administrator_addsite", $data);
    }
    
    //获取图标列表
    function geticons(){        
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            echo "您尚未登录，请登录后再试";
            return;
        }
        
        //获取参数
        $keyword = $this->getPost("keyword", true);
        if(!$keyword){
            $keyword = null;
        }
        
        $iconModel = $this->loadModel("Icon", "Icon");
        $icons = $iconModel->search($keyword);
        
        $data['icons'] = $icons;
        $this->loadView("administrator_geticons", $data);
    }
    
    //编辑默认桌面
    function editdefaultdesktop(){
        $data = array();
        $data['page'] = "编辑默认桌面-WebGrid";
        
        //检测登录状态
        $adm_id = $this->logged();
        if(!$adm_id){
            $data['level'] = LEVEL_WARNING;
            $data['content'] = "您尚未登录，请登录后再试";
            $data['jump'] = LOGIN_PAGE;
            $this->loadView("show_message", $data);
            return;
        }
        
        $categoryModel = $this->loadModel("Category", "Category");
        
        //获取分类
        $categories = $categoryModel->getCategories();
        $data['categories'] = $categories;
        
        //显示页面
        $this->loadView("administrator_editdefaultdesktop", $data);
    }
    
    //获取默认桌面
    function getdefaultdesktop(){ 
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            echo "您尚未登录，请登录后再试";
            return;
        }
        
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        $siteModel = $this->loadModel("Site", "Site");
        
        $siteIds = $desktopModel->getSites(1);
        if($siteIds != null){
            $sites = array();
            
            foreach($siteIds as $siteId){
                $site = $siteModel->getSite($siteId);
                if($site == null)continue;
                
                $sites[] = $site;
            }
            
            //显示桌面
            $data['sites'] = $sites;
            $this->loadView("administrator_defaultdesktop", $data);
        }else{
            echo "none";
        }
    }
    
    //搜索网址
    function searchsite(){
        $data = array();
        
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            echo "您尚未登录，请登录后再试";
            return;
        }
        
        //获取参数
        $keyword = $this->getPost("keyword", true);        
        $siteModel = $this->loadModel("Site", "Site");        
        $sites = $siteModel->search($keyword);
        
        $data['sites'] = $sites;
        $this->loadView("administrator_showsites", $data);
    }
    
    //根据分类获取网址列表
    function getsitesbycategory(){
        $data = array();
        
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            echo "您尚未登录，请登录后再试";
            return;
        }
        
        $category = $this->getPost("category", true);
        if(!$category){
            echo "您访问的页面有误";
            return;
        }
        
        $siteModel = $this->loadModel("Site", "Site");
        
        $sites = $siteModel->getSitesByCategory($category);
        $data['sites'] = $sites;
        $this->loadView("administrator_showsites", $data);
    }
    
    //管理控件页面
    function widget(){
		$data = array();
		$data['page'] = "管理控件";
		
		//检测登录状态
		$adm_id = $this->logged();
		if(!$adm_id){
			$data['level'] = LEVEL_INFO;
			$data['content'] = "您尚未登录，请登录后再试";
			$data['jump'] = LOGIN_PAGE;
			$this->loadView("show_message", $data);
			return;
		}
		
		$this->loadView("administrator_widget", $data);
	}
    
    //获取控件列表
    function widgetlist(){
		$data = array();
		
		//检测登录状态
		$adm_id = $this->logged();
		if(!$adm_id){
			echo "需要先登录才能看到哦";
			return;
		}
		
		$widgetModel = $this->loadModel("Widget", "Widget");
		$widgets = $widgetModel->getWidgets("name");
		$data['widgets'] = $widgets;
		$this->loadView("administrator_widgetlist", $data);
	}
    
    //执行添加网址操作
    function execAddSite(){     
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            $this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
            return;
        }
        
        //获取参数
        $site_name = $this->getPost("site_name", true);
        $site_url = $this->getPost("site_url", true);
        $site_category = $this->getPost("site_category", true);
        $site_icon = $this->getPost("site_icon", true);
        
        if(!$site_name || !$site_url){
            $this->sendMessage(Message::ARG_ERROR, "您访问的页面不正确");
            return;
        }
        
        $regexp = "/^(http|https|ftp):\/\//";
        $site_name = trim($site_name);
        $site_url = trim($site_url);
        if($site_name == ""){
            $this->sendMessage(Message::ARG_ERROR, "请填写网址名称");
            return;
        }
        if(!preg_match($regexp, $site_url)){
            $this->sendMessage(Message::ARG_ERROR, "填写的网址格式有误，请重新填写");
            return;
        }
        
        $site_category = ($site_category == -1)?null:$site_category;
        
        $iconModel = $this->loadModel("Icon", "Icon");
        $siteModel = $this->loadModel("Site", "Site");
        
        //获取图标链接
        $icon_url = $iconModel->getUrl($site_icon);
        try{
            //添加网址
            $siteModel->addSite($site_name, $site_url, $site_category, $icon_url);
            $this->sendMessage(Message::SUCCESS, "操作成功！");
        }catch(Exception $e){
            $this->sendMessage(Message::ERROR, "执行操作时发生错误！", $e->getMessage(), true);
        }
    }
    
    //添加到默认桌面
    function execAddToDefaultDesktop(){   
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            $this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
            return;
        }
        
        //获取参数
        $str_sites = $this->getPost("str_sites", true);
        if(!$str_sites){
            $this->sendMessage(Message::ARG_ERROR, "您访问的页面存在错误");
            return;
        }
        
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        
        try{
            $sites = explode(SEP_I, $str_sites);
            foreach($sites as $site){
                if(trim($site) == "")
                    break;
                
                $desktopModel->addSite(1, $site, -1);
            }
            
            $this->sendMessage(Message::SUCCESS, "操作成功！");
        }catch(Exception $e){
            $this->sendMessage(Message::ERROR, "执行操作时发生错误！", $e->getMessage(), true);
        }
    }
    
    //删除默认桌面的图标
    function execDelIcon(){  
        //登录状态验证
        $adm_id = $this->logged();
        if(!$adm_id){
            $this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
            return;
        }
        
        //获取参数
        $str_icons = $this->getPost("str_icons", true);
        if($str_icons === false){
            $this->sendMessage(Message::ARG_ERROR, "您访问的页面存在错误");
            return;
        }
        
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        
        try{
            $icons = explode(SEP_I, $str_icons);
            foreach($icons as $icon){
                if($icon == "")
                    continue;
                
                $desktopModel->removeSite(1, $icon);
            }
            
            $this->sendMessage(Message::SUCCESS, "操作成功！");
        }catch(Exception $e){
            $this->sendMessage(Message::ERROR, "执行操作时发生错误！".$e->getMessage(), $e->getMessage(), true);
        }
    }
    
    //添加控件
    function execAddWidget(){
		//验证登录状态
		$adm_id = $this->logged();
		if(!$adm_id){
			$this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
			return;
		}
		
		//获取参数
		$widget_name = $this->getPost("widget_name", true);
		$widget_link = $this->getPost("widget_link", true);
		$widget_height = $this->getPost("widget_height", true);		
		if(!$widget_name || !$widget_link){
			$this->sendMessage(Message::ARG_ERROR, "请填写控件名称或页面地址");
			return;
		}		
		$widget_name = trim($widget_name);
		$widget_link = trim($widget_link);
		
		if($widget_height){			
			$widget_height = trim($widget_height);
			if(!is_numeric($widget_height)){
				$this->sendMessage(Message::ARG_ERROR, "控件高度必须为整数");
				return;
			}
		}else{
			$widget_height = null;
		}
		
		$widgetModel = $this->loadModel("Widget", "Widget");
		
		//添加控件
		try{
			$widgetModel->addWidget($widget_name, $widget_link, $widget_height);
			$this->sendMessage(Message::SUCCESS, "操作成功");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "执行操作时发生错误！", $e->getMessage(), true);
		}
	}
    
    //删除控件
    function execDelWidget(){
		//判断登录状态
		$adm_id = $this->logged();
		if(!$adm_id){
			$this->sendMessage(Message::NOT_LOGGED, "您还没有登录哦，亲");
			return;
		}
		
		//获取参数
		$str_widgets = $this->getPost("str_widgets", true);
		$widgets = explode(SEP_I, $str_widgets);
		
		$userwidgetModel = $this->loadModel("UserWidget", "UserWidget");
		$widgetModel = $this->loadModel("Widget", "Widget");
		
		try{
			foreach($widgets as $widget){
				if($widget == "")
					continue;
				
				$userwidgetModel->delWidget($widget);
				$widgetModel->delWidget($widget);
			}
			
			$this->sendMessage(Message::SUCCESS, "控件被删除了");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "执行操作时发生错误！", $e->getMessage(), true);
		}
	}
	
    //获取图标文件类型
    private function getIconTypes(){
        return array(
            'png'=>'image/png',
            'bmp'=>'image/bmp',
            'jpg'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'gif'=>'image/gif'
        );
    }
    
    //判断是否登录
    private function logged(){
        return isset($_SESSION['adm_id'])?$_SESSION['adm_id']:false;
    }
}
?>
