var mmg;
$(function(){
    var h = WST.pageHeight();
    var cols = [
            {title:'禁用关键字', name:'word', width: 300},
            {title:'创建时间', name:'createTime', width: 300},
            {title:'操作' , width: 300,name:'status', renderer:function(val,item,rowIndex){
                    var h = "";
                    if(WST.GRANT.XTJYGJZ_02)h += "<a class='btn btn-blue' onclick='javascript:toEdit("+item.id+")'><i class='fa fa-pencil'></i>编辑</a> ";
                    if(WST.GRANT.XTJYGJZ_03)h += "<a class='btn btn-red' onclick='javascript:toDel("+item.id+")'><i class='fa fa-trash-o'></i>删除</a> ";
                    return h;
                }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-162,indexCol: true,indexColWidth:50,cols: cols,method:'POST',
        url: WST.U('admin/limitWords/pageQuery'), fullWidthRows: true, autoLoad: true,
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
	mmg.load({page:1,word:$('#limitword').val()});
}

function toEdit(id){
    $('#limitWordForm')[0].reset();
    if(id>0){
        $.post(WST.U('admin/limitwords/get'),{id:id},function(data,textStatus){
            var json = WST.toAdminJson(data);
            if(json){
                WST.setValues(json);
                layui.form.render();
                editsBox(id);
            }
        });
    }else{
        WST.setValues({word:''});
        layui.form.render();
        editsBox(id);
    }
}

function editsBox(id,v){
    var title =(id>0)?"修改系统禁用关键字":"新增系统禁用关键字";
    var box = WST.open({title:title,type:1,content:$('#limitWordBox'),area: ['500px', '200px'],btn:['确定','取消'],
        end:function(){$('#limitWordBox').hide();},yes:function(){
            $('#limitWordForm').submit();
        }});
    $('#limitWordForm').validator({
        fields: {
            word: {
                tip: "请输入系统禁用关键字",
                rule: '系统禁用关键字:required;length[~50];'
            },
        },
        valid: function(form){
            var params = WST.getParams('.ipt');
            params.id = id;
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/limitwords/'+((id>0)?"edit":"add")),params,function(data,textStatus){
                layer.close(loading);
                var json = WST.toAdminJson(data);
                if(json.status=='1'){
                    WST.msg(json.msg,{icon:1});
                    layer.close(box);
                    setTimeout(function(){
                        loadGrid(WST_CURR_PAGE);
                    },1000);
                }else{
                    WST.msg(json.msg,{icon:2});
                }
            });
        }
    });
}

function toDel(id){
    var box = WST.confirm({content:"您确定要删除该关键字吗?",yes:function(){
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/limitwords/del'),{id:id},function(data,textStatus){
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