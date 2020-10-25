<?php
/**
 *
 */
class Chart_Line extends ECDataSet
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
            type:'line',
            smooth:true,
            lineWidth:1.2,

            itemStyle:{
                normal:{
                    color:'#f17a52',
                    shadowBlur: 40,
                    label:{
                        show:true,
                        position:'top',
                        textStyle:{
                            color:'#f17a52'

                        }
                    }
                }
            },
            areaStyle:{
                normal:{
                    color:'#f17a52',
                    opacity:0.08
                }
            }

        }

    ");

  }

}


 ?>
