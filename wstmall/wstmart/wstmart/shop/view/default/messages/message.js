var laytpl = layui.laytpl;
var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'消息', name:'msgContent' ,width:700,sortable:true,renderer: function(val,item,rowIndex){
            if(item['msgStatus'] == 0){
                return "<i class='fa fa-envelope fa-lg'></i-o'></i> "+item['msgContent'];
            }else{
                return "<i class='fa fa-envelope-open-o fa-lg'></i> "+item['msgContent'];
            }
        }},
        {title:'时间', name:'createTime' ,width:90,sortable:true},
        {title:'操作', name:'' ,width:100, align:'center', renderer: function(val,item,rowIndex){
            var h = "";
            h += "<a  class='btn btn-blue' onclick='javascript:showMsg("+item['id']+")'><i class='fa fa-search'></i>查看</a> ";
            h += "<a  class='btn btn-red' onclick='javascript:delMsg(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
            return h;
        }}
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',checkCol:true,multiSelect:true,
        url: WST.U('shop/Messages/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,
        plugins: [
            $('#pg').mmPaginator()
        ]
    });
    loadGrid(p);
}

function loadGrid(p){
    p = (p<=1)?1:p;
    mmg.load({page:p});
}

function showMsg(id){
  location.href=WST.U('shop/messages/showShopMsg','msgId='+id+'&p='+WST_CURR_PAGE);
}

function delMsg(id){
WST.confirm({content:"您确定要删除该消息吗？", yes:function(tips){
  var ll = WST.load({msg:'数据处理中，请稍候...'});
  $.post(WST.U('shop/messages/del'),{id:id},function(data,textStatus){
    layer.close(ll);
      layer.close(tips);
    var json = WST.toJson(data);
    if(json.status=='1'){
      WST.msg('操作成功!', {icon: 1}, function(){
         loadGrid(WST_CURR_PAGE);
      });
    }else{
      WST.msg('操作失败!', {icon: 5});
    }
  });
}});
}
function batchDel(){
    var rows = mmg.selectedRows();
    if(rows.length==0){
        WST.msg('请选择要删除的消息!',{icon:2});
        return;
    }
    var ids = [];
    for(var i=0;i<rows.length;i++){
        ids.push(rows[i]['id']);
    }
    WST.confirm({content:"您确定要删除该消息吗？", yes:function(tips){
        var params = {};
        params.ids = ids;
        var load = WST.load({msg:'请稍后...'});
        $.post(WST.U('shop/messages/batchDel'),params,function(data,textStatus){
          layer.close(load);
          var json = WST.toJson(data);
          if(json.status=='1'){
            WST.msg('操作成功',{icon:1},function(){
                loadGrid(WST_CURR_PAGE);
            });
          }else{
            WST.msg('操作失败',{icon:5});
          }
        });
    }});
}
function batchRead(){
    var rows = mmg.selectedRows();
    if(rows.length==0){
        WST.msg('请选择处理的消息!',{icon:2});
        return;
    }
    var ids = [];
    for(var i=0;i<rows.length;i++){
        ids.push(rows[i]['id']);
    }
    WST.confirm({content:"您确定要将这些消息标记为已读吗？", yes:function(tips){
        var params = {};
        params.ids = ids;
        var load = WST.load({msg:'请稍后...'});
        $.post(WST.U('shop/messages/batchRead'),params,function(data,textStatus){
          layer.close(load);
          var json = WST.toJson(data);
          if(json.status=='1'){
            WST.msg('操作成功',{icon:1},function(){
                loadGrid(1);
            });
          }else{
            WST.msg('操作失败',{icon:5});
          }
        });
    }});
}
