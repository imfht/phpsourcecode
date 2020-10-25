<?php

class Chart_Gauge extends ECData
{
  public $max;
  function init(){
    $this->option->clean("dataZoom");
    $this->option->set("tooltip",'
    {
        formatter: "{a} <br/>{b} : {c}%"
    }
    ');
    $this->option->set("series","
    [
        {
            name: '百分比',
            type: 'gauge',
            detail: {formatter:'{value}%'},
            data: [0]
        }
    ]
    ");

  }
  function add($data){
    if (isset($data[1])){
      $this->d[0][]=array(
        'name'=>$data[0],
        'value'=>$data[1],
        'data'=>(isset($data[2]))?$data[2]:'',
      );
      $this->max=($data[1]<=100)?"100":ceil($data[1]/10)*10;
    }

  }
  function make_data(){
    if (empty($this->d[0])) return;
    
    $this->data=array(
      'series'=>array(
        array(
          "max"=>$this->max,
          "data"=>$this->d[0],
        )
      )
    );
  }
}

 ?>
