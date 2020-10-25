<?php
/**
 *
 */
 // function customError($errno, $errstr, $errfile, $errline){
 //  echo "<b>Custom error:</b> [$errno] $errstr<br />";
 //  echo " Error on line $errline in $errfile<br />";
 //  echo "Ending Script";
 //  die();
 // }
 // set_error_handler("customError");

class EasyChart
{
  protected $typename;
  public $data=false;
  public $option=false;
  public $config;


  function __construct($type=""){
    global $EC_config;

    $this->config=(empty($EC_config))?array():$EC_config;

    if (!empty($this->config['debug'])){
      if ($this->config['debug']){
        set_error_handler("EasyChart::customError");
      }
    }
    if (empty($type)){
      if (empty($this->config['type'])){
        $type="none";
      }else{
        $type=$this->config['type'];
      }
    }
    $this->type($type);



  }
  function title($title='',$subtitle='',$x="left"){
    $this->option->set("title","
    {
            text: '$title',
            subtext: '$subtitle',
    }
     ");
  }
  function zoom($enable=true){
    $en=($enable)?"true":"false";
    $this->option->set("dataZoom","
    {
        show: $en,
        start : 0
    }
     ");
  }
  function padding($left="60",$right="60",$top="60",$bottom="60"){
    $this->option->set("grid","
    {
        left: '$left',
        right: '$right',
        top: '$top',
        bottom: '$bottom',
        containLabel: true
    }
     ");
  }
  function toolbox($conf=""){
    $this->option->set("toolbox","
    {
          show : true,
          feature : {
              mark : {show: true},
              dataView : {show: true, readOnly: false},
            //  magicType : {show: true, type: ['line', 'bar']},
              restore : {show: true},
              saveAsImage : {show: true},
              $conf
          }
      }
     ");
  }
  function type($type='none'){
    $this->typename=ucfirst($type);
    if (!empty($this->typename)){
      $classname='Chart_'.$this->typename;
      $this->option=new ECOption();
      if (!empty($this->config['default'])){
        foreach ($this->config['default'] as $key => $value) {
          $this->option->set($key,$value);
        }
      }
      $this->data=new $classname($this->option);
    }

  }
  function xl($name){
    $this->data->addXL($name);
  }
  function wd($name){
    $this->data->addXL($name);
  }
  function add(){
    $args = func_get_args();
    $this->data->add($args);
  }
  function set($name,$value){
    $this->option->set($name,$value);
  }
  function setJS($_js){
    $this->option->setJS($_js);
  }
  function add_data($data){
    $this->data->add_data($data);
  }
  function clean(){
    $this->data->clean();
  }
  function right2left(){
    $this->data->right2left();
  }
  static function error($msg){
    self::apiout(array(
      "result"=>false,
      "type"=>"error",
      "data"=>$msg
    ));

  }
  static function customError($errno, $errstr, $errfile, $errline){
   echo "<b>Custom error:</b> [$errno] $errstr<br />";
   echo " Error on line $errline in $errfile<br />";
   echo "Ending Script";
   die();
  }
  static function apiout($data){

    header('Cache-Control: no-cache, must-revalidate');
		header("Pragma: no-cache");
    header('Content-type: application/json');

    echo json_encode($data);
    die();
  }
  static function strout($data){
    return json_encode($data);
  }
  static function getAPI($key="api"){
    $api=self::getVar("EC_api","");
    if (empty($api)) $api=self::getVar($key,"");
    return $api;
  }
  static function server($dir="",$stop=false){
    $_hash=self::getAPI();

    if (empty($_hash)){
      if ($stop){
        self::error('没有指定API');
      }else{
        return;
      }
    }

    $_file=strtr($_hash,array('.' => '/')).'.php';
    $_file=rtrim($dir,'/').$_file;

    if (file_exists($_file)) {
    	require($_file);
    }else{
    	EasyChart::error('找不到API');
    }
  }
  static function getVar($name,$default=""){
    return (isset($_REQUEST[$name]))?$_REQUEST[$name]:$default;
  }
  static function getP($parmstr){
		$str=is_array($parmstr)?$parmstr:explode(',',trim($parmstr,','));
		foreach ($str as $ks){
			$k=trim($ks);
			global ${$k};
			${$k}=self::getVar($k);
		}
	}
  function out($to_str=false){
    $this->data->build();
    $out=array(
      'result'=>true,
      'type'=>"option",
      'data'=>$this->option->parseJSFunction(),
    );

    if ($to_str){
      return self::strout($out);
    }else{
      self::apiout($out);
    }

  }

}


?>
