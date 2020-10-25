var mmg;
function initSaleGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'&nbsp;', name:'goodsImg', width: 20, renderer: function(val,item,rowIndex){
            	var thumb = item['goodsImg'];
	        	thumb = thumb.replace('.','_thumb.');
            	return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+thumb
            	+"'><span class='imged' ><img  style='height:180px;width:180px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span></span>";
            }},
            {title:'商品名称', name:'goodsName', width: 120,sortable:true,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsName']+"</p></span>";
            }},
            {title:'商品编号', name:'goodsSn' ,width:60,sortable:true},
            {title:'价格', name:'shopPrice' ,width:20,sortable:true, renderer: function(val,item,rowIndex){
            	return '￥'+item['shopPrice'];
            }},
            {title:'所属店铺', name:'shopName' ,width:60,sortable:true},
            {title:'所属分类', name:'goodsCatName' ,width:100,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsCatName']+"</p></span>";
            }},
            {title:'销量', name:'saleNum' ,width:20,sortable:true,align:'center'},
            {title:'操作', name:'' ,width:150, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' target='_blank' href='"+WST.U("home/goods/detail","goodsId="+item['goodsId'])+"'><i class='fa fa-search'></i>查看</a> ";
	            if(WST.GRANT.SJSP_04)h += "<a class='btn btn-red' href='javascript:illegal(" + item['goodsId'] + ",1)'><i class='fa fa-ban'></i>违规下架</a> ";
	            if(WST.GRANT.SJSP_03)h += "<a class='btn btn-red' href='javascript:del(" + item['goodsId'] + ",1)'><i class='fa fa-trash-o'></i>删除</a> "; 
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/goods/saleByPage'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'goodsSn',sortStatus:'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function loadGrid(p){
	p=(p<=1)?1:p;
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
    params.page = p;
	mmg.load(params);
}

function del(id,type){
	var box = WST.confirm({content:"您确定要删除该商品吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/goods/del'),{id:id},function(data,textStatus){
	           			layer.close(loading);
	           			var json = WST.toAdminJson(data);
	           			if(json.status=='1'){
	           			    WST.msg(json.msg,{icon:1});
	           			    layer.close(box);
	           			    loadGrid(WST_CURR_PAGE);
	           			}else{
	           			    WST.msg(json.msg,{icon:2});
	           			}
	           		});
	            }});
}
function illegal(id,type){
	var w = WST.open({type: 1,title:((type==1)?"商品违规原因":"商品不通过原因"),shade: [0.6, '#000'],border: [0],
	    content: '<textarea id="illegalRemarks" rows="7" style="width:100%" maxLength="200"></textarea>',
	    area: ['500px', '260px'],btn: ['确定', '关闭窗口'],
        yes: function(index, layero){
        	var illegalRemarks = $.trim($('#illegalRemarks').val());
        	if(illegalRemarks==''){
        		WST.msg(((type==1)?'请输入违规原因 !':'请输入不通过原因!'), {icon: 5});
        		return;
        	}
        	var ll = WST.msg('数据处理中，请稍候...');
		    $.post(WST.U('admin/goods/illegal'),{id:id,illegalRemarks:illegalRemarks},function(data){
		    	layer.close(w);
		    	layer.close(ll);
		    	var json = WST.toAdminJson(data);
				if(json.status>0){
					WST.msg(json.msg, {icon: 1});
					loadGrid(WST_CURR_PAGE);
				}else{
					WST.msg(json.msg, {icon: 2});
				}
		   });
        }
	});
}

function initAuditGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'&nbsp;', name:'goodsName', width: 20, renderer: function(val,item,rowIndex){
            	return "<span class='weixin'><img class='img' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span>";
            }},
            {title:'商品名称', name:'goodsName', width: 200,sortable:true,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsName']+"</p></span>";
            }},
            {title:'商品编号', name:'goodsSn' ,width:40,sortable:true},
            {title:'价格', name:'shopPrice' ,width:20,sortable:true, renderer: function(val,item,rowIndex){
            	return '￥'+item['shopPrice'];
            }},
            {title:'所属店铺', name:'shopName' ,width:60,sortable:true},
            {title:'所属分类', name:'goodsCatName' ,width:60,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsCatName']+"</p></span>";
            }},
            {title:'销量', name:'saleNum' ,width:20,sortable:true,align:'center'},
            {title:'操作', name:'' ,width:160, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' target='_blank' href='"+WST.U("home/goods/detail","goodsId="+item['goodsId']+"&key="+item['verfiycode'])+"'><i class='fa fa-search'></i>查看</a> ";
	            if(WST.GRANT.DSHSP_04)h += "<a class='btn btn-blue' href='javascript:allow(" + item['goodsId'] + ",0)'><i class='fa fa-check'></i>审核通过</a> ";
	            if(WST.GRANT.DSHSP_04)h += "<a class='btn btn-red' href='javascript:illegal(" + item['goodsId'] + ",0)'><i class='fa fa-ban'></i>审核不通过</a> ";
	            if(WST.GRANT.DSHSP_03)h += "<a class='btn btn-red' href='javascript:del(" + item['goodsId'] + ",0)'><i class='fa fa-trash-o'></i>删除</a>"; 
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, indexColWidth:40, cols: cols,method:'POST',checkCol:true,multiSelect:true,
        url: WST.U('admin/goods/auditByPage'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'goodsSn',sortStatus:'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
// 批量审核通过
function toBatchAllow(){
	var rows = mmg.selectedRows();
	if(rows.length==0){
		 WST.msg('请选择商品',{icon:2});
		 return;
	}
	var ids = [];
	for(var i=0;i<rows.length;i++){
       ids.push(rows[i]['goodsId']); 
	}

    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
   	$.post(WST.U('admin/goods/batchAllow'),{ids:ids.join(',')},function(data,textStatus){
   			  layer.close(loading);
   			  var json = WST.toAdminJson(data);
   			  if(json.status=='1'){
   			    	WST.msg(json.msg,{icon:1});
   		            loadGrid(WST_CURR_PAGE);
   			  }else{
   			    	WST.msg(json.msg,{icon:2});
   			  }
   		});
}
// 批量审核不通过
function toBatchIllegal(){
	var rows = mmg.selectedRows();
	if(rows.length==0){
		 WST.msg('请选择商品',{icon:2});
		 return;
	}
	var ids = [];
	for(var i=0;i<rows.length;i++){
       ids.push(rows[i]['goodsId']); 
	}
	// 先显示弹出框,让用户输入审核不通原因
	var w = WST.open({type: 1,title:"商品不通过原因",shade: [0.6, '#000'],border: [0],
	    content: '<textarea id="illegalRemarks" rows="7" style="width:100%" maxLength="200"></textarea>',
	    area: ['500px', '260px'],btn: ['确定', '关闭窗口'],
        yes: function(index, layero){
        	var illegalRemarks = $.trim($('#illegalRemarks').val());
        	if(illegalRemarks==''){
        		WST.msg('请输入不通过原因!', {icon: 5});
        		return;
        	}
        	var ll = WST.msg('数据处理中，请稍候...');
		    $.post(WST.U('admin/goods/batchIllegal'),{ids:ids.join(','),illegalRemarks:illegalRemarks},function(data){
		    	layer.close(w);
		    	layer.close(ll);
		    	var json = WST.toAdminJson(data);
				if(json.status>0){
					WST.msg(json.msg, {icon: 1});
					loadGrid(WST_CURR_PAGE);
				}else{
					WST.msg(json.msg, {icon: 2});
				}
		   });
        }
	});
}

function allow(id,type){
	var box = WST.confirm({content:"您确定审核通过该商品吗?",yes:function(){
        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
        $.post(WST.U('admin/goods/allow'),{id:id},function(data,textStatus){
        			layer.close(loading);
        			var json = WST.toAdminJson(data);
        			if(json.status=='1'){
        			    WST.msg(json.msg,{icon:1});
        			    layer.close(box);
        			    loadGrid(WST_CURR_PAGE);
        			}else{
        			    WST.msg(json.msg,{icon:2});
        			}
        		});
         }});
}

function initIllegalGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'&nbsp;', name:'goodsName', width: 20, renderer: function(val,item,rowIndex){
            	return "<span class='weixin'><img class='img' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span>";
            }},
            {title:'商品名称', name:'goodsName', width: 100,sortable:true,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsName']+"</p></span>";
            }},
            {title:'商品编号', name:'goodsSn' ,width:60,sortable:true},
            {title:'所属店铺', name:'shopName' ,width:60,sortable:true},
            {title:'所属分类', name:'goodsCatName' ,width:60,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsName']+"</p></span>";
            }},
            {title:'违规原因', name:'illegalRemarks' ,width:170},
            {title:'操作', name:'' ,width:150, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' target='_blank' href='"+WST.U("home/goods/detail","goodsId="+item['goodsId']+"&key="+item['verfiycode'])+"'><i class='fa fa-search'></i>查看</a> ";
	            if(WST.GRANT.WGSP_04)h += "<a class='btn btn-blue' href='javascript:allow(" + item['goodsId'] + ",0)'><i class='fa fa-check'></i>审核通过</a> ";
	            if(WST.GRANT.WGSP_03)h += "<a class='btn btn-red' href='javascript:del(" + item['goodsId'] + ",0)'><i class='fa fa-trash-o'></i>删除</a></div> "; 
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('admin/goods/illegalByPage'), fullWidthRows: true, autoLoad: false,remoteSort: true,sortName:'goodsSn',sortStatus:'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function toolTip(){
    WST.toolTip();
}
