// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * @name 后台首页
 */
layui.define(['jquery','larry'],function(exports){
  "use strict";
	var $ = layui.$,
	    larry = layui.larry,
	    larryms = layui.larryms;

    $('#closeInfo').on('click',function(){
         $('#Minfo').hide();
    });
	   
	$('#shortcut a').off('click').on('click',function(){
          var data = {
               href: $(this).data('url'),
               icon: $(this).data('icon'),
               ids: $(this).data('id'),
               group:$(this).data('group'),
               title:$(this).children('.larry-value').children('cite').text()
          }
          // larry.addTab(data);
    });
	
    // 引入larry-panel操作
    larry.panel();
    
//    var myDate = new Date(),
//           year = myDate.getFullYear(),
//           month = myDate.getMonth()+1,
//           day = myDate.getDate(),
//           time = myDate.toLocaleTimeString();
//       $('#weather').html('您好，现在时间是：'+year+'年'+month+'月'+day+'日 '+time);
//       $('#versionT').text(larryms.version);
    
     
    //引用百度图标JS
    larryms.plugin('echarts.js');
    larryms.plugin('theme/macarons.js',statisticsList);
    var themeName = "";//"macarons";

    //大数据统计入口
    function statisticsList() {
    	
    	//会员来源
    	statistics1();
    	
    	//周会员增量
    	statistics2();
    	
    	//数据统计
    	statistics3();
    	
    }
    
    /**
     * 会员来源
     */
    function statistics1() {
	    var myChart = echarts.init(document.getElementById('larryCount'),themeName),
	    option = {
	      title: {
	        text: '用户访问来源',
	        subtext: '纯属虚构',
	        x: 'center'
	      },
	      tooltip: {
	        trigger: 'item',
	        formatter: "{a} <br/>{b} : {c} ({d}%)"
	      },
	      legend: {
	        orient: 'vertical',
	        left: 'left',
	        data: ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
	      },
	      series: [{
	        name: '访问来源',
	        type: 'pie',
	        radius: '55%',
	        center: ['50%', '60%'],
	        data: [{
	          value: 335,
	          name: '直接访问'
	        }, {
	          value: 310,
	          name: '邮件营销'
	        }, {
	          value: 234,
	          name: '联盟广告'
	        }, {
	          value: 135,
	          name: '视频广告'
	        }, {
	          value: 1548,
	          name: '搜索引擎'
	        }],
	        itemStyle: {
	          emphasis: {
	            shadowBlur: 10,
	            shadowOffsetX: 0,
	            shadowColor: 'rgba(0, 0, 0, 0.5)'
	          }
	        }
	      }]
	    };
	    myChart.setOption(option);
	
	    window.onresize = function() {
	    	myChart.resize();
	    };
  }
    
    /**
     * 周会员增量
     */
    function statistics2() {
	    var myChart = echarts.init(document.getElementById('larryCount2'),themeName),
	    option = {
	        title : {
	            text: '某地区蒸发量和降水量',
	            subtext: '纯属虚构'
	        },
	        tooltip : {
	            trigger: 'axis'
	        },
	        legend: {
	            data:['蒸发量','降水量']
	        },
	        toolbox: {
	            show : true,
	            feature : {
	                mark : {show: true},
	                dataView : {show: true, readOnly: false},
	                magicType : {show: true, type: ['line', 'bar']},
	                restore : {show: true},
	                saveAsImage : {show: true}
	            }
	        },
	        calculable : true,
	        xAxis : [
	            {
	                type : 'category',
	                data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
	            }
	        ],
	        yAxis : [
	            {
	                type : 'value'
	            }
	        ],
	        series : [
	            {
	                name:'蒸发量',
	                type:'bar',
	                data:[2.0, 4.9, 7.0, 23.2, 25.6, 76.7, 135.6, 162.2, 32.6, 20.0, 6.4, 3.3],
	                markPoint : {
	                    data : [
	                        {type : 'max', name: '最大值'},
	                        {type : 'min', name: '最小值'}
	                    ]
	                },
	                markLine : {
	                    data : [
	                        {type : 'average', name: '平均值'}
	                    ]
	                }
	            },
	            {
	                name:'降水量',
	                type:'bar',
	                data:[2.6, 5.9, 9.0, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6.0, 2.3],
	                markPoint : {
	                    data : [
	                        {name : '年最高', value : 182.2, xAxis: 7, yAxis: 183, symbolSize:18},
	                        {name : '年最低', value : 2.3, xAxis: 11, yAxis: 3}
	                    ]
	                },
	                markLine : {
	                    data : [
	                        {type : 'average', name : '平均值'}
	                    ]
	                }
	            }
	        ]
	    };
	    myChart.setOption(option);
	
	    window.onresize = function() {
	    	myChart.resize();
	    };
  }
    
    
    /**
     * 数据统计
     */
    function statistics3() {
	    var myChart = echarts.init(document.getElementById('larryCount3'),themeName),
	    option = {
	        tooltip : {
	            trigger: 'axis'
	        },
	        toolbox: {
	            show : true,
	            y: 'bottom',
	            feature : {
	                mark : {show: true},
	                dataView : {show: true, readOnly: false},
	                magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
	                restore : {show: true},
	                saveAsImage : {show: true}
	            }
	        },
	        calculable : true,
	        legend: {
	            data:['直接访问','邮件营销','联盟广告','视频广告','搜索引擎','百度','谷歌','必应','其他']
	        },
	        xAxis : [
	            {
	                type : 'category',
	                splitLine : {show : false},
	                data : ['周一','周二','周三','周四','周五','周六','周日']
	            }
	        ],
	        yAxis : [
	            {
	                type : 'value',
	                position: 'right'
	            }
	        ],
	        series : [
	            {
	                name:'直接访问',
	                type:'bar',
	                data:[320, 332, 301, 334, 390, 330, 320]
	            },
	            {
	                name:'邮件营销',
	                type:'bar',
	                tooltip : {trigger: 'item'},
	                stack: '广告',
	                data:[120, 132, 101, 134, 90, 230, 210]
	            },
	            {
	                name:'联盟广告',
	                type:'bar',
	                tooltip : {trigger: 'item'},
	                stack: '广告',
	                data:[220, 182, 191, 234, 290, 330, 310]
	            },
	            {
	                name:'视频广告',
	                type:'bar',
	                tooltip : {trigger: 'item'},
	                stack: '广告',
	                data:[150, 232, 201, 154, 190, 330, 410]
	            },
	            {
	                name:'搜索引擎',
	                type:'line',
	                data:[862, 1018, 964, 1026, 1679, 1600, 1570]
	            },
	
	            {
	                name:'搜索引擎细分',
	                type:'pie',
	                tooltip : {
	                    trigger: 'item',
	                    formatter: '{a} <br/>{b} : {c} ({d}%)'
	                },
	                center: [160,130],
	                radius : [0, 50],
	                itemStyle :　{
	                    normal : {
	                        labelLine : {
	                            length : 20
	                        }
	                    }
	                },
	                data:[
	                    {value:1048, name:'百度'},
	                    {value:251, name:'谷歌'},
	                    {value:147, name:'必应'},
	                    {value:102, name:'其他'}
	                ]
	            }
	        ]
	    };
	
	    myChart.setOption(option);
	    
	    window.onresize = function () {
	        myChart.resize();
	    }
  }
    
    exports('main', {}); 
});