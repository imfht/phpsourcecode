/*店长推荐*/
function gpanelOver2(obj){
var sid = $(obj).attr("id");
var ids = sid.split("_");
var preid = ids[0]+"_"+ids[1];

$("li[id^="+preid+"_]").removeClass("j-s-rec-selected");

$("#"+sid).addClass("j-s-rec-selected");

$("div[id^="+preid+"_]").hide();
$("#"+sid+"_pl").show();
}

/*楼层*/
function gpanelOver(obj){
	var sid = $(obj).attr("id");

	var index = $(obj).attr('c');

	var ids = sid.split("_");
	var preid = ids[0]+"_"+ids[1];
	
	$("li[id^="+preid+"_]").removeClass("j-tab-selected"+index);
	$("#"+sid).addClass("j-tab-selected"+index);
	
	$("div[id^="+preid+"_]").hide();
	$("#"+sid+"_pl").show();
}



function searchShopsGoods(){
	var params = new Array();
	params.push("shopId=" + $("#shopId").val());
	params.push("goodsName=" + $("#goodsName").val());
	document.location.href = WST.U('home/shops/home',params.join('&'),true);
}
$(function(){
	$(".s-goods").hover(function(){
		$(this).find(".s-add-cart").slideDown(100);
	},function(){
		$(this).find(".s-add-cart").slideUp(100);
	});
})


$(function(){
	if($('.s-wst-slide-items a').length>1)WST.slides('.s-wst-slide');
	$('.shop-cat1').hover(function(){
		$(this).addClass('ct1-hover');
		var cid = $(this).attr('cid');

		var h = 66.3*cid+'px';
		$('.cid'+cid).css('top',h);
		$('.cid'+cid).show();
	},function(){
		$(this).removeClass('ct1-hover');
		var cid = $(this).attr('cid');
		$('.cid'+cid).hide();
	})


	$('.shop-cat2').hover(function(){
		var cid = $(this).attr('cid');
		$('#ct1-'+cid).addClass('ct1-hover');
		$(this).show();
	},function(){
		var cid = $(this).attr('cid');
		$('#ct1-'+cid).removeClass('ct1-hover');
		$(this).hide();
	});



	$('.s-cat').hover(function(){
	  $('.s-cat-head').addClass('s-cat-head-hover');
	  $(this).show();
	},function(){
	  $('.s-cat-head').removeClass('s-cat-head-hover');
	  $(this).hide();
	});

	
	$('.s-cat-head').hover(function(){
	  $(this).addClass('s-cat-head-hover');
	  $('.s-cat').css({left:$('.s-cat-head').offset().left}).show();
	},function(){
	  $(this).removeClass('s-cat-head-hover');
	  $('.s-cat').hide();
	})


});
function init() {
  var longitude = $('#longitude').val();
  var latitude = $('#latitude').val();
  var shopName = $('#shopName').val();
  
  var myLatlng = new qq.maps.LatLng(latitude,longitude);
  var myOptions = {
    zoom: 15,               
    center: myLatlng,     
    mapTypeId: qq.maps.MapTypeId.ROADMAP  
  }
  var map = new qq.maps.Map(document.getElementById("container"), myOptions);
  var marker = new qq.maps.Marker({
        position: myLatlng,
        map: map
    }); 
  var border = layer.open({
	  type: 1,
	  title: false,
	  closeBtn: 0,
	  shadeClose: true,
	  content: $('#container')
	});
	layer.style(border, {
		width: '800px',
		height:'300px'
	});
  var label = new qq.maps.Label({
        position: myLatlng,
        map: map,
        content:shopName
    });
  var cssC = {
        background:'#3A9BFF',
        padding:"2px",
        color: "#fff",
        fontSize: "18px",
    };
  label.setStyle(cssC);
}
//加入购物车
function addCart(goodsId){
	$.post(WST.U('home/carts/addCart'),{goodsId:goodsId,buyNum:1},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1,time:600,shade:false});
	    	 if(json.data && json.data.forward){
	    	 	 location.href=WST.U('home/carts/'+json.data.forward);
	    	 }
	    	 getRightCart();
	     }else{
    		if(json.status==-2){
    			WST.loginWindow();
    			return;
    		}
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
function cancelFavorite(obj,type,id,fId){
	var param = {},str = '商品';
	param.id = fId;
	param.type = type;
	str = (type==1)?'店铺':'商品';
	$.post(WST.U('home/favorites/cancel'),param,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status=='1'){
	       WST.msg(json.msg,{icon:1});
	       $(obj).removeClass('j-fav').addClass('j-fav2');
	       $(obj).html('关注'+str)[0].onclick = function(){
	    	   addFavorite(obj,type,id,fId); 
	       };
	    }else{
			if(json.status==-2){
				WST.loginWindow();
				return;
			}
	       WST.msg(json.msg,{icon:5});
	    }
    });
}


function addFavorite(obj,type,id,fId){
	$.post(WST.U('home/favorites/add'),{type:type,id:id},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1});
	    	 $(obj).removeClass('j-fav2').addClass('j-fav');
	    	 $(obj).html('已关注')[0].onclick = function(){
	    		 cancelFavorite(obj,type,id,json.data.fId); 
	    	 };
	     }else{
    		if(json.status==-2){
    			WST.loginWindow();
    			return;
    		}
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}