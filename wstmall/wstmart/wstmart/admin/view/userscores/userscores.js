var mmg;
function gridInit(id){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
    var h = WST.pageHeight();
    var cols = [
            {title:'来源', name:'dataSrc', width: 60},
            {title:'积分变化', name:'dataSrc',width: 60,renderer: function (val,item,rowIndex){
                if(item['scoreType']==1){
                    return '<font color="red">+'+item['score']+'</font>';
                }else{
                    return '<font color="green">-'+item['score']+'</font>';
                }
            }},
            {title:'备注', name:'dataRemarks',width: 60},
            {title:'日期', name:'createTime',width: 40}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-120),indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/userscores/pageQuery','id='+id), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    }); 
}
function loadGrid(id){
	mmg.load({page:1,id:id,startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}
var w;
function toAdd(id){
	var ll = WST.msg('正在加载信息，请稍候...');
	$.post(WST.U('admin/userscores/toAdd',{id:id}),{},function(data){
		layer.close(ll);
		w =WST.open({type: 1,title:"调节会员积分",shade: [0.6, '#000'],offset:'50px',border: [0],content:data,area: ['550px', '380px'],success:function(){
            layui.form.render();
        }});
	});
}
function editScore(){
	var params = WST.getParams('.ipt');
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/userscores/add'),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status=='1'){
    		WST.msg("操作成功",{icon:1});
    		closeFrom();
    		loadGrid(params.userId);
    	}else{
    		WST.msg(json.msg,{icon:2});
    	}
    });
}
function closeFrom(){
    layer.close(w);
}