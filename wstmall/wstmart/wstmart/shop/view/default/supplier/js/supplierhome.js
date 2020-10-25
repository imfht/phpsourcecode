$(function() {
	WST.dropDownLayer(".wst-supplier-code",".wst-supplier-codes");
    var pageId = $('#supplierHomePageId').val();
    if(pageId==undefined){
        $(".ck-slide-wrapper img").width(1200);
        $('.ck-slide').ckSlide({
            autoPlay: true,
            time:5000,
            isAnimate:true,
            dir: 'x'
        });
        $(".wst-supplier-goimg").hover(function(){
            $(this).find(".js-cart").slideDown(100);
        },function(){
            $(this).find(".js-cart").slideUp(100);
        });
    }
});
function dropDown(obj,id){
    if( $(obj).attr('class').indexOf('js-supplier-plus') > -1 ){
    	$(obj).removeClass('js-supplier-plus').addClass('js-supplier-redu');
    	$('.tree_'+id).slideUp();
    }else{
    	$(obj).removeClass('js-supplier-redu').addClass('js-supplier-plus');
    	$('.tree_'+id).slideDown();
    }
}

function mallGoodsSearch() {
	var goodsName = $("#goodsName").val();
	location.href = WST.U('shop/supplierindex/index','keyword='+goodsName,true);
}



function searchSuppliersGoods(obj){
	var mdesc = $('#mdesc').val();
	if($('#msort').val() != obj)mdesc = 0;
	var msort = obj;
	var params = new Array();
	params.push("supplierId=" + $("#supplierId").val());
	params.push("msort=" + obj);
	params.push("mdesc=" + ((mdesc=="0")?"1":"0"));
	params.push("sprice=" + $("#sprice").val());
	params.push("eprice=" + $("#eprice").val());
	params.push("ct1=" + $("#ct1").val());
	params.push("ct2=" + $("#ct2").val());
	if($("#goodsName").val()!=undefined)params.push("goodsName=" + $("#goodsName").val());
	location.href = WST.U('shop/suppliers/index',params.join('&'),true);
}


function init() {
  var longitude = $('#longitude').val();
  var latitude = $('#latitude').val();
  var supplierName = $('#supplierName').val();
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
        content:supplierName
    });
  var cssC = {
        background:'#3A9BFF',
        padding:"2px",
        color: "#fff",
        fontSize: "18px",
    };
  label.setStyle(cssC);
}
//加入进货单
function addCart(goodsId){
	$.post(WST.U('shop/suppliercarts/addCart'),{goodsId:goodsId,buyNum:1},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,{icon:1,time:600,shade:false});
	    	 if(json.data && json.data.forward){
	    	 	 location.href=WST.U('shop/suppliercarts/'+json.data.forward);
	    	 }
	     }else{
    		if(json.status==-2){
    			WST.loginWindow();
    			return;
    		}
	    	 WST.msg(json.msg,{icon:2});
	     }
	});
}
