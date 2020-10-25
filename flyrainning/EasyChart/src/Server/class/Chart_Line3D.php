<?php


/**
 *
 */
class Chart_Line3D extends ECData3D
{
  function init(){
    $this->option->clean();
    $this->option->set("tooltip","{}");

    $this->option->set("visualMap","
    {
         show: false,
         dimension: 2,
         inRange: {
             color: ['#313695', '#4575b4', '#74add1', '#abd9e9', '#e0f3f8', '#ffffbf', '#fee090', '#fdae61', '#f46d43', '#d73027', '#a50026']
         }
     }
    ");
    $this->option->set("xAxis3D","
    {
       type: 'category',
       name: '',
       data: []
    }
    ");
    $this->option->set("yAxis3D","
    {
       type: 'category',
       name: '',
       data: []
    }
    ");
    $this->option->set("zAxis3D","
    {
       type: 'value',
       name: '',
       data: []
    }
    ");
    $this->option->set("grid3D","
    {
      axisPointer:false,
      viewControl: {
           projection: 'orthographic'
      }
    }
    ");
    $this->option->set("series","
    [{
        type: 'line3D',
        name:'',
        data: [],
        lineStyle: {
           width: 4
        }
    }]
       ");


  }
}


 ?>
