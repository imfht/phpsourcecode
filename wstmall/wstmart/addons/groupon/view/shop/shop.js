var mmg;
function initGrid(p){
   var h = WST.pageHeight();
   var cols = [
       {title:'商品图片', name:'goodsName', width: 30,renderer:function(val,item,rowIndex){
               var html = [];
               html.push('<div class="goods-img"><a href="'+WST.AU("groupon://goods/detail","id="+item["grouponId"])+'" target="_blank">');
               html.push("<span class='weixin'><img class='img' style='height:60px;width:60px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;max-width: 200px;max-height: 200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span></a></div>");
               return html.join('');
           }},
       {title:'商品名称', name:'goodsName', width: 300},
        {title:'商品编号', name:'goodsSn', width: 90},
        {title:'团购价格', name:'grouponPrice', width: 50,renderer:function(val,item,rowIndex){
        	return '￥'+item['grouponPrice'];
        }},
        {title:'开始时间', name:'startTime', width: 100},
        {title:'结束时间', name:'endTime', width: 100},
        {title:'团购数量', name:'grouponNum', width: 30},
        {title:'已团数量', name:'orderNum', width: 30,renderer:function(val,item,rowIndex){
            return "<a style='color:blue' href='"+WST.AU("groupon://shops/orders","grouponId="+item['grouponId'])+"'>"+item['orderNum']+"</a>";
        }},
        {title:'状态', name:'grouponStatus', width: 70,renderer:function(val,item,rowIndex){
        	if(item['grouponStatus']==0){
                return "<span class='statu-wait'><i class='fa fa-clock-o'></i>待审核</span>";
            }else if(item['grouponStatus']==-1){
                return "<span class='statu-no' title='"+item['illegalRemarks']+"'><i class='fa fa-ban'></i>审核不通过</span>";
            }else{
               if(item['status']==0){
                   return "<span class='lbel lbel-info'>未开始</span>";
               }else if(item['status']==1){
                   return "<span class='lbel lbel-success'>进行中</span>";
               }else{
                   return "<span class='lbel lbel-gray'>已过期</span>";
               }
            }
        }},
        {title:'操作', name:'' ,width:160,renderer:function(val,item,rowIndex){
        	var html = [];
            if(item['grouponStatus']==1){
	           html.push("<a class='btn btn-blue' href='"+WST.AU("groupon://goods/detail","id="+item["grouponId"])+"' target='_blank'><i class='fa fa-search'></i>查看</a>");
	        }
	        html.push(" <a class='btn btn-blue' href='javascript:toEdit("+item["grouponId"]+")'><i class='fa fa-pencil'></i>编辑</a>");
	        html.push(" <a class='btn btn-red' href='javascript:del("+item["grouponId"]+")'><i class='fa fa-trash-o'></i>删除</a>");
	        return html.join('');
        }}
    ];

    mmg = $('.mmg').mmGrid({height: h-83,indexCol: true, cols: cols,method:'POST',
        url: WST.AU('groupon://shops/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}

function loadGrid(p){
    var params = {};
    params = WST.getParams('.s-query');
    params.key = $.trim($('#key').val());
    p=(p<=1)?1:p;
    params.page=p;
    mmg.load(params);
}

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
    location.href = WST.AU('groupon://shops/edit','id='+id+'&p='+WST_CURR_PAGE);
}
function toView(id){
	location.href = WST.AU('groupon://goods/detail','id='+id);
}

function save(p){
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
		            	location.href = WST.AU('groupon://shops/groupon','p='+p);
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
		        loadGrid(WST_CURR_PAGE);
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