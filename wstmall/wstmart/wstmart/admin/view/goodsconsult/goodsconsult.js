var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'商品主图', name:'goodsImg', width: 100, renderer: function(val,item,rowIndex){
            	var thumb = item['goodsImg'];
	        	thumb = thumb.replace('.','_thumb.');
	        	return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+thumb
            	+"'><span class='imged' style='left:45px;'><img  style='height:150px;width:150px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span></span>";
            }},
            {title:'商品', name:'goodsName', width: 100,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsName']+"</p></span>";
            }},
            
            {title:'咨询内容', name:'consultContent', width: 100,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['consultContent']+"</p></span>";
            }},
            {title:'回复内容', name:'reply', width: 100,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['reply']+"</p></span>";
            }},
            {title:'状态', name:'isShow', width: 100, renderer: function(val,item,rowIndex){
            	return (val==0)?"<span class='statu-no'><i class='fa fa-ban'></i> 隐藏</span>":"<span class='statu-yes'><i class='fa fa-check-circle'></i> 显示</span></h3>";
            }},
            {title:'操作', name:'' ,width:70, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            if(WST.GRANT.SPZX_02)h += "<a class='btn btn-blue' href='"+WST.U('admin/goodsconsult/toEdit','id='+item['id'])+'&p='+WST_CURR_PAGE+"'><i class='fa fa-pencil'></i>修改</a> ";
	            if(WST.GRANT.SPZX_03)h += "<a class='btn btn-red' href='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> "; 
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/goodsconsult/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function toDel(id){
	var box = WST.confirm({content:"您确定要删除该记录吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/goodsconsult/del'),{id:id},function(data,textStatus){
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
function loadGrid(p){
    p=(p<=1)?1:p;
		var query = WST.getParams('.query');
    query.page = p;
	  mmg.load(query);
}

function editInit(p){
/* 表单验证 */
    $('#goodsconsultForm').validator({
            fields: {
                consultContent: {
                  rule:"required;length(3~200)",
                  msg:{length:"评价内容为3-200个字",required:"评价内容为3-200个字"},
                  tip:"评价内容为3-200个字",
                  ok:"",
                },
                reply:  {
                  rule:"required;length(3~200)",
                  msg:{length:"回复内容为3-200个字",required:"回复内容为3-200个字"},
                  tip:"回复内容为3-200个字",
                  ok:""
                },
                
            },
          valid: function(form){
            var params = WST.getParams('.ipt');
            var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
            $.post(WST.U('admin/goodsconsult/edit'),params,function(data,textStatus){
              layer.close(loading);
              var json = WST.toAdminJson(data);
              if(json.status=='1'){
                  WST.msg("操作成功",{icon:1});
                  location.href=WST.U('Admin/goodsconsult/index',"p="+p);
              }else{
                    WST.msg(json.msg,{icon:2});
              }
            });

      }

    });
}
function toolTip(){
    $('body').mousemove(function(e){
    	var windowH = $(window).height();  
        if(e.pageY >= windowH*0.8){
        	var top = windowH*0.233;
        	$('.imged').css('margin-top',-top);
        }else{
        	var top = windowH*0.06;
        	$('.imged').css('margin-top',-top);
        }
    });
}