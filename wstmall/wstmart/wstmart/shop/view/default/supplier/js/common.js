$(function(){

	$('.goodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
	$('.shopsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.SHOP_LOGO});//店铺默认头像
	$('.usersImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.USER_LOGO});//会员默认头像

	WST.dropDownLayerCart(".wst-cart-box",".wst-cart-boxs");
});

WST.search = function(){
	WST.goodsSearch($.trim($('#search-ipt').val()));
}
WST.goodsSearch = function(v){
	location.href = WST.U('shop/supplierindex/index','keyword='+v,true);
}
/**
 * 去除url中指定的参数(用于分页)
 */
WST.splitURL = function(spchar){
	var url = location.href;
	var urlist = url.split("?");
	var furl = new Array();
	var fparams = new Array();
		furl.push(urlist[0]);
	if(urlist.length>1){
		var urlparam = urlist[1];
			params = urlparam.split("&");
		for(var i=0; i<params.length; i++){
			var vparam = params[i];
			var param = vparam.split("=");
			if(param[0]!=spchar){
				fparams.push(vparam);
			}
		}
		if(fparams.length>0){
			furl.push(fparams.join("&"));
		}
		
	}
	if(furl.length>1){
		return furl.join("?");
	}else{
		return furl.join("");
	}
}


WST.dropDownLayerCart = function(dropdown,layer){
	$(dropdown).hover(function () {
        $(this).find(layer).show();
        WST.checkCart();
    }, function () {
    	$(this).find(layer).hide();
    });
	$(layer).hover(function (event) {
		event.stopPropagation();
		$(this).show();
    }, function (event) {
    	event.stopPropagation();
    	$(this).hide();
    });
}

WST.addCart = function(goodsId){
	
	$.post(WST.U('shop/suppliercarts/addCart'),{goodsId:goodsId,buyNum:1},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1,time:600,shade:false});
	    	 if(json.data && json.data.forward){
	    	 	 location.href=WST.U('shop/suppliercarts/'+json.data.forward);
	    	 }
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}

WST.delCart = function(id){
	WST.confirm({content:'您确定要删除该商品吗？',yes:function(index){
		$.post(WST.U('shop/suppliercarts/delCart'),{id:id,rnd:Math.random()},function(data,textStatus){
		     var json = WST.toJson(data);
		     if(json.status==1){
		    	 WST.msg(json.msg,{icon:1});
		         location.href=WST.U('shop/suppliercarts/index');
		     }else{
		    	 WST.msg(json.msg,{icon:2});
		     }
		});
	}});
}
WST.changeCartGoods = function(id,buyNum,isCheck){
	$.post(WST.U('shop/suppliercarts/changeCartGoods'),{id:id,isCheck:isCheck,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status!=1){
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
WST.dropDownLayerCart = function(dropdown,layer){
	$(dropdown).hover(function () {
        $(this).find(layer).show();
        WST.checkCart();
    }, function () {
    	$(this).find(layer).hide();
    });
	$(layer).hover(function (event) {
		event.stopPropagation();
		$(this).show();
    }, function (event) {
    	event.stopPropagation();
    	$(this).hide();
    });
}
WST.delCheckCart = function(id,func){
	$.post(WST.U('shop/suppliercarts/delCart'),{id:id,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1});
	    	 WST.checkCart();
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
WST.checkCart = function(){
	$('#list-carts2').html('');
	$('#list-carts3').html('');
	$('#list-carts').html('<div style="padding:32px 0px 77px 112px;"><img src="'+WST.conf.ROOT+'/wstmart/shop/view/default/supplier/img/loading.gif">正在加载数据...</div>');
	$.post(WST.U('shop/suppliercarts/getCartInfo'),'',function(data) {
		var json = WST.toJson(data,true);
		$('#goodsTotalNum').hide();
		if(json.status==1){
			json = json.data;
			if(json.list.length>0){
				$('#goodsTotalNum').show();
				var gettpl = document.getElementById('list-cart').innerHTML;
				laytpl(gettpl).render(json, function(html){
					$('#list-carts').html(html);
				});
				$('#list-carts2').html('<div class="comm" id="list-comm">&nbsp;&nbsp;共<span>'+json.goodsTotalNum+'</span>件商品<span class="span2">￥'+json.goodsTotalMoney+'</span></div>');
				$('#list-carts3').html('<a href="'+window.conf.ROOT+'/shop/suppliercarts/index" class="btn btn-3">去进货单结算</a>');
				$('.goodsImgc').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
				if(json.list.length>5){
					$('#list-carts').css('overflow-y','scroll').css('height','416');
				}
			}else{
				$('#list-carts').html('<p class="carts">进货单中空空如也，赶紧去选购吧～</p>');
			}
			$('#goodsTotalNum').html(json.goodsTotalNum);
		}else{
			$('#list-carts').html('<p class="carts">进货单中空空如也，赶紧去选购吧～</p>');
			$('#goodsTotalNum').html(0);
		}
	});
}
WST.changeIptNum = function(diffNum,iptId,btnId,id,func){
	var suffix = (id)?"_"+id:"";
	var iptElem = $(iptId+suffix);
	var minVal = parseInt(iptElem.attr('data-min'),10);
	var maxVal = parseInt(iptElem.attr('data-max'),10);
	var tmp = 0;
	if(maxVal<minVal){
		tmp = maxVal;
		maxVal = minVal;
		minVal = tmp;
	}
	var num = parseInt(iptElem.val(),10);
	num = num?num:1;
	num = num + diffNum;
	btnId = btnId.split(',');
	$(btnId[0]+suffix).css('color','#666');
	$(btnId[1]+suffix).css('color','#666');
	if(minVal>=num){
		num=minVal;
		$(btnId[0]+suffix).css('color','#f1f1f1');
	}
	if(maxVal<=num){
		num=maxVal;
		$(btnId[1]+suffix).css('color','#f1f1f1');
	}
	iptElem.val(num);
	if(suffix!='')WST.changeCartGoods(id,num,-1);
	if(func){
		var fn = window[func];
		fn();
	}
}
WST.supplierQQ = function(val){
	if(WST.blank(val) !=''){
      return [
              '<a href="tencent://message/?uin='+val+'&Site=QQ交谈&Menu=yes">',
		      '<img border="0" src='+window.conf.__HTTP__+'wpa.qq.com/pa?p=1:'+val+':7" alt="QQ交谈" width="71" height="24" />',
		      '</a>'
		      ].join('');
	}else{
		return '';
	}
}
WST.supplierWangWang = function(val){
	if(WST.blank(val) !=''){
		return [
	           '<a target="_blank" href='+window.conf.__HTTP__+'www.taobao.com/webww/ww.php?ver=3&touid='+val+'&siteid=cntaobao&status=1&charset=utf-8">',
		       '<img border="0" src='+window.conf.__HTTP__+'amos.alicdn.com/realonline.aw?v=2&uid='+val+'&site=cntaobao&s=1&charset=utf-8" alt="和我联系" />',
	           '</a>'
		       ].join('');
	}else{
		return '';
	}
}
