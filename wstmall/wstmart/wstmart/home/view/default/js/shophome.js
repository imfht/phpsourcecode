$(function() {
	WST.dropDownLayer(".wst-shop-code",".wst-shop-codes");
    var pageId = $('#shopHomePageId').val();
    if(pageId==undefined){
        $(".ck-slide-wrapper img").width(1200);
        $('.ck-slide').ckSlide({
            autoPlay: true,
            time:5000,
            isAnimate:true,
            dir: 'x'
        });
        $(".wst-shop-goimg").hover(function(){
            $(this).find(".js-cart").slideDown(100);
        },function(){
            $(this).find(".js-cart").slideUp(100);
        });
    }
});
function dropDown(obj,id){
    if( $(obj).attr('class').indexOf('js-shop-plus') > -1 ){
    	$(obj).removeClass('js-shop-plus').addClass('js-shop-redu');
    	$('.tree_'+id).slideUp();
    }else{
    	$(obj).removeClass('js-shop-redu').addClass('js-shop-plus');
    	$('.tree_'+id).slideDown();
    }
}
function searchShopsGoods(obj){
	var mdesc = $('#mdesc').val();
	if($('#msort').val() != obj)mdesc = 0;
	var msort = obj;
	var params = new Array();
	params.push("shopId=" + $("#shopId").val());
	params.push("msort=" + obj);
	params.push("mdesc=" + ((mdesc=="0")?"1":"0"));
	params.push("sprice=" + $("#sprice").val());
	params.push("eprice=" + $("#eprice").val());
	params.push("ct1=" + $("#ct1").val());
	params.push("ct2=" + $("#ct2").val());
	if($("#goodsName").val()!=undefined)params.push("goodsName=" + $("#goodsName").val());
	location.href = WST.U('home/shops/goods',params.join('&'),true);
}


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