@extends('layouts.app')

@section('content')
    <div class="container">
            <!-- Current Tasks -->
                <div class="card">
                    <div class="card-header">
                        	统计汇总[{{$start_date}}-{{$end_date}}]
                    </div>

                    <div class="card-body">
                    	<div id="pie_main" style="height:400px"></div>
                    	<div id="pomo_main" style="height:400px"></div>
                    	<div id="task_main" style="height:400px"></div>
                    	<div id="note_main" style="height:400px"></div>
                    	<div id="article_main" style="height:400px"></div>
                    	<div id="mind_main" style="height:400px"></div>
                    </div>
                </div>
        </div>
    </div>
    <script src="{{ url('/js/echarts.min.js') }}"></script>
     <script type="text/javascript">
        // 路径配置
        require.config({
            paths: {
                echarts: '{{ url('/js/') }}'
            }
        });
        
        // 使用
        require(
            [
                'echarts',
                'echarts/chart/bar',
                'echarts/chart/pie' // 使用柱状图就加载bar模块，按需加载
            ],
            function (ec) {
            	// 基于准备好的dom，初始化echarts图表
                var myPomoChart = ec.init(document.getElementById('pomo_main')); 
             	// 为echarts对象加载数据 
             	var option =  eval('(' +' {{  $pomo_bar_statistics}}' + ')');
                myPomoChart.setOption(option); 
                
                // 基于准备好的dom，初始化echarts图表
                var myTaskChart = ec.init(document.getElementById('task_main')); 
             	var option =  eval('(' +' {{  $task_bar_statistics}}' + ')');
             	// 为echarts对象加载数据 
                myTaskChart.setOption(option); 

             	// 基于准备好的dom，初始化echarts图表
                var myNoteChart = ec.init(document.getElementById('note_main')); 
             	var option =  eval('(' +' {{  $note_bar_statistics}}' + ')');
             	// 为echarts对象加载数据 
                myNoteChart.setOption(option); 

             // 基于准备好的dom，初始化echarts图表
                var myMindChart = ec.init(document.getElementById('mind_main')); 
             	var option =  eval('(' +' {{  $mind_bar_statistics}}' + ')');
             	// 为echarts对象加载数据 
                myMindChart.setOption(option); 

             // 基于准备好的dom，初始化echarts图表
                var myArticleChart = ec.init(document.getElementById('article_main')); 
             	var option =  eval('(' +' {{  $article_bar_statistics}}' + ')');
             	// 为echarts对象加载数据 
                myArticleChart.setOption(option); 

                var myPieChart = ec.init(document.getElementById('pie_main')); 
             	var option =  eval('(' +' {{  $count_pie_statistics}}' + ')');
             	// 为echarts对象加载数据 
                myPieChart.setOption(option); 
                
                
                var option = {
                    tooltip: {
                        show: true
                    },
                    legend: {
                        data:['销量']
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : ["衬衫","羊毛衫","雪纺衫","裤子","高跟鞋","袜子"]
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            "name":"销量",
                            "type":"bar",
                            "data":[5, 20, 40, 10, 10, 20]
                        }
                    ]
                };
        
                
            }
        );
    </script>
@endsection
