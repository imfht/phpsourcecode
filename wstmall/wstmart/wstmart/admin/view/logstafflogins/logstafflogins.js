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
            {title:'登录时间', name:'loginTime' ,width:100},
            {title:'登录IP', name:'loginIp' ,width:100}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-162,indexCol: true,indexColWidth:50,cols: cols,method:'POST',
        url: WST.U('admin/logstafflogins/pageQuery'), fullWidthRows: true, autoLoad: true,
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
	mmg.load({page:1,startDate:$('#startDate').val(),endDate:$('#endDate').val(),staffName:$('#staffName').val(),loginIp:$('#loginIp').val()});
}