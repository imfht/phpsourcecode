var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'计划任务名称', name:'cronName', width: 80},
            {title:'计划任务描述', name:'cronDesc', width: 150},
            {title:'上次执行时间', name:'runTime', width: 70, renderer: function(val,item,rowIndex){
            	return (item['runTime']==0)?'-':item['runTime'];
            }},
            {title:'执行状态', name:'isEnable', width: 20, renderer: function(val,item,rowIndex){
            	return (item['isRunSuccess']==1)?'<span class="statu-yes"><i class="fa fa-check-circle"></i> 成功</span>':'<span class="statu-no"><i class="fa fa-times-circle"></i> 失败</span>';
            }},
            {title:'下次执行时间', name:'nextTime', width: 70, renderer: function(val,item,rowIndex){
            	return (item['nextTime']==0)?'-':item['nextTime'];
            }},
            {title:'作者', name:'auchor', width: 20, renderer: function(val,item,rowIndex){
            	return '<a href="'+item['authorUrl']+'" target="_blank">'+item['author']+'</a>';
            }},
            {title:'计划状态', name:'isEnable', width: 20, renderer: function(val,item,rowIndex){
            	return (item['isEnable']==1)?'<span class="statu-yes"><i class="fa fa-check-circle"></i> 启用</span>':'<span class="statu-wait"><i class="fa fa-ban"></i> 停用</span>';
            }},
            {title:'操作', name:'' ,width:160, align:'center', renderer: function(val,item,rowIndex){
                var h="";
	            if(WST.GRANT.CRON_JHRW_04){
	            	h += "<a class='btn btn-blue' href='javascript:toEdit(" + item['id'] + ")'><i class='fa fa-pencil'></i>修改</a> ";
	            	if(item['isEnable']==0){
	            	    h += "<a class='btn btn-green' href='javascript:changgeEnableStatus(" + item['id'] + ",1)'><i class='fa fa-check'></i>启用</a> "; 
		            }else{
		            	h += "<a class='btn btn-red' href='javascript:changgeEnableStatus(" + item['id'] + ",0)'><i class='fa fa-ban'></i>停用</a> "; 
		                h += '<a class="btn btn-blue" href="javascript:run(\'' + item['id'] + '\')"><i class="fa fa-refresh"></i>执行</a>';
		            }
		            
	            }
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-125,indexCol: true, cols: cols,method:'POST',
        url: WST.AU('cron://cron/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
		 var diff = v?115:88;
	     mmg.resize({height:h-diff})
	}});
    loadGrid(p);
}
function loadGrid(p){
    p=(p<=1)?1:p;
	mmg.load({page:p});
}

function toEdit(id){
	location.href=WST.AU('cron://cron/toEdit','id='+id+'&p='+WST_CURR_PAGE);
}
function checkType(v){
   $('.cycle').hide();
   $('.cycle'+v).show();
}
function run(id){
	var box = WST.confirm({content:'你确定要执行该任务吗？',yes:function(){
		var loading = WST.msg('正在执行计划任务，请稍后...',{icon: 16,time:6000000000});
		$.post(WST.AU('cron://cron/runCron'),{id:id},function(data,textStatus){
			layer.close(loading);
	        var json = WST.toAdminJson(data);
	        if(json.status=='1'){
	           	WST.msg(json.msg,{icon:1});
	           	layer.close(box);
                loadGrid(WST_CURR_PAGE);
	        }else{
	           	WST.msg(json.msg,{icon:2});
	        }
		})
	}});
}
function edit(id,p){
    var params = WST.getParams('.ipt');
	params.id = id;
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.AU('cron://cron/edit'),params,function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
		   	WST.msg("操作成功",{icon:1},function(){
		   		location.href=WST.AU('cron://cron/index','p='+p);
		   	});
		}else{
		   	WST.msg(json.msg,{icon:2});
		}
	});
}
function changgeEnableStatus(id,type){
	var msg = (type==1)?"您确定要启用该计划任务吗?":"您确定要停用该计划任务吗?"
	var box = WST.confirm({content:msg,yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.AU('cron://cron/changeEnableStatus'),{id:id,status:type},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
                              loadGrid(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}






		