$(function(){
	var laydate = layui.laydate;
	laydate.render({
	    elem: '#startDate'
	});
	laydate.render({
	    elem: '#endDate'
	});
    loadStat();
});
function loadStat(){
    var loading = WST.msg('正在查询数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/reports/statOrders'),WST.getParams('.ipt'),function(data,textStatus){
	    layer.close(loading);
	    var json = WST.toAdminJson(data);
	    var myChart = echarts.init(document.getElementById('main'));
	    myChart.clear();
	    if(json.status=='1' && json.data){
			var option = {
			    tooltip : {
			        trigger: 'axis'
			    },
			    toolbox: {
			        show : true,
			        y: 'top',
			        feature : {
			            mark : {show: true},
			            dataView : {show: false, readOnly: false},
			            magicType : {show: true, type: ['line', 'bar', 'tiled']},
			            restore : {show: true},
			            saveAsImage : {show: true}
			        }
			    },
			    calculable : true,
			    legend: {
			        data:['电脑端','微信端','触屏端','小程序','安卓端','苹果端','总订单数']
			    },
			    xAxis : [
			        {
			            type : 'category',
			            splitLine : {show : false},
			            data : json.data.days
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
			            name:'电脑端',
			            type:'line',
			            stack: '来源',
			            data:json.data['p0']
			        },
			        {
			            name:'微信端',
			            type:'line',
			            stack: '来源',
			            data:json.data['p1']
			        },
			        {
			            name:'触屏端',
			            type:'line',
			            stack: '来源',
			            data:json.data['p2']
			        },
			        {
			            name:'小程序',
			            type:'line',
			            stack: '来源',
			            data:json.data['p5']
			        },
			        {
			            name:'安卓端',
			            type:'line',
			            stack: '来源',
			            data:json.data['p3']
			        },
			        {
			            name:'苹果端',
			            type:'line',
			            stack: '来源',
			            data:json.data['p4']
			        },
			        {
			            name:'总订单数',
			            type:'line',
			            data:json.data['total']
			        },

			        {
			            name:'销售来源细分',
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
			                {value:json.data.map.p0, name:'电脑端'},
			                {value:json.data.map.p1, name:'微信端'},
			                {value:json.data.map.p2, name:'触屏端'},
			                {value:json.data.map.p5, name:'小程序'},
			                {value:json.data.map.p3, name:'安卓端'},
			                {value:json.data.map.p4, name:'苹果端'}
			            ]
			        }
			    ]
			};       
			myChart.setOption(option);
			var gettpl = document.getElementById('stat-tblist').innerHTML;
			console.log(json.data.days);
			layui.laytpl(gettpl).render(json.data, function(html){
	       		$('#list-box').html(html);
	       		$('#mainTable').removeClass('hide');
		    });
	    }else{
	    	WST.msg('没有查询到记录');
	    }

	});  
}

function toExport(){
    var params = WST.getParams('.ipt');
    var box = WST.confirm({content:"您确定要导出该统计数据吗?",yes:function(){
        layer.close(box);
        location.href=WST.U('admin/reports/toExportStatOrders',params);
    }});
}