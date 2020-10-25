function queryByPage(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'商品图片', name:'goodsName', width: 40, renderer: function(val,item,rowIndex){
                return "<span class='weixin'><img class='img' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span>";
            }},
        {title:'商品', name:'goodsName', width: 200},

        {title:'显示', name:'', width: 10, align:'center',renderer:function(val,item,rowIndex){
            if(item['isShow']==1){
                return "<span class='statu-yes'><i class='fa fa-check-circle'></i>显示</span>";
            }else{
                return "<span class='statu-no'><i class='fa fa-ban'></i>隐藏</span>";
            }
        }},
        {title:'咨询内容', name:'goodsName', width: 300,renderer:function(val,item,rowIndex){
            var html="";
            if(WST.blank(item['loginName'])==''){
                html+='游客'
            }else{
                html+=item['loginName']+"："+item['consultContent'];

            }
                html+="<span>("+item['createTime']+")</span>";
                if(item['reply']==null || item['reply']==''){
                   html+="<div><textarea style=\"width:98%;height:80px;margin-bottom:2px;\" id=\"reply-"+item['id']+"\" placeholder='3~200个字符长度' maxlength='200'></textarea>\n" +
                       "              <a class=\"btn btn-primary\" style=\"margin-left:3px;\" onclick=\"reply(this,"+item['id']+")\"><i class='fa fa-mail-reply'></i>回复</a></div>";
                }else {
                    html+="<p class=\"reply-content\">"+item['reply']+"【"+item['replyTime']+"】</p>";
                }
                return html;
            }},
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/goodsconsult/pageQuery'), fullWidthRows: true, autoLoad: false,checkCol:true,multiSelect:true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function loadGrid(p){
    p=(p<=1)?1:p;
    mmg.load({consultType:$('#consultType').val(),consultKey:$('#consultKey').val(),page:p});
}
function reply(t,id){
 var params = {};
 if($('#reply-'+id).val()==''){
    WST.msg('回复内容不能为空',{icon:2});
    return false;
 }
 params.reply = $('#reply-'+id).val();
 params.id=id;
 $.post(WST.U('shop/goodsconsult/reply'),params,function(data){
    var json = WST.toJson(data);
    if(json.status==1){
      var today = new Date();
      var Myd = today.toLocaleDateString();
      var His = today.toLocaleTimeString();
      var html = '<p class="reply-content">'+params.reply+'【'+Myd+'  '+His+'】</p>'
      $(t).parent().html(html);
    }
 });
}

function editConsult(isShow,id){
    var rows = mmg.selectedRows();
    if(rows.length==0){
        WST.msg('请选择要修改的商品',{icon:2});
        return;
    }
    var ids = [];
    for(var s=0;s<rows.length;s++){
        ids.push(rows[s]['id']);
    }
	var params = {};
	params.id = ids;
	params.isShow = parseInt(isShow);
	$.post(WST.U('shop/goodsConsult/edit'),params,function(data){
          var json = WST.toJson(data);
          if(json.status==1){
           WST.msg('设置成功!', {icon: 1});
           loadGrid(WST_CURR_PAGE);
          }
	})
}