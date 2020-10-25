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

function toApprDetail(oId,ogId){
	var param = {};
	param.oId = oId;
	param.orderGoodsId = ogId;
	location.href=WST.U('mobile/orders/orderappraisedetail',param);
}
//商品评价
function clickStar(obj){
    var index = $(obj).index(); // 当前选中的分数
    $(obj).parent().find('span').each(function(k,v){
        if(k<=index){
            $(this).removeClass('start-not').addClass('start-on');
        }else{
            $(this).removeClass('start-on').addClass('start-not');
        }
    })
    $(obj).parent().siblings().html(index+1+'分');
    $(obj).parent().siblings().attr('score',index+1);
}

function appraise(gId,sId,ogId,obj){

	$('.appraise').removeClass('score');
	$(obj).addClass('score');
	var param = {};
	param.gId = gId;
	param.sId = sId;
	param.oId = $('#oId').val();
	param.orderGoodsId = ogId;
	$.post(WST.U('mobile/goodsappraises/getAppr'),param,function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			var gettpl = document.getElementById('appraises-box').innerHTML;
			json.data.goodsId = gId;
			json.data.goodsSpecId = sId;
			json.data.orderGoodsId = ogId;
	          laytpl(gettpl).render(json.data, function(html){
	          	$('div[id^="appBox_"]').html(' ');
	            $('#appBox_'+ogId).html(html);
	          });
	        if(json.data.serviceScore=='')userAppraiseInit();
		}else{
			WST.msg('请求出错','info');
		}
	})
}
function saveAppr(gId,sId,ogId){
	var content = $.trim($('#content').val());
	if(content==''){
		WST.msg('评价内容不能为空','info');
		return
	}
	var param = {};
	param.content = content;
	param.goodsId = gId;
	param.goodsSpecId = sId;
	param.orderId = $('#oId').val();
	param.timeScore = $('#timeScore').attr('score');
	param.goodsScore = $('#goodsScore').attr('score');
	param.serviceScore = $('#serviceScore').attr('score');
	param.orderGoodsId = ogId;

	var imgs = [];
	//  是否有上传附件
	$('.imgSrc').each(function(k,v){
		imgs.push($(this).attr('v'));
	})
	imgs = imgs.join(',');
	if(imgs!='')
	param.images = imgs;

	$.post(WST.U('mobile/goodsappraises/add'),param,function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg(json.msg,'success');
			setTimeout(function(){history.go(-1);},1000);
		}else{
			WST.msg(json.msg);
		}
	})

}
$(function(){
	WST.imgAdapt('j-imgAdapt');
})


/*************** 上传图片 *****************/
function userAppraiseInit(){
   var uploader =WST.upload({
        pick:'#filePicker',
        formData: {dir:'appraises',isThumb:1},
        fileNumLimit:5,
        accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
        callback:function(f,file){
          var json = WST.toJson(f);
          if(json.status==1){
		  var tdiv = $("<div style='position: relative'>"+
			  "<img class='imgSrc' src='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb+"' v='"+json.savePath+json.name+"'></div>");
		  var btn = $('<div class="del-btn"><span class="upload-icon-delete"></span></div>');
		  tdiv.append(btn);
		  $('#filePicker').before(tdiv);
          btn.on('click','span',function(){
            uploader.removeFile(file);
            $(this).parent().parent().remove();
            uploader.refresh();
          });
          }else{
            WST.msg(json.msg,{icon:2});
          }
      },
      progress:function(rate){
          $('#uploadMsg').show().html('已上传'+rate+"%");
      }
    });
}
