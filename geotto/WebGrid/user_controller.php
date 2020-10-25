<?php
/*
 * 该脚本用于提供用户访问功能
 * */
include_once("controller.php");

class UserController extends Controller{
  function __construct($modelPath, $viewPath, $dbc=null){
    parent::__construct($modelPath, $viewPath, $dbc);
    
    define("PASSWORD_LEN", 6);
    define("INCLUDES", "includes");
      define("STORAGE", "storage");
      define("MAX_BACKGROUND_SIZE", 1*1024*1024);
    
    session_start();
  }
  
  //注册页面
  function register(){
    $page = "注册-WebGrid";
    include(VIEW_PATH."/user_register.php");
  }
  
  //执行注册
  function doRegister(){
    //加载模型
    $user = $this->loadModel("User", "User");
    $syslogModel = $this->loadModel("SysLog", "SysLog");
    $desktopModel = $this->loadModel("Desktop", "Desktop");
    
    //获取参数
    $name = $this->getPost('name', true);
    $password = $this->getPost('password', true);
    
    //检测参数
    if(strlen($password) < PASSWORD_LEN){
      $msg = new Message(Message::ARG_ERROR, "密码长度有误，请重新输入");
      echo $msg->form();
      return;
    }
    
    $exists = $user->exists($name);
    if($exists){
      $msg = new Message(Message::ALREADY_EXISTS, "使用的用户名已被注册");
      echo $msg->form();
      return;
    }
    
    try{
      $id = $user->register($name, $password);
    }catch(Exception $e){
      $msg = new Message(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage());
      $syslogModel->log($msg);
      echo $msg->form();
      return;
    }
    
    //添加桌面
    try{
      $desktop_id = $desktopModel->addDesktop($id);
      $user->addDesktop($id, $desktop_id);
    }catch(Exception $e){
      $msg = new Message(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage());
      $syslogModel->log($msg);
      echo $msg->form();
      return;
    }
    
    $msg = new Message(Message::SUCCESS, "操作成功");
    echo $msg->form();
  }
  
  //主页面
  function index(){
    $data = array();

    $categoryModel = $this->loadModel("Category", "Category");
    $userModel = $this->loadModel("User", "User");
    
    $user_id = $this->logged();
    
    //获取分类
    $categories = $categoryModel->getCategories();
    $data['categories'] = $categories;
    //获取桌面
    if($user_id){        
        $data['user_name'] = $_SESSION["name"];
        try{
            $desktops = $userModel->getDesktops($user_id);
            $data['desktops'] = $desktops;
        }catch(Exception $e){
            $msg_content =  "执行操作时发生错误，我们对此感到十分抱歉";
            $msg = new Message(Message::ERROR, $msg_content, $e->getMessage());
            $syslogModel->log($msg);

            $data['level'] = LEVEL_DANGER;
            $data['content'] = $msg_content;
            $this->loadView("show_message", $data);
            return;
        }
    }
    
    $this->loadView("user_index", $data);
  }
  
  //登录
  function doLogin(){
    //获取参数
    $name = $this->getPost("name", true);
    $password = $this->getPost("password", true);
    $keep_signed = $this->getPost("keep_signed", true);
    
    $userModel = $this->loadModel("User", "User");
    $user = $userModel->login($name, $password);
    if($user == null){
		$msg = new Message(Message::ARG_ERROR, "用户名或密码错误，请重新输入");
		echo $msg->form();
      return;
    }
    
    //保持登录状态
    if($keep_signed == "yes"){
		setcookie("key", sha1($password), time() + 3600 * 24 * 7);
		setcookie("id", $user['id'], time() + 3600 * 24 * 7);
	}
    
    $this->setSession($user);
    $msg = new Message(Message::SUCCESS, "登录成功");
    echo $msg->form();
  }
    
    //用户信息
    function me(){
        $data = array();
        $data['page'] = "用户中心-WebGrid";
        
        //登录验证
        $user_id = $this->logged();
        if(!$user_id){
            $data['level'] = LEVEL_WARNING;
            $data['content'] = "您尚未登录，请登录后再试";
            $data['jump'] = "index.php?fun=login";
            $this->loadView("show_message", data);
            return;
        }
        
        $data['id'] = $user_id;
        $data['name'] = $_SESSION['name'];
        $data['background'] = $_SESSION['background'];
        $this->loadView("user_me", $data);
    }
    
    //获取控件列表
    function widgetlist(){
		$data = array();
		
		$widgetModel = $this->loadModel("Widget", "Widget");
		$widgets = $widgetModel->getWidgets("name");
		$data['widgets'] = $widgets;
		$this->loadView("user_widgetlist", $data);
	}
  
  //获取用户控件列表
  function mywidgets(){
	  $data = array();
	  
		//登录验证
		$user_id = $this->logged();
		if(!$user_id){
			echo "您还没有登录哦，亲";
			return;
		}
		
		$userwidgetModel = $this->loadModel("UserWidget", "UserWidget");
		$widgetModel = $this->loadModel("Widget", "Widget");
		
		$widget_ids = $userwidgetModel->getWidgets($user_id);
		if($widget_ids != null){
			$widgets = array();
			foreach($widget_ids as $widget_id){
				$widgets[] = $widgetModel->getWidget($widget_id);
			}
		}else{
			$widgets = null;
		}
		
		$data['widgets'] = $widgets;
		$this->loadView("user_mywidgets", $data);
	}
  
  //根据分类获取网址
  function getSitesByCategory(){
    $category = $this->getPost("category", true);
    
    $siteModel = $this->loadModel("Site", "Site");
    $sites = $siteModel->getSitesByCategory($category, 0, ITEM_COUNT);
    if($sites == null){
      $msg = new Message(Message::NONE, "暂无记录");
      echo $msg->form();
      return;
    }
    
    $rows = array();
    foreach($sites as $site){
      $rows[] = implode(SEP_II, $site);
    }
    $msg = new Message(Message::SUCCESS, implode(SEP_I, $rows));
    echo $msg->form();
  }
  
  //获取用户编号
  function logged(){
    return isset($_SESSION['id'])?$_SESSION['id']:false;
  }

  //添加网址
    function addSites(){
        //查看是否登录
        $user_id = $this->logged();
        if(!$user_id){
            $msg = new Message(Message::NOT_LOGGED, "您需要登录后才能添加哦");
            echo $msg->form();
            return;
        }
        
        //获取提交数据
        $desktop = $this->getPost("desktop", true);
        $str_sites = $this->getPost("str_sites", true);
        
        if(!$desktop || !$str_sites){
            $msg = new Message(Message::ARG_ERROR, "您访问的页面有误");
            echo $msg->form();
            return;
        }
        
        $siteModel = $this->loadModel("Site", "Site");
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        $syslogModel = $this->loadModel("SysLog", "SysLog");
        
        //添加
        try{
            $sites = explode(SEP_I, $str_sites);
            foreach($sites as $site){
                $desktopModel->addSite($desktop, $site, -1);
                $siteModel->stepPop($site);
            }
            $msg = new Message(Message::SUCCESS, "添加网址成功！");
            echo $msg->form();
        }catch(Exception $e){
            $msg_content = "执行操作时发生错误，我们对此感到十分抱歉";
            $msg = new Message(Message::ERROR, $msg_content, $e->getMessage());
            $syslogModel->log($msg);
            echo $msg->form();
        }
    }
    
    //获取桌面信息
    function getDesktopContent(){
        $data = array();
        $data['page'] = "桌面内容";
            
        $userModel = $this->loadModel("User", "User");
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        
        //验证登录
        $user_id = $this->logged();
        if(!$user_id){
            //加载默认桌面
            $site_ids = $desktopModel->getSites(1);
        }else{
            $desktop = $this->getPost("desktop", true);
            if($desktop === false){
                echo "您访问的页面不存在";
                return;
            }
            
            $desktops = $userModel->getDesktops($user_id);
            if($this->array_index($desktop, $desktops) == -1){
                echo "您访问的页面不存在";
                return;
            }

            $site_ids = $desktopModel->getSites($desktop);
        }
        
        $syslogModel = $this->loadModel("SysLog", "SysLog");
        $siteModel = $this->loadModel("Site", "Site");
            
        try{
            //获取内容
            $sites = array();
            foreach($site_ids as $site_id){
                $sites[] = $siteModel->getSite($site_id);
            }

            $data['sites'] = $sites;
            $this->loadView("user_showdesktop", $data);
        }catch(Exception $e){
            $msg_content = "执行操作时发生错误，我们对此感到十分抱歉";
            $msg = new Message(Message::ERROR, $msg_content, $e->getMessage());
            $syslogModel->log($msg);
            echo $msg_content;
            return;
        }
    }
    
    //搜索网址
    function execSearchSite(){
        //获取参数
        $keyword = $this->getPost("keyword", true);
        if(!$keyword || trim($keyword) == ""){
            $this->sendMessage(Message::ARG_ERROR, "请填写关键词");
            return;
        }
        
        $siteModel = $this->loadModel("Site", "Site");
        $sites = $siteModel->search($keyword);
        if($sites == null){
            $this->sendMessage(Message::NONE, "暂无记录");
            return;
        }
    
        $rows = array();
        foreach($sites as $site){
          $rows[] = implode(SEP_II, $site);
        }
        $this->sendMessage(Message::SUCCESS, implode(SEP_I, $rows));
    }
    
    //获取推荐内容
    function execGetPopSites(){
        $siteModel = $this->loadModel("Site", "Site");
        $sites = $siteModel->getPopSites(ITEM_COUNT);
        if($sites == null){
            $this->sendMessage(Message::NONE, "暂无记录");
            return;
        }
    
        $rows = array();
        foreach($sites as $site){
          $rows[] = implode(SEP_II, $site);
        }
        $this->sendMessage(Message::SUCCESS, implode(SEP_I, $rows));
    }
    
    //删除桌面图标
    function execDelIcon(){
        //登录验证
        $user_id = $this->logged();
        if(!$user_id){
            $this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
            return;
        }
        
        //获取参数
        $desktop = $this->getPost("desktop", true);
        $index = $this->getPost("index", true);
        if($desktop === false || $index === false){
            $this->sendMessage(Message::ARG_ERROR, "您访问的页面不正确");
            return;
        }
        
        $desktopModel = $this->loadModel("Desktop", "Desktop");
        
        try{
            //执行操作
            $desktopModel->removeSite($desktop, $index);
            $this->sendMessage(Message::SUCCESS, "操作成功");
        }catch(Exception $e){
            $this->sendMessage(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage(), true);
        }
    }
    
    //注销
    function execLogout(){
        session_destroy();
        $this->sendMessage(Message::SUCCESS, "退出成功！");
    }
    
    //修改密码
    function execChangePassword(){
        //登录验证
        $user_id = $this->logged();
        if(!$user_id){
            $this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
            return;
        }
        
        //获取参数
        $new_password = $this->getPost("new_password", true);
        $old_password = $this->getPost("old_password", true);
        
        if(!$old_password || !$new_password){
            $this->sendMessage(Message::ARG_ERROR, "您访问的页面存在错误");
            return;
        }
        
        $new_password = trim($new_password);
        $userModel = $this->loadModel("User", "User");
        
        try{
            $result = $userModel->changePassword($user_id, $old_password, $new_password);
            
            if($result == false){
                $this->sendMessage(Message::NONE, "您输入的原密码有误，请重新输入");
            }else{
                $this->sendMessage(Message::SUCCESS, "操作成功！");
            }
        }catch(Exception $e){
            $this->sendMessage(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage(), true);
        }
    }
    
    //更换背景
    function execChangeBackground(){
        $data = array();
        $data['page'] = "更换背景-WebGrid";
        
        //登录验证
        $user_id = $this->logged();
        if(!$user_id){
            $data['level'] = LEVEL_INFO;
            $data['content'] = "您尚未登录，请登录后再试";
            $data['jump'] = "index.php";
            
            $this->loadView("show_message", $data);
            return;
        }
        
        //获取参数
        $msg_content = "";
        $file = $_FILES['background_file'];
        if(!$file){
            $msg_content = "请选择要上传的文件";
        }elseif($file['error'] > 0){
            switch($file['error']){
                case UPLOAD_ERR_INI_SIZE:
                $msg_content = "上传文件大小超出服务器限制";
                break;
                
                case UPLOAD_ERR_FORM_SIZE:
                $msg_content = "上传文件大小超出限制";
                break;
                
                case UPLOAD_ERR_PARTIAL:
                $msg_content = "没有成功上传文件";
                break;
                
                case UPLOAD_ERR_NO_FILE:
                $msg_content = "没有文件被上传";
                break;
            }
        }elseif($file['size'] > MAX_BACKGROUND_SIZE){
            $msg_content ="文件大小不能超过1M";
        }else{
            $sufix = array_search($file['type'], $this->getBackgroundTypes());
            if(!$sufix){
                $msg_content = "文件类型有误";
            }
        }
        
        if($msg_content != ""){
            $data['level'] = LEVEL_WARNING;
            $data['content'] = $msg_content;
            $data['jump'] = "index.php?fun=me";
            
            $this->loadView("show_message", $data);
            return;
        }
        
        //保存文件        
        $file_name = $this->getRandStr(12).".".$sufix;
        $this->saveFile(STORAGE."/backgrounds", $file['tmp_name'], $file_name);
        
        try{
            //更新数据库
            $userModel = $this->loadModel("User", "User");
            $userModel->setBackground($user_id, $file_name);
            
            $_SESSION['background'] = $file_name;
            
            $data['level'] = LEVEL_SUCCESS;
            $data['content'] = "更改背景成功";
            $data['jump'] = "index.php?fun=me";            
            $this->loadView("show_message", $data);
        }catch(Exception $e){
            $data['level'] = LEVEL_DANGER;
            $data['content'] = "执行操作时发生错误，我们对此感到十分抱歉";
            $data['jump'] = "index.php?fun=me";
            
            $this->loadView("show_message", $data);
        }
    }

	//添加桌面
	function execAddDesktop(){
		//验证登录状态
		$user_id = $this->logged();
		if(!$user_id){
			$this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
			return;
		}
		
		$desktopModel = $this->loadModel("Desktop", "Desktop");
		$userModel = $this->loadModel("User", "User");
		
		try{
			$desktop = $desktopModel->addDesktop($user_id, false);
			$userModel->addDesktop($user_id, $desktop);
			$this->sendMessage(Message::SUCCESS, "添加桌面成功");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage(), true);
		}
	}
	
	//删除桌面
	function execDelDesktop(){
		//检测登录状态
		$user_id = $this->logged();
		if(!$user_id){
			$this->sendMessage(Message::NOT_LOGGED, "您尚未登录，请登录后再试");
			return;
		}
		
		//获取参数
		$desktop = $this->getPost("desktop", true);
		if(!$desktop){
			$this->sendMessage(Message::ARG_ERROR, "您访问的页面存在错误");
			return;
		}
		
		$userModel = $this->loadModel("User", "User");
		$desktopModel = $this->loadModel("Desktop", "Desktop");
		
		//权限检测
		$desktops = $userModel->getDesktops($user_id);
		$index = $this->array_index($desktop, $desktops);
		if($index == -1){
			$this->sendMessage(Message::NOT_PERMITTED, "您没有相关权限进行此操作");
			return;
		}
		
		if(count($desktops) <= 1){
			$this->sendMessage(Message::NOT_PERMITTED, "只剩下一个桌面了，不能再删了T^T");
			return;
		}
		
		//执行操作
		try{
			$userModel->delDesktop($user_id, $desktop);
			$desktopModel->removeDesktop($desktop, $user_id);
			$this->sendMessage(Message::SUCCESS, "删除桌面成功！");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage(), true);
		}
	}
	
	//添加控件
	function execAddWidget(){
		//检测登录状态
		$user_id = $this->logged();
		if(!$user_id){
			$this->sendMessage(Message::NOT_LOGGED, "您还没有登录哦，亲");
			return;
		}
		
		//获取参数
		$widget = $this->getPost("widget", true);
		if(!$widget){
			$this->sendMessage(Message::ARG_ERROR, "您访问的页面有错误");
			return;
		}
		
		try{
			$userwidgetModel = $this->loadModel("UserWidget", "UserWidget");
			
			//查看是否已经添加
			$exists = $userwidgetModel->exists($user_id, $widget);
			if($exists){
				$this->sendMessage(Message::ALREADY_EXISTS, "您已经添加过了哦");
				return;
			}
			
			$userwidgetModel->addWidget($user_id, $widget);			
			$this->sendMessage(Message::SUCCESS, "添加控件成功");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "不好意思，出错了～", $e->getMessage(), true);
		}
	}
	
	//移除控件
	function execRemoveWidget(){
		$user_id = $this->logged();
		if(!$user_id){
			$this->sendMessage(Message::NOT_LOGGED, "需要登录才能移除哦");
			return;
		}
		
		//获取参数
		$widget = $this->getPost("widget", true);
		if(!$widget){
			$this->sendMessage(Message::ARG_ERROR, "您访问的页面不正确");
			return;
		}
		
		try{
			$userwidgetModel = $this->loadModel("UserWidget", "UserWidget");
			$userwidgetModel->removeWidget($user_id, $widget);
			
			$this->sendMessage(Message::SUCCESS, "操作成功！");
		}catch(Exception $e){
			$this->sendMessage(Message::ERROR, "执行操作时发生错误，我们对此感到十分抱歉", $e->getMessage(), true);
		}
	}
	
	//执行自动登录
	function execSignIn(){
		//获取参数
		$id = $this->getPost("id", true);
		$key = $this->getPost("key", true);
		
		if(!$id || !$key){
			$this->sendMessage(Message::ARG_ERROR, "访问的页面不正确");
			return;
		}
		
		$userModel = $this->loadModel("User", "User");
		$user = $userModel->signIn($id, $key);
		if(is_null($user)){
			$this->sendMessage(Message::ARG_ERROR, "数据有误");
			return;
		}
		
		$this->setSession($user);		
		$this->sendMessage(Message::SUCCESS, "登录成功");
	}
	
    //获取背景图片文件类型
    private function getBackgroundTypes(){
        return array(
            'png'=>'image/png',
            'bmp'=>'image/bmp',
            'jpg'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'gif'=>'image/gif'
        );
    }
    
    //设置session
    private function setSession($user){      
		$_SESSION['id'] = $user['id'];
		$_SESSION['name'] = $user['name'];
		$_SESSION['background'] = $user['background'];
	}
}
?>
