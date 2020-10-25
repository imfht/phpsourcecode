var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'提现单号', name:'cashNo', width: 90,sortable: true},
            {title:'会员类型', name:'targetTypeName' ,width:60,sortable: true},
            {title:'会员名称', name:'loginName' ,width:100, renderer:function(val,item,rowIndex){
                return WST.blank(item['userName'])+"("+item['loginName']+")";
            }},
            {title:'提现银行', name:'accTargetName' ,width:60,sortable: true},
            {title:'银行卡号', name:'accNo' ,width:40,sortable: true},
            {title:'持卡人', name:'accUser' ,width:40,sortable: true},
            {title:'提现金额', name:'money' ,width:40,sortable: true, renderer:function(val,item,rowIndex){
                return '￥'+val;
            }},
            {title:'提现时间', name:'createTime',sortable: true ,width:60},
            {title:'状态', name:'cashSatus' ,width:60,sortable: true, renderer:function(val,item,rowIndex){
                return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 提现成功</span>":((val==-1)?"<span class='statu-no'><i class='fa fa-ban'></i> 提现失败&nbsp;</span>":"<span class='statu-wait'><i class='fa fa-clock-o'></i> 待处理&nbsp;</span>");
            }},
            {title:'操作', name:'' ,width:120, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' href='javascript:toView(" + item['cashId'] + ")'><i class='fa fa-search'></i>查看</a> ";
	            if(item['cashSatus']==0 && WST.GRANT.TXSQ_04)h += "<a class='btn btn-green' href='javascript:toEdit(" + item['cashId'] + ")'><i class='fa fa-pencil'></i>处理</a> ";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-182,indexCol: true,indexColWidth:50, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/cashdraws/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'createTime',sortStatus:'desc',
        remoteSort:true ,
        sortName: 'cashNo',
        sortStatus: 'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });  
    $('#headTip').WSTTips({width:90,height:35,callback:function(v){
         var diff = v?182:137;
         mmg.resize({height:h-diff})
    }});
    loadGrid(p)
}

function toEdit(id){
	location.href=WST.U('admin/cashdraws/toHandle','id='+id+'&p='+WST_CURR_PAGE);
}
function toView(id){
	location.href=WST.U('admin/cashdraws/toView','id='+id+'&p='+WST_CURR_PAGE);
}
function loadGrid(p){
    p=(p<=1)?1:p;
	mmg.load({page:p,cashNo:$('#cashNo').val(),cashSatus:$('#cashSatus').val(),targetType:$('#targetType').val()});
}

function save(p){
	var params = WST.getParams('.ipt');
	if(typeof(params.cashSatus)=='undefined'){
		WST.msg('请选择提现结果',{icon:2});
		return;
	}
	if(params.cashSatus==-1 && $.trim(params.cashRemarks)==''){
		WST.msg('输入提现失败原因',{icon:2});
		return;
	}
	if(WST.confirm({content:'您确定该提现申请'+((params.cashSatus==1)?'成功':'失败')+'吗？',yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	    $.post(WST.U('admin/cashdraws/handle'),params,function(data,textStatus){
	    	layer.close(loading);
	    	var json = WST.toAdminJson(data);
	    	if(json.status=='1'){
	    		WST.msg("操作成功",{icon:1});
	    		location.href=WST.U('admin/cashdraws/index','p='+p);
	    	}else{
	    		WST.msg(json.msg,{icon:2});
	    	}
	    });
	}}));
}
function toExport(){
    var params = {};
    params = WST.getParams('.j-ipt');
    var box = WST.confirm({content:"您确定要导出提现申请记录吗?",yes:function(){
        layer.close(box);
        location.href=WST.U('admin/cashdraws/toExport',params);
    }});
}