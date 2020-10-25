<?php


/**
 *
 */
class Chart_Bar extends ECDataSet
{
  function init(){

    $this->option->set("tooltip","
    {
        trigger: 'axis',
        axisPointer : {
            type : 'shadow'
        }
    }
    ");

    $this->option->set("xAxis","
       {
           type : 'category',
           axisTick: {
               alignWithLabel: true
           }
       }
     ");


     $this->option->set("yAxis","
          {
              type : 'value'
          }
      ");

    $this->setSeries("
        {
            type:'bar'
        }
       ");


  }
}


 ?>
