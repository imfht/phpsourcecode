<?php

class Chart_Pie extends ECData
{
  function init(){
    $this->option->clean("dataZoom");
    $this->option->set("tooltip","
    {
        trigger: 'item',
        formatter: '{b} <br/>{c} ({d}%)'
    }
    ");
    $this->option->set("legend","
    {
        show:true,
        orient: 'vertical',
        x: 'right',
        top:'50px',
        data:['']
    }
    ");
    $this->option->set("series","
    [
        {
            name:'',
            type:'pie',
            radius: ['40%', '70%'],
            data:[
                {value:0, name:''}
            ],

        }
    ]
    ");

  }
  function add($data){
    if (isset($data[1])){
      $this->d[0][]=$data[0];
      $this->d[1][]=array(
        'name'=>$data[0],
        'value'=>$data[1],
        'data'=>(isset($data[2]))?$data[2]:'',
      );

    }
  }
  function make_data(){
    if (empty($this->d[0])) return;
    $this->data=array(
      'legend'=>array(
        "data"=>$this->d[0],
      ),
      'series'=>array(
        array(
          "data"=>$this->d[1],
        )
      )
    );
  }
}

 ?>
