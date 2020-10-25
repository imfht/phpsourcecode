var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'&nbsp;', name:'goodsImg', width: 30, renderer: function(val,item,rowIndex){
            	var thumb = item['goodsImg'];
	        	thumb = thumb.replace('.','_thumb.');
            	return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+thumb
            	+"'><span class='imged' ><img  style='height:180px;width:180px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span></span>";
            }},
            {title:'举报商品',sortable: true, name:'goodsName',renderer: function(val,item,rowIndex){
                return "<a style='color:blue' target='_blank' href='"+WST.U("home/goods/detail","goodsId="+item['goodsId'])+"'><span><p class='wst-nowrap'>"+item['goodsName']+"</p></span></a>";
            }},
            {title:'举报店铺',sortable: true, name:'shopName'},
            {title:'举报人', name:'userName', width: 30,sortable: true, renderer: function(val,item,rowIndex){
            	return WST.blank(item['userName'],item['loginName']);
            }},
            {title:'举报类型',sortable: true, name:'informType'},
            {title:'举报时间',sortable: true, name:'informTime'},
            {title:'状态', name:'informStatus', renderer: function(val,item,rowIndex){
	        	if(val==0)
	        		return "<span class='statu-wait'><i class='fa fa-clock-o'></i> 等待处理</span>";
	        	else if(val==1)
	        		return "<span class='statu-no'><i class='fa fa-ban'></i> 无效举报</span>";
	        	else if(val==2)
	        		return "<span class='statu-yes'><i class='fa fa-check-circle'></i> 有效举报</span>";
	        	else if(val==3)
	        		return "<span class='statu-no'><i class='fa fa-exclamation-triangle'></i> 恶意举报</span>";
            }},
            {title:'操作', name:'op' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
		            h += "<a class='btn btn-blue' href='javascript:toView(" + item['informId'] + ")'><i class='fa fa-search'></i>查看</a> ";
		            if(item['informStatus']==0)
		            h += "<a class='btn btn-blue' href='javascript:toHandle(" + item['informId'] + ")'><i class='fa fa-pencil'></i>处理</a> ";
		            return h;
	            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/Informs/pageQuery'), fullWidthRows: true, autoLoad: false,
        remoteSort:true ,
        sortName: 'informTime',
        sortStatus: 'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function toView(id){
	location.href=WST.U('admin/Informs/view','cid='+id+'&p='+WST_CURR_PAGE);
}
function toHandle(id){
	location.href=WST.U('admin/Informs/toHandle','cid='+id+'&p='+WST_CURR_PAGE);
}
function loadGrid(page){
	var p = WST.getParams('.j-ipt');
	page=(page<=1)?1:page;
    p.page = page;
	mmg.load(p);
}



function finalHandle(id,p){
   var params = {};
   params.cid = id;
   params.finalResult = $.trim($('#finalResult').val());
   params.informStatus = $('input:radio:checked').val();
   if(params.finalResult==''){
     WST.msg('请输入处理信息!',{icon:2});
     return;
   }
   if(typeof(params.informStatus)=='undefined'){
		WST.msg('请选择处理结果',{icon:2});
		return;
	}
   var c = WST.confirm({title:'信息提示',content:'您确定处理该举报商品吗?',yes:function(){
     layer.close(c);
     $.post(WST.U('Admin/Informs/finalHandle'),params,function(data,textStatus){
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
          WST.msg(json.msg,{icon:1});
          location.reload();
        }else if(json.status == '2'){
          location.href=WST.U('admin/informs/index',"p="+p);
        }else{
          WST.msg(json.msg,{icon:2});
        }
      });
   }});
}

  
