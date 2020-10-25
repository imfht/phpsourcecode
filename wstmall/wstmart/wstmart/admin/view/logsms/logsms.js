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
            {title:'使用者ID', name:'smsUserId', width: 10},
            {title:'消息内容', name:'smsContent' ,width:120},
            {title:'消息代码', name:'smsCode' ,width:50},
            {title:'发送方式', name:'smsFunc' ,width:80},
            {title:'发送号码', name:'smsPhoneNumber' ,width:50},
            {title:'消息IP', name:'smsIP' ,width:60},
            {title:'发送时间', name:'createTime' ,width:70},
            {title:'返回状态', name:'smsReturnCode' ,width:130}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-162,indexCol: true,indexColWidth:50,cols: cols,method:'POST',
        url: WST.U('admin/logsms/pageQuery'), fullWidthRows: true, autoLoad: true,
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
	mmg.load({page:1,startDate:$('#startDate').val(),endDate:$('#endDate').val(),phone:$('#phone').val()});
}
