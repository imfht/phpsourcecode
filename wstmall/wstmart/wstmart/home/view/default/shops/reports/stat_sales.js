function loadStat(){
    var loading = WST.load({msg:'正在查询数据，请稍后...'});
	$.post(WST.U('home/reports/getStatSales'),WST.getParams('.j-ipt'),function(data,textStatus){
	    layer.close(loading);
	    var json = WST.toJson(data);
	    var myChart = echarts.init(document.getElementById('main'));
	    myChart.clear();
	    $('#mainTable').hide();
	    if(json.status=='1' && json.data){
			var option = {
			    tooltip : {
			        trigger: 'axis'
			    },
			    toolbox: {
			        show : true,
			        feature : {
			            mark : {show: true},
			            dataView : {show: false, readOnly: false},
			            magicType : {show: true, type: ['line', 'bar']},
			            restore : {show: true},
			            saveAsImage : {show: true}
			        }
			    },
			    calculable : true,
			    xAxis : [
			        {
			            type : 'category',
			            data : json.data.days
			        }
			    ],
			    yAxis : [
			        {
			            type : 'value'
			        }
			    ],
			    series : [
			        {
			            name:'销售额',
			            type:'line',
			            data:json.data.dayVals
			        }
			    ]
			};
			myChart.setOption(option);
			var gettpl = document.getElementById('stat-tblist').innerHTML;
			laytpl(gettpl).render(json.data.list, function(html){
	       		$('#list-box').html(html);
	       		$('#mainTable').show();
		    });
	    }else{
	    	WST.msg('没有查询到记录',{icon:5});
	    }
	}); 
}