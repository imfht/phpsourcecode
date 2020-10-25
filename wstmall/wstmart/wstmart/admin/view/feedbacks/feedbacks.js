var mmg;
$(function(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#startDate'
    });
    laydate.render({
        elem: '#endDate'
    });
    layer.photos({
        photos: '.feedback-content-gallery',
        area: ['20%','auto']
    });
})
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'用户', name:'userName' ,width:30,sortable:true},
        {title:'反馈类型', name:'feedbackType' ,width:30,sortable:true},
        {title:'反馈内容', name:'feedbackContent' ,width:330,sortable:true},
        {title:'联系方式', name:'contactInfo' ,width:65,sortable:true},
        {title:'创建时间', name:'createTime' ,width:100,sortable:true},
        {title:'处理状态', name:'feedbackStatus' ,width:30,sortable:true,renderer: function(val,item,rowIndex){
            if(item['feedbackStatus']==0){
                return "<span class='statu-wait'><i class='fa fa-clock-o'></i> "+item['feedbackStatusName']+"</span>";
            }else{
                return "<span class='statu-yes'><i class='fa fa-check-circle'></i> "+item['feedbackStatusName']+"</span>";
            }
        }},
        {title:'操作', name:'' ,width:100, align:'center', renderer: function(val,item,rowIndex){
            var h = "";
            if(item['feedbackStatus'] == 0){
                if(WST.GRANT.GNFK_02)h += "<a  class='btn btn-blue' onclick='javascript:toEdit("+item['feedbackId']+")'><i class='fa fa-pencil'></i>回复</a> ";
            }else{
                h += "<a  class='btn btn-blue' onclick='javascript:toEdit("+item['feedbackId']+")'><i class='fa fa-search'></i>查看</a> ";
            }
            if(WST.GRANT.GNFK_03)h += "<a  class='btn btn-red' onclick='javascript:toDel(" + item['feedbackId'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
            return h;
        }}
    ];

    mmg = $('.mmg').mmGrid({height: (h-85),indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/feedbacks/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}

function loadGrid(p){
    p=(p<=1)?1:p;
    mmg.load({page:p,key:$('#key').val(),feedbackContent:$('#feedbackContent').val(),feedbackType:$('#feedbackType').val(),startDate:$('#startDate').val(),endDate:$('#endDate').val()});
}

function toDel(id){
    var box = WST.confirm({content:"您确定要删除该反馈吗?",yes:function(){
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('admin/feedbacks/del'),{feedbackId:id},function(data,textStatus){
            layer.close(loading);
            var json = WST.toAdminJson(data);
            if(json.status=='1'){
                WST.msg("操作成功",{icon:1});
                layer.close(box);
                loadGrid(WST_CURR_PAGE);
            }else{
                WST.msg(json.msg,{icon:2});
            }
        });
    }});
}

function toEdit(id){
    location.href=WST.U('admin/feedbacks/toEdit','feedbackId='+id+'&p='+WST_CURR_PAGE);
}

function toEdits(id,p){
    var params = WST.getParams('.ipt');
    params.feedbackId = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/feedbacks/edit'),params,function(data,textStatus){
        layer.close(loading);
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            WST.msg(json.msg,{icon:1});
            setTimeout(function(){
                location.href=WST.U('admin/feedbacks/index','p='+p);
            },1000);
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}