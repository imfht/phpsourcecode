var WSTHook_checkGoodsStock = [];
$(function(){
	$("embed").css('width','100%');
	WST.dropDownLayer(".item",".dorp-down-layer");
	$('.item-more').click(function(){
		if($(this).attr('v')==1){
			$('.hideItem').show(300);
			$(this).find("span").html("收起");
			$(this).find("i").attr({"class":"drop-up"});
			$(this).attr('v',0);
		}else{
			$('.hideItem').hide(300);
			$(this).find("span").html("更多选项");
			$(this).find("i").attr({"class":"drop-down-icon"});
			$(this).attr('v',1);
		}
	});
	$(".gallery-img").mouseenter(function(){
		$(".gallery-li").removeClass("hover");
		$(this).parent().addClass('hover');
		if($(this).hasClass('gvideo')){
			document.getElementById('previewVideo').play();
			$(".wst-video-box").show();
		}else{
			$(".wst-video-box").hide();
		}
	});
	$(".item-more").hover(function(){
		if($(this).find("i").hasClass("drop-down-icon")){
			$(this).find("i").attr({"class":"down-hover"});
		}else{
			$(this).find("i").attr({"class":"up-hover"});
		}
		
	},function(){
		if($(this).find("i").hasClass("down-hover")){
			$(this).find("i").attr({"class":"drop-down"});
		}else{
			$(this).find("i").attr({"class":"drop-up"});
		}
	});
	if(goodsInfo.sku){
		var specs,dv;
		for(var key in goodsInfo.sku){
			if(goodsInfo.sku[key].isDefault==1){
				specs = key.split(':');
				$('.j-option').each(function(){
					dv = $(this).attr('data-val')
					if($.inArray(dv,specs)>-1){
						$(this).addClass('j-selected');
					}
				})
				$('#buyNum').attr('data-max',goodsInfo.sku[key].specStock);
			}
		}
	}else{
		$('#buyNum').attr('data-max',goodsInfo.goodsStock);
	}
	checkGoodsStock();
	//图片放大镜效果
	CloudZoom.quickStart();
	imagesMove({id:'.goods-pics',items:'.items'});
	//选择规格
	$('.spec .j-option').click(function(){
		$(this).addClass('j-selected').siblings().removeClass('j-selected');
		checkGoodsStock();
	});
	fixedbar();
	$('#tab').TabPanel({tab:0,callback:function(no){
		if(no==1)queryByPage();
		if(no==2)queryConsult();
	}});
	contrastGoods(1,0,2);
	// 取消'手机购买'click事件
	$('#wx_qrcode').unbind();
});
function fixedbar(){
    var offsetTop = $("#goodsTabs").offset().top;  
    $(window).scroll(function() {  
        var scrollTop = $(document).scrollTop();  
        if (scrollTop > offsetTop){  
        	$('#addCart2').show();
            $("#goodsTabs").css("position","fixed");  
            $("#wx_qrcode").addClass('wx_qrcode_fixed');  
        }else{  
        	$('#addCart2').hide();
            $("#goodsTabs").css("position", "static");  
            $("#wx_qrcode").removeClass('wx_qrcode_fixed');  
        }  
    });   
}
function checkGoodsStock(){
	var specIds = [],stock = 0,goodsPrice=0,marketPrice=0;
	if(goodsInfo.isSpec==1){
		$('.j-selected').each(function(){
			specIds.push(parseInt($(this).attr('data-val'),10));
		});
		specIds.sort(function(a,b){return a-b;});
		if(goodsInfo.sku[specIds.join(':')]){
			stock = goodsInfo.sku[specIds.join(':')].specStock;
			marketPrice = goodsInfo.sku[specIds.join(':')].marketPrice;
			goodsPrice = goodsInfo.sku[specIds.join(':')].specPrice;
		}
	}else{
		stock = goodsInfo.goodsStock;
		marketPrice = goodsInfo.marketPrice;
		goodsPrice = goodsInfo.goodsPrice;
	}
	var obj = {stock:stock,marketPrice:marketPrice,goodsPrice:goodsPrice};
	if(WSTHook_checkGoodsStock.length>0){
		for(var i=0;i<WSTHook_checkGoodsStock.length;i++){
			delete window['callback_'+WSTHook_checkGoodsStock[i]];
			obj = window[WSTHook_checkGoodsStock[i]](obj); 
			if(window['callback_'+WSTHook_checkGoodsStock[i]]){
				window['callback_'+WSTHook_checkGoodsStock[i]]();
				return;
			 }
		}
	}
    stock = obj.stock;
    marketPrice = obj.marketPrice;
    goodsPrice = obj.goodsPrice;
	var iptv = parseInt($('#buyNum').val(),10);
	iptv = (iptv>stock)?stock:iptv;
	$('#buyNum').val(iptv);
	$('#buyNum').attr('data-max',stock);
	$('#goods-stock').html(stock);
	$('#j-market-price').html('￥'+marketPrice);
	$('#j-shop-price').html(goodsPrice);
	if(stock<=0){
		$('#addBtn').addClass('disabled');
		$('#buyBtn').addClass('disabled');
	}else{
		$('#addBtn').removeClass('disabled');
		$('#buyBtn').removeClass('disabled');
	}
}

function imagesMove(opts){
	var tempLength = 0; //临时变量,当前移动的长度
	var viewNum = 5; //设置每次显示图片的个数量
	var moveNum = 2; //每次移动的数量
	var moveTime = 300; //移动速度,毫秒
	var scrollDiv = $(opts.id+" "+opts.items+" ul"); //进行移动动画的容器
	var scrollItems = $(opts.id+" "+opts.items+" ul li"); //移动容器里的集合
	var moveLength = scrollItems.eq(0).width() * moveNum; //计算每次移动的长度
	var countLength = (scrollItems.length - viewNum) * scrollItems.eq(0).width(); //计算总长度,总个数*单个长度
	  
	//下一张
	$(opts.id+" .next").bind("click",function(){
		if(tempLength < countLength){
			if((countLength - tempLength) > moveLength){
				scrollDiv.animate({left:"-=" + moveLength + "px"}, moveTime);
				tempLength += moveLength;
			}else{
				scrollDiv.animate({left:"-=" + (countLength - tempLength) + "px"}, moveTime);
				tempLength += (countLength - tempLength);
			}
		}
	});
	//上一张
	$(opts.id+" .prev").bind("click",function(){
		if(tempLength > 0){
			if(tempLength > moveLength){
				scrollDiv.animate({left: "+=" + moveLength + "px"}, moveTime);
				tempLength -= moveLength;
			}else{
				scrollDiv.animate({left: "+=" + tempLength + "px"}, moveTime);
				tempLength = 0;
			}
		}
	});
}


/****************** 商品评价 ******************/
function showImg(id){
  layer.photos({
      photos: '#img-file-'+id
    });
}
function apprfilter(type){
	$('.appr-filterbox li a').removeClass('curr');
	$('#filtertype').val(type);
	queryByPage(1);
}

var _first=true;
function queryByPage(p){
  var params = {};
  params.page = p;
  params.goodsId = goodsInfo.id;
  params.anonymous = 1;
  params.type = $('#filtertype').val();
  $.post(WST.U('home/goodsappraises/getById'),params,function(data,textStatus){
      var json = WST.toJson(data);
      if(json.status==1 && json.data.data){
          var gettpl = document.getElementById('tblist').innerHTML;
          laytpl(gettpl).render(json.data.data, function(html){
            $('#ga-box').html(html);
            for(var g=0;g<=json.data.data.length;g++){
              showImg(g);
            }
          });
          //  各评价数.
          $('#totalNum').html(json.data.sum);
          $('#bestNum').html(json.data.bestNum);
          $('#goodNum').html(json.data.goodNum);
          $('#badNum').html(json.data.badNum);
          $('#picNum').html(json.data.picNum);
          // 选中当前筛选条件
          $('#'+params.type).addClass('curr');
          if(_first && json.data.sum>0){
	          // 好、中、差评率
	          var best = parseInt(json.data.bestNum/json.data.sum*100);
	          var good = parseInt(json.data.goodNum/json.data.sum*100);
	          var bad = 100-best-good;
	          $('.best_percent').html(best);
	          $('.good_percent').html(good);
	          $('.bad_percent').html(bad);
	          // 背景色
	          $('#best_percentbg').css({width:best+'%'});
	          $('#good_percentbg').css({width:good+'%'});
	          $('#bad_percentbg').css({width:bad+'%'});
	          _first = false;
          }

          $('.j-lazyImg').lazyload({ effect: "fadeIn",failurelimit : 10,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});
          $('.apprimg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.USER_LOGO});//会员默认头像
           laypage({
               cont: 'pager', 
               pages:json.data.last_page, 
               curr: json.data.current_page,
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
function addCart(type,iptId){
	if(window.conf.IS_LOGIN==0){
		WST.loginWindow();
		return;
	}
	var goodsSpecId = 0;
	if(goodsInfo.isSpec==1){
		var specIds = [];
		$('.j-selected').each(function(){
			specIds.push($(this).attr('data-val'));
		});

		if(specIds.length==0){
			WST.msg('请选择你要购买的商品信息',{icon:2});
		}
		specIds.sort(function(a,b){return a-b});
		if(goodsInfo.sku[specIds.join(':')]){
			goodsSpecId = goodsInfo.sku[specIds.join(':')].id;
		}
	}
	var buyNum = $(iptId)[0]?$(iptId).val():1;
	$.post(WST.U('home/carts/addCart'),{goodsId:goodsInfo.id,goodsSpecId:goodsSpecId,buyNum:buyNum,type:type,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1},function(){
	    	 	if(json.data && json.data.forward){
		    	 	 location.href=WST.U('home/carts/'+json.data.forward);
		    	 }else{
		    	 	if(type==1){
		    		 location.href=WST.U('home/carts/settlement');
		    	    }
		    	 }
	    	 });
	    	 getRightCart();
	     }else{
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
/******************* 商品咨询 ********************/

// 提交商品咨询
function consultCommit(){
	var params={};
	params.goodsId = goodsInfo.id;
	$('[name="pointType"]').each(function(k,v){
		if(v.checked){params.consultType = v.value}
	})
	if(!params.consultType){
		WST.msg('请选择咨询类别',{icon:2});
		return;
	}
	params.consultContent = $('#consultContent').val();
	if(params.consultContent == ''){
		WST.msg('请输入咨询内容',{icon:2});
		return;
	}
	if(params.consultContent.length<3 || params.consultContent.length>200){
		WST.msg('咨询内容应为3-200个字',{icon:2});
		return;
	}
	var load = WST.load({msg:'正在提交，请稍后...'});
	$.post(WST.U('home/goodsconsult/add'),params,function(responData){
		layer.close(load); 
		var json = WST.toJson(responData);
		if(json.status==1){
			 // 发布成功
			 WST.msg(json.msg,{icon:1},function(){
			 	// 重置咨询输入框
			 	$('[name="pointType"]').map(function(k,v){v.checked=false;});
			 	$('#consultContent').val(' ');
			 	queryConsult(0);
			 });
		}else{
			WST.msg(json.msg,{icon:2});
		}
	})
}
function queryConsult(p){
	var params = {};
		params.page = p;
		params.goodsId = goodsInfo.id;
		params.type = $('#consultType').val();
		params.consultKey = $('#consultKey').val();
  $.post(WST.U('home/goodsconsult/listquery'),params,function(data,textStatus){
      var json = WST.toJson(data);
      if(json.status==1 && json.data.data){
          var gettpl = document.getElementById('gclist').innerHTML;
          laytpl(gettpl).render(json.data.data, function(html){
            	$('#consultBox').html(html);
          });
           laypage({
               cont: 'consult-pager', 
               pages:json.data.last_page, 
               curr: json.data.current_page,
               skin: '#e23e3d',
               groups: 3,
               jump: function(e, first){
                    if(!first){
                      queryConsult(e.curr);
                    }
                  } 
            });
        }  
  });
}
//筛选咨询类别
function filterConsult(obj, type){
	$('.gc-filter').each(function(k,v){
		$(v).removeClass('curr');
	})
	$(obj).addClass('curr');
	$('#consultType').val(type);
	queryConsult(0);
}
//对比商品
function contrastGoods(show,id,type){
	if(show==1){
		$.post(WST.U('home/goods/contrastGoods'),{id:id},function(data,textStatus){
			var json = WST.toJson(data);
			if(json.status==1){
				if(type==2 && json.data)$("#j-cont-frame").addClass('show');
				var gettpl = document.getElementById('colist').innerHTML;
				laytpl(gettpl).render(json, function(html){
					$('#contrastList').html(html);
				});
				$('.contImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
			}else{
				WST.msg(json.msg,{icon:2});
			}
			if(type==1)$("#j-cont-frame").addClass('show');
		});
	}else{
		$("#j-cont-frame").removeClass('show');
	}
}
//删除
function contrastDel(id){
	$.post(WST.U('home/goods/contrastDel'),{id:id},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			contrastGoods(1,0,1);
		}
	});
}
function informs($goodsId){
	if(window.conf.IS_LOGIN==0){
		WST.loginWindow();
		return;
	}
	location.href=WST.U("home/informs/inform",'id='+$goodsId);
	}
