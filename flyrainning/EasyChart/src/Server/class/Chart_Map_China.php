<?php


/**
 *
 */
class Chart_Map_China extends ECData
{
  function init(){

    $this->option->clean();

    $js=require("Res_Map_china.php");

    $this->option->setJS($js);


    $this->option->set("tooltip","
    {
        trigger: 'item'
    }
    ");


    $this->option->set("series","
    [
        {
          name: '值',
          type: 'map',
          mapType: 'china',
          roam: true,
          label: {
            normal: {
              show: true
            },
            emphasis: {
              show: true
            }
          },
          data:[]
        }
      ]
       ");


  }
  function add($data){

    if (isset($data[1])){
      $this->d[0][]=array(
        "name"=>$data[0],
        "value"=>$data[1],
        'data'=>(isset($data[2]))?$data[2]:'',
      );

    }


  }
  function make_data(){
    if (empty($this->d[0])) return;
    $this->data=array(
      'series'=>array(
        array(
          "data"=>$this->d[0],
        )
      )
    );
  }
}


 ?>
