<?php

class ECData
{
  public $option=false;
  protected $data;
  protected $d;
  protected $d_count=20;


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

    if (isset($data[1])){
      $this->d[0][]=$data[0];
      $this->d[1][]=array(
        "value"=>$data[1],
        'data'=>(isset($data[2]))?$data[2]:'',
      );

    }


  }
  function addXL($name){

  }
  function make_data(){
    if (empty($this->d[0])) return;
    $this->data=array(
      'xAxis'=>array(
          "data"=>$this->d[0],
      ),
      'series'=>array(
        array(
          "data"=>$this->d[1],
        )
      )
    );
  }
  function clean(){
    $this->data=array();
    $this->d=array();
    for ($i=0; $i <$this->d_count ; $i++) {
      $this->d[$i]=array();
    }
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
