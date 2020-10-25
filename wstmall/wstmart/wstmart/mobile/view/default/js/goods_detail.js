var WSTHook_checkGoodsStock = [];
jQuery.noConflict();
  // 查看大图
  function gViewImg(index,obj){
	var pswpElement = document.querySelectorAll('.pswp')[0];
	var gallery = $(obj).parent().data("gallery");
	if(gallery!=''){
	    gallery = gallery.split(',').map(function(imgUrl,i){
	      imgUrl = WST.conf.RESOURCE_PATH+"/"+imgUrl;
	      var _obj = { src:imgUrl, w:0, h:0 };
	      return _obj;
	    })
	  }
	// build items array
	if(!gallery || gallery.length==0)return;
	// define options (if needed)
	var options = {
	  index: index
	};
	// Initializes and opens PhotoSwipe
	var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, gallery, options);
	gallery.init();
	gallery.listen('imageLoadComplete', function(index, item) { 
       	if (item.h < 1 || item.w < 1) {
		  var img = new Image();
		  img.onload = function(){
			item.w = img.width;
			item.h = img.height;
			gallery.invalidateCurrItems()
			gallery.updateSize(true);
		  }
		  img.src = item.src
		}
    });  
  }
  



//切换
function pageSwitch(obj,type){
	$('.ui-tab-nav li.switch').removeClass('active');
	$(obj).addClass('active');
	if(type!=1){
		$('#goods'+type).show().siblings('section.ui-container').hide();
	}else{
		$('#goods'+type).show().siblings('section.ui-container').hide();
		$('#goods2').show();
	}
	if(type==1){
		var offsetTop = $("#goods1").offset().top;
        var scrollTop = $(window).scrollTop()-100; 
        if (scrollTop > offsetTop){
            $("#goods-header").show();
        }else{  
            $("#goods-header").hide();
        }
	}
	if(type==2){
		$('#goods2').css('border-top','0.47rem solid transparent');
	}else{
		$('#goods2').css('border-top','0');
	}
	if(type==2 || type==3){
		$(window).scrollTop(0);
		$("#goods-header").show();
	}
}
//商品评价列表
function evaluateList(){
    loading = true;
    var param = {};
    param.goodsId = $('#goodsId').val();
    param.type = $('#evaluateType').val();
	param.pagesize = 10;
	param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/goodsappraises/getById'), param,function(data){
        var json = WST.toJson(data);
        $('#currPage').val(json.data.current_page);
        $('#totalPage').val(json.data.last_page);
        var gettpl = document.getElementById('list').innerHTML;
        laytpl(gettpl).render(json.data.data, function(html){
            $('#evaluate-list').append(html);
        });
        loading = false;
        echo.init();//图片懒加载
    });
}
function evaluateSwitch(obj,type){
	$('#evaluateType').val(type);
	$(obj).addClass('active').siblings('.wst-ev-term .ui-col').removeClass('active');
    $('#currPage').val('0');
    $('#totalPage').val('0');
	$('#evaluate-list').html('');
	evaluateList();
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
	$("embed").removeAttr('height').css('width','100%');
	//商品图片
	new Swiper('.swiper-container', {
		autoplay: false,
		autoHeight: true, //高度随内容变化
		width: window.innerWidth,
		on: {
			resize: function(){
				this.params.width = window.innerWidth;
				this.update();
			},
		} ,
		pagination: {
			el: '.swiper-pagination',
			type: 'bullets',
		},
	});
    evaluateList();
    fixedHeader();
	WST.imgAdapt('j-imgAdapt');
	var detail = $(".wst-go-details");
	var offSet = detail.offset().top; 
	var tab0 = $('.ui-tab-nav li').get(0);
	var tab1 = $('.ui-tab-nav li').get(1);
    $(window).scroll(function(){ 
        
    	if ($(window).scrollTop() > offSet){
    		if($(tab0).hasClass('active')){
	    		$(tab0).removeClass('active');
				$(tab1).addClass('active');
			}
		}else{
			if($(tab1).hasClass('active')){
				$(tab0).addClass('active');
				$(tab1).removeClass('active');
			}
		}
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	evaluateList();
            }
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
						$(this).addClass('active');
					}
				})
				$('#buyNum').attr('data-max',goodsInfo.sku[key].specStock);
			}
		}
	}else{
		$('#buyNum').attr('data-max',goodsInfo.goodsStock);
	}
	checkGoodsStock();
	//选择规格
	$('.spec .j-option').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		if($(this).attr('data-image')){
		    $("#specImage").attr('src',$(this).attr('data-image'));
	    }
		checkGoodsStock();
	});
    //弹框的高度
    var dataHeight = $("#frame").css('height');
    var cartHeight = parseInt($("#frame-cart").css('height'))+52+'px';
    if(parseInt(dataHeight)>230){
        $('#content').css('overflow-y','scroll').css('height','200');
    }
    if(parseInt(cartHeight)>420){
        $('#standard').css('overflow-y','scroll').css('height','260');
    }
    $("#frame").css('bottom','-100%');
    $("#frame-cart").css('bottom','-100%');
	echo.init({
		offset:2500
	});//图片懒加载
});
function checkGoodsStock(){
	var specIds = [],stock = 0,goodsPrice=0,marketPrice=0;
	if(goodsInfo.isSpec==1){
		$('.spec .active').each(function(){
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
	$('#goods-stock').html(stock);
	$('#buyNum').attr('data-max',stock);
	$('#j-market-price').html('¥'+marketPrice);
	$('#j-shop-price').html('¥'+goodsPrice);
	if(stock<=0){
		$('#addBtn').addClass('disabled');
		$('#buyBtn').addClass('disabled');
	}else{
		$('#addBtn').removeClass('disabled');
		$('#buyBtn').removeClass('disabled');
	}
}
//导航
function fixedHeader(){
    var offsetTop = $("#goods1").offset().top;
    $(window).scroll(function() {
        if($("#goods1").css("display")!='none'){
	        var scrollTop = $(window).scrollTop()-100; 
	        if (scrollTop > offsetTop){
	            $("#goods-header").show();
	        }else{  
	            $("#goods-header").hide();
	        }  
        }else{
        	$("#goods-header").show();
        }
    });
}
function inMore(){
	if($("#arrow").css("display")=='none'){
		jQuery('#arrow').show(200);
		$("#layer").show();
	}else{
		jQuery('#arrow').hide(100);
		$("#layer").hide();
	}
}
//弹框
function dataShow(){
	jQuery('#cover').attr("onclick","javascript:dataHide();").show();
	jQuery('#frame').animate({"bottom": 0}, 500);
}
function dataHide(){
	jQuery('#frame').animate({'bottom': '-100%'}, 500);
	jQuery('#cover').hide();
}
//弹框
var type;
function cartShow(t){
	type = t;
	jQuery('#cover').attr("onclick","javascript:cartHide();").show();
	jQuery('#frame-cart').animate({"bottom": 0}, 500);
}
function cartHide(){
	jQuery('#frame-cart').animate({'bottom': '-100%'}, 500);
	jQuery('#cover').hide();
}
//加入购物车
function addCart(){
	var goodsSpecId = 0;
	if(goodsInfo.isSpec==1){
		var specIds = [];
		$('.spec .active').each(function(){
			specIds.push($(this).attr('data-val'));
		});
		if(specIds.length==0){
			WST.msg('请选择你要购买的商品信息','info');
		}
		specIds.sort(function(a,b){return a-b;});
		if(goodsInfo.sku[specIds.join(':')]){
			goodsSpecId = goodsInfo.sku[specIds.join(':')].id;
		}
	}
	var goodsType = $("#goodsType").val();
	var buyNum = $("#buyNum").val()?$("#buyNum").val():1;
	$.post(WST.U('mobile/carts/addCart'),{goodsId:goodsInfo.id,goodsSpecId:goodsSpecId,buyNum:buyNum,type:type,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,'success');
	    	 cartHide();
	    	 if(type==1){
	    		 setTimeout(function(){
	    			 if(goodsType==1){
	    				 location.href=WST.U('mobile/carts/'+json.data.forward);
	    			 }else{
	    				 location.href=WST.U('mobile/carts/settlement');
	    			 }
	    		 },1000);
	    	 }else{
	    		 if(json.cartNum>0)$("#cartNum").html('<span>'+json.cartNum+'</span>');
	    	 }
	     }else{
	    	 WST.msg(json.msg,'info');
	     }
	});
}
document.addEventListener('touchmove', function(event) {
    //阻止背景页面滚动,
    if(!jQuery("#cover").is(":hidden")){
        event.preventDefault();
    }
    if(!jQuery("#layer").is(":hidden")){
        event.preventDefault();
    }
})


function dialogShare(){
	reloadPoster(1);
}
function reloadPoster(isNew){
	var goodsId = goodsInfo.id;
	$('#Load').show();
	$.post(WST.U('mobile/goods/moCreatePoster'), {goodsId:goodsId,isNew:isNew},function(data){
 		$('#Load').hide();
        var json = WST.toJson(data);
        if(json.status==1){
        	$("#shareImg").html("<img src='"+window.conf.ROOT+"/"+json.data.shareImg+"?v="+Math.random()+"' style='width:3rem;height:4.96rem;border-radius: 6px;'/>");
        	$(".reload-btn-box").show();
        	$("#wst-di-qrcod").dialog("show");
        }
    });
}