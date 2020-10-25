<?php

class ECDataSet
{
  public $option=false;
  protected $data;
  protected $d;

  protected $xl=array();
  protected $series="";
  protected $nowxl="";



  function __construct(&$option){
    $this->clean();
    $this->bind($option);
    $this->init();
  }
  function init(){}
  function bind(&$option){
    $this->option=$option;
  }

  function set_data($data){
    $this->data=$data;
  }
  function get_data($data){
    return $this->data;
  }
  function add_data($data){
    $this->data=array_merge($this->data,$data);
  }
  function add($data){
    if (empty($this->xl)) $this->addXL("值");
    if (isset($data[1])){

      if (!isset($this->d[$this->nowxl][$data[0]])) $this->d[$this->nowxl][$data[0]]=array();
      // $this->d[$this->nowxl][$data[0]][]=array(
      //   "value"=>$data[1],
      //   'data'=>(isset($data[2]))?$data[2]:'',
      // );
      $this->d[$this->nowxl][$data[0]][]=$data[1];
    }

  }
  function addXL($name){
    if (!empty($name)){
      $this->nowxl=$name;
      $this->xl[]=$name;
      if (!isset($this->d[$this->nowxl])) $this->d[$this->nowxl]=array();
    }
  }
  function setSeries($data){

    if (!is_array($data)){

      $json=trim($data);
      $json = str_replace(array("\n","\r"),"",$json);
      $json = str_replace("'","\"",$json);
      $json = preg_replace("/([a-zA-Z0-9_]+?):/" , "\"$1\":", $json); // fix variable names
      $json = str_replace(" ","",$json);
      $data=json_decode($json,true);

    }

    $this->series=$data;
  }
  function make_data(){

    if (empty($this->d)) return;

    $series=array();
    $source=array();
    foreach ($this->xl as $xlindex => $xl) {
      $s=$this->series;
      $s['name']=$xl;
      $series[]=$s;

      foreach ($this->d[$xl] as $key => $value) {
        if (!isset($source[$key])) $source[$key]=array($key);

        foreach ($value as $d) {
          $source[$key][]=$d;
        }

      }

    }


    $this->data=array(
      'dataset'=>array(
          "dimensions"=>array_merge(array("null"),$this->xl),
          // "source"=>array_merge(
          //   array(
          //     'name'=>array_merge(array("null"),$this->xl),
          //   ),
          // ),
          "source"=>array_values($source),
      ),
      'series'=>$series,

    );
  }
  function clean(){
    $this->data=array();
    $this->d=array();

  }
  function right2left(){
    for ($i=0; $i <$this->d_count ; $i++) {
      $this->d[$i]=array_reverse($this->d[$i]);
    }
  }
  function build(){
    $this->make_data();

    if (!empty($this->data)){
      foreach ($this->data as $key => $value) {
        $this->option->set($key,$value);
      }
    }

  }
}


 ?>
