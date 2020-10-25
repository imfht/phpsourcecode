/**获取本店分类**/
function getShopsCats(objId,pVal,objVal){
	$('#'+objId).empty();
	$.post(WST.U('shop/shopcats/listQuery'),{parentId:pVal},function(data,textStatus){
	     var json = WST.toJson(data);
	     var html = [],cat;
	     html.push("<option value='' >-请选择-</option>");
	     if(json.status==1 && json.list){
	    	 json = json.list;
			 for(var i=0;i<json.length;i++){
			     cat = json[i];
			     html.push("<option value='"+cat.catId+"' "+((objVal==cat.catId)?"selected":"")+">"+cat.catName+"</option>");
			 }
	     }
	     $('#'+objId).html(html.join(''));
	});
}
function getCat(val){
  if(val==0){
     $('#cat2').html("<option value='' >-请选择-</option>");
     return;
  }
  $.post(WST.U('shop/shopcats/listQuery'),{parentId:val},function(data,textStatus){
       var json = WST.toJson(data);
       var html = [],cat;
       html.push("<option value='' >-请选择-</option>");
       if(json.status==1 && json.list){
         json = json.list;
       for(var i=0;i<json.length;i++){
           cat = json[i];
           html.push("<option value='"+cat.catId+"'>"+cat.catName+"</option>");
        }
       }
       $('#cat2').html(html.join(''));
  });
}
function showImg(id){
  layer.photos({
      photos: '#img-file-'+id
    });
}
function queryByPage(){
    var h = WST.pageHeight();
    var cols = [
        {title:'商品图片', name:'goodsName', width: 40, renderer: function(val,item,rowIndex){
                return "<span class='weixin'><img class='img' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span>";
            }},
        {title:'商品', name:'goodsName', width: 200},

        {title:'评分信息', name:'', width: 100,renderer:function(val,item,rowIndex){

                    var goodsScore="";
                    for(var g = 0; g < item.goodsScore; g++){
                        goodsScore +="<img src='"+window.conf.ROOT+"/static/plugins/raty/img/star-on.png'>";

                    }
                    return goodsScore;

            }},
        {title:'服务评分', name:'', width: 100,renderer:function(val,item,rowIndex){

                var serviceScore="";
                for(var s = 0; s < item.serviceScore; s++){
                    serviceScore +="<img src='"+window.conf.ROOT+"/static/plugins/raty/img/star-on.png'>";

                }
                return serviceScore;

            }},
        {title:'时效评分', name:'', width: 30,renderer:function(val,item,rowIndex){

                var timeScore="";
                for(var t = 0; t < item.timeScore; t++){
                    timeScore +="<img src='"+window.conf.ROOT+"/static/plugins/raty/img/star-on.png'>";

                }
                return timeScore;

            }},
        {title:'回复', name:'', width: 300,renderer:function(val,item,rowIndex){
                var html="";
                html+="评价"+[item['loginName']]+"："+item['content'];
                if(WST.blank(item['images'])!=''){
                    var img = item['images'].split(',');
                    var length = img.length;
                    html+="<div id=\"img-file-"+rowIndex+"\">";
                    for(var g=0;g<length;g++){
                        html+="<img src=\""+window.conf.ROOT+"/"+img[g].replace('.','_thumb.')+"\" layer-src=\""+window.conf.ROOT+"/"+img[g]+"\" width=\"30\" height=\"30\" />";
                    }
                    html+="</div>";
                }
                html+="<div class=\"reply-box\">";
                if(item['shopReply']==null || item['shopReply']==''){
                    html+="<textarea style=\"width:98%;height:80px;margin-bottom:2px;\" id=\"reply-"+item['gaId']+"\" ></textarea>\n" +
                        "              <a class=\"btn btn-primary\" onclick=\"reply(this,"+item['gaId']+")\"><i class='fa fa-mail-reply'></i>回复</a>";
                }else{
                    html+="<p class=\"reply-content\">"+item['shopReply']+"【"+item['replyTime']+"】</p>";
                }
                html+="</div>";
                return html;

            }},
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/goodsappraises/queryByPage'), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    mmg.on('loadSuccess', function(e,data) {
        for(var g=0;g<=data.items.length;g++){
            showImg(g);
        }
    });
}
function loadGrid(){
    mmg.load({cat1:$('#cat1').val(),cat2:$('#cat2').val(),goodsName:$('#goodsName').val(),page:1});
}
function reply(t,id){
 var params = {};
 if($('#reply-'+id).val()==''){
    WST.msg('回复内容不能为空',{icon:2});
    return false;
 }
 params.reply = $('#reply-'+id).val();
 params.id=id;
 $.post(WST.U('shop/goodsappraises/shopReply'),params,function(data){
    var json = WST.toJson(data);
    if(json.status==1){
      var today = new Date();
          today = today.toLocaleDateString();
      var html = '<p class="reply-content">'+params.reply+'【'+today+'】</p>'
      $(t).parent().html(html);
    }
 });
}
