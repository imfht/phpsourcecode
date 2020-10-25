var mmg;
$(function(){
	var laydate = layui.laydate;
	laydate.render({
	    elem: '#startDate'
	});
	laydate.render({
	    elem: '#endDate'
	});
	var h = WST.pageHeight();
    var cols = [
            {title:'职员', name:'staffName', width: 50},
            {title:'操作功能', name:'operateDesc' ,width:80,renderer: function (val,item,rowIndex){
	        	return item['menuName']+"-"+item['operateDesc'];
	        }},
            {title:'访问路径', name:'operateUrl' ,width:200},
            {title:'操作IP', name:'operateIP' ,width:70},
            {title:'操作时间', name:'operateTime' ,width:70},
            {title:'传递参数', name:'op' ,width:30,renderer: function (val,item,rowIndex){
	        	return "<a  class='btn btn-blue' onclick='javascript:toView("+item['operateId']+")'><i class='fa fa-search'></i>查看</a>";
	        }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-162,indexCol: true,indexColWidth:50,cols: cols,method:'POST',
        url: WST.U('admin/logoperates/pageQuery'), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });   
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
		 var diff = v?162:135;
	     mmg.resize({height:h-diff})
	}});  
})
function loadGrid(){
	mmg.load({page:1,startDate:$('#startDate').val(),endDate:$('#endDate').val(),staffName:$('#staffName').val(),operateUrl:$('#operateUrl').val()});
}
function toView(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
	 $.post(WST.U('admin/logoperates/get'),{id:id},function(data,textStatus){
	       layer.close(loading);
	       var json = WST.toAdminJson(data);
	       if(json.status==1){
               var content="<xmp style='white-space:normal'>"+json.data.content+"</xmp>";
	    	   $('#content').html(content);
	    	   var box = WST.open({ title:"传递参数",type: 1,area: ['500px', '350px'],
		                content:$('#viewBox'),
		                btn:['关闭'],
		                end:function(){$('#viewBox').hide();},
		                yes: function(index, layero){
		                	layer.close(box);
		                }
	    	   });
	       }else{
	           WST.msg(json.msg,{icon:2});
	       }
	 });
}