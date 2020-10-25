<?php
/*
 * 该类作为基础的控制器类使用
 * */

class Controller{
  private $dbc;
  
  function __construct($modelPath, $viewPath, $dbc=null){
    define("MODEL_PATH", $modelPath);
    define("VIEW_PATH", $viewPath);
    
    define("SUCCESS", 0);
    define("NOT_LOGGED", -1);
    define("NOT_PERMITTED", -2);
    define("ARG_ERROR", -3);
    define("ALREADY_EXISTS", -4);
    define("ERROR", -5);
    
    define("SEP_I", "[sep1]");	//记录分隔符
    define("SEP_II", "[sep2]");	//字段分割符
    
    define("ITEM_COUNT", 20);	//显示条目数

    define("LEVEL_SUCCESS", 1);
    define("LEVEL_INFO", 2);
    define("LEVEL_WARNING", 3);
    define("LEVEL_DANGER", 4);
    
    $this->dbc = $dbc;
  }

  //设定数据库连接
  function setDbc($dbc){
    if($this->dbc != null){
      mysqli_close($this->dbc);
    }
    
    $this->dbc = $dbc;
  }
  
  //获取参数
  function getPost($name, $escape){
    $postVal = isset($_POST[$name])?$_POST[$name]:false;
    if($postVal){
      //过滤
      $postVal = mysqli_real_escape_string($this->dbc, $postVal);
      if($escape){
	$postVal = strip_tags($postVal);
	//$postVal = htmlentities($postVal);
      }
    }
    
    return $postVal;
  } 
  
  //加载模型
  function loadModel($file, $name){
    include_once(MODEL_PATH."/{$file}.php");
    $model = new $name($this->dbc);
    
    return $model;
  }

  //加载视图
  function loadView($file, $data){
    extract($data);
    include(VIEW_PATH."/{$file}.php");
  }

  //默认页面
  function index(){
    include(VIEW_PATH."/404.html");
  }
    
    //发送消息
    function sendMessage($no, $content, $detail=null, $logit=false){        
        $msg = new Message($no, $content, $detail);
        if($logit){
            $syslogModel = $this->loadModel("SysLog", "SysLog");
            $syslogModel->log($msg);
        }
        
        echo $msg->form();
    }
    
    //保存文件
    protected function saveFile($path, $src, $dest){
        move_uploaded_file($src, $path."/".$dest);
        if(file_exists($src) && is_file($src)){
            unlink($src);
        }
    }
    
    //获取随机字符串
    function getRandStr($len){
        $source = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $max_index = strlen($source);
        
        $output = "";
        for($i=0;$i<$len;$i++){
            $index = rand(0, $max_index);
            $output .= substr($source, $index, 1);
        }
        
        return $output;
    }
    
    //在数组中搜索
    function array_index($elem, $array){
        for($i=0;$i<count($array);$i++){
            if($elem == $array[$i]){
                return $i;
            }
        }
        
        return -1;
    }
}

//服务器-浏览器通讯类
class Message{
  const SUCCESS = 0;
  const ERROR = -1;
  const NOT_LOGGED = -2;
  const NOT_PERMITTED = -3;
  const ARG_ERROR = -4;
  const ALREADY_EXISTS = -5;
  const NONE = -6;
  
  private $no;//消息编号
  private $content;//通讯内容
  private $detail;//通讯详情
  private $generateTime;//生成时间
  
  function __construct($no, $content, $detail=null){
    $this->no = $no;
    $this->content = strip_tags($content);
    $this->detail = $detail;
    $this->generateTime = date("Y-m-d H:i:s");
  }
  
  //获取编号
  function getNo(){
    return $this->no;
  }
  
  //获取通讯内容
  function getContent(){
    return $this->content;
  }
  
  //获取通讯详情
  function getDetail(){
    return $this->detail;
  }
  
  function getGenerateTime(){
    return $this->generateTime;
  }
  
  //生成格式化字符串
  function form(){
	  $array = array(
		"no"=>$this->no,
		"content"=>$this->content,
		"generateTime"=>$this->generateTime
	  );
	  
	return json_encode($array);
    //return $this->no.Message::MSG_SEP.$this->content.Message::MSG_SEP.$this->generateTime;
  }
}
?>
