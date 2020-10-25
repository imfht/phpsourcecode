function queryByPage(p){
	$('#list').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params = {};
	params.goodsName = $.trim($('#goodsName').val());
	params.page = p;
	$.post(WST.AU('groupon://shops/pageQuery'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.data){
	    	if(params.page>json.last_page && json.last_page >0){
               queryByPage(json.last_page);
               return;
            }
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#list').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:WST.conf.RESOURCE_PATH+'/'+WST.conf.GOODS_LOGO});//商品默认图片
	       	});
	       	laypage({
		        	 cont: 'pager', 
		        	 pages:json.last_page, 
		        	 curr: json.current_page,
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		    if(!first){
		        		    	queryByPage(e.curr);
		        		    }
		        	 } 
		    });
       	}  
	});
}
function getShopsCats(objId,pVal,objVal){
	$('#'+objId).empty();
	$.post(WST.U('home/shopcats/listQuery'),{parentId:pVal},function(data,textStatus){
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
function searchGoods(){
	var params = {};
	params.shopCatId1 = $('#shopCatId1').val();
	params.shopCatId2 = $('#shopCatId2').val();
    params.goodsName = $('#goodsName').val();
    if(params.shopCatId1=='' && params.goodsName==''){
		 WST.msg('请至少选择商品分类',{icon:2});
		 return;
	}
	$('#goodsId').empty();
    var loading = WST.load({msg:'正在查询数据，请稍后...'});
	$.post(WST.AU("groupon://shops/searchGoods"),params,function(data,textStatus){
		layer.close(loading);
	    var json = WST.toJson(data);
	    if(json.status==1 && json.data){
	    	var html = [];
	    	var option1 = [];
	    	for(var i=0;i<json.data.length;i++){
	    		if(i==0)option1 = json.data[i];
                html.push('<option value="'+json.data[i].goodsId+'" gt="'+json.data[i].goodsType+'" mp="'+json.data[i].marketPrice+'" sp="'+json.data[i].marketPrice+'">'+json.data[i].goodsName+'</option>');
	    	}
	    	$('#goodsId').html(html.join(''));
	    	$('#marketPrice').html("￥"+option1.marketPrice);
	    }
	});
}
function changeGoods(obj){
    var opts = $(obj).find("option:selected");
    $('#marketPrice').html(opts.attr('mp'));
}
function toEdit(id){
    location.href = WST.AU('groupon://shops/edit','id='+id);
}
function toView(id){
	location.href = WST.AU('groupon://goods/detail','id='+id);
}

function save(){
    $('#grouponform').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			if(params.goodsId==''){
				WST.msg('请选择要参与团购的商品',{icon:2});
				return;
			}
			var loading = WST.load({msg:'正在提交数据，请稍后...'});
			$.post(WST.AU("groupon://shops/toEdit"),params,function(data,textStatus){
				layer.close(loading);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
		            	location.href = WST.AU('groupon://shops/groupon');
		            });
			    }else{
			    	WST.msg(json.msg,{icon:2});
			    }
			});
		}
	});
}
function del(id){
	var loading = WST.load({msg:'正在提交请求，请稍后...'});
	$.post(WST.AU("groupon://shops/del"),{id:id},function(data,textStatus){
		layer.close(loading);
	    var json = WST.toJson(data);
		if(json.status==1){
		    WST.msg(json.msg,{icon:1},function(){
		        queryByPage(WSTCurrPage);
		    });
	    }else{
			WST.msg(json.msg,{icon:2});
		}
	});
}
function listByPage(p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.s-ipt');
	params.key = $.trim($('#key').val());
	params.page = p;
	$.post(WST.AU('groupon://shops/pageQueryByGoods'),params,function(data,textStatus){
		$('#loading').hide();
	    var json = WST.toJson(data);
	    $('.j-order-row').remove();
	    if(json.status==1){
	    	json = json.data;
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$(html).insertAfter('#loadingBdy');
	       		$('.gImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:WST.conf.RESOURCE_PATH+'/'+WST.conf.GOODS_LOGO});
	       	});
	       	if(json.last_page>1){
	       		laypage({
		        	 cont: 'pager', 
		        	 pages:json.last_page, 
		        	 curr: json.current_page,
		        	 skin: '#e23e3d',
		        	 groups: 3,
		        	 jump: function(e, first){
		        		 if(!first){
		        			 listByPage(e.curr);
		        		 }
		        	 } 
		        });
	       	}else{

	       		$('#pager').empty();
	       	}
       	} 
	});
}
function view(id){
    location.href=WST.U('home/orders/view','id='+id);
}