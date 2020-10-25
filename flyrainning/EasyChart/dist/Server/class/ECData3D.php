<?php

class ECData3D extends ECData
{
  public $max_value=0;
  function add($data){

    if (isset($data[2])){
      $this->d[0][]=$data[0];
      $this->d[1][]=$data[1];
      $this->d[2][]=$data[2];
      $this->d[3][]=(isset($data[3]))?$data[3]:'';


    }

  }
  function addXL($name){

  }
  function make_data(){
    if (empty($this->d[0])) return;

    //处理数据
    $tmp=array();
    foreach ($this->d[0] as $key => $value) {
      $tmp[]=array(
        "value"=>array(
          $this->d[0][$key],
          $this->d[1][$key],
          $this->d[2][$key],
        ),
        "data"=>$this->d[3],

      );
      $this->max_value=max($this->max_value,$this->d[2][$key]);

    }

    //去掉重复的x,y
    $x = array_values(array_flip(array_flip($this->d[0])));
    $y = array_values(array_flip(array_flip($this->d[1])));

    //打包数据
    $this->data=array(
      'visualMap'=>array(
        'max'=>$this->max_value,
      ),
      'xAxis3D'=>array(
          "data"=>$x,
      ),
      'yAxis3D'=>array(
          "data"=>$y,
      ),
      'series'=>array(
        array(
          "data"=>$tmp,
        ),

      )
    );
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
