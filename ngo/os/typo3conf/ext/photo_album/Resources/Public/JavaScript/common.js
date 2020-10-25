(function($){
    $(function(){
	
		if ( $("#timeline_bodytext").length > 0 ) {
			UE.getEditor("timeline_bodytext");
		}		
		
		$('.btn-del').click(function(){
			if(confirm('您确认要删除该条记录吗？')) return true;
			return false;
		});
		
		//全选&取消全选
		$('input[type="checkbox"].selectall').bind('click', function() { 
			$(this).closest('table').find('.sel').prop("checked", this.checked);
		}); 
		
		//删除选中
		$('.btn-delete-all').bind('click',function(){
			var items='';
			$('input[type="checkbox"].sel').each(function(){
				if($(this).prop("checked")){
					items += $(this).val()+',';
				}
			});
			
			if(items == ''){
				alert('请您选择需要删除的记录！');
			}else{
				$('#multidelete-items').val(items);
				//alert($('#multidelete-items').val()   );
				return true;
			}
			return false;
		});
		
		
		$("#photoalbum").validate({
			errorElement: "span", 
			rules: {
				'tx_photoalbum_album[title]': {
					required: true
				}
			},
			messages: {
				'tx_photoalbum_album[title]': {
					required: "请输入相册标题"
				}
			}   
        });

        $(".pimg").click(function(){
	        var _this = $(this);//将当前的pimg元素作为_this传入函数 
	        imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);  
	    });

	    //验证上传文件类型并即时显示
		$("#exampleInputFile").change(function () {
			var message = $("#see_image");
	        var filepath = $("#exampleInputFile").val();
	        var extStart = filepath.lastIndexOf(".");
	        var ext = filepath.substring(extStart, filepath.length).toUpperCase();
	        if (ext == ".PNG" || ext == ".JPG" || ext == ".JPEG") {
	        	message.empty();
	        	message.html("<img src='"+getObjectURL($(this)[0].files[0])+"' id='preview' class='img-responsive' style='max-height:200px' />");
	        } else {
	          	message.empty();
	          	message.css("color","red");
	          	message.html("仅支持<br/>JPG、PNG、JPEG格式的图片");
	          	return false;
	        }
	        return true;
	    });
    });
})(jQuery);


function isMobile() {
    return /(iPhone|iPad|iPod|iOS|android|MicroMessenger)/i.test(navigator.userAgent);
}

//获得上传图片URL地址
function getObjectURL(file) {
    var url = null ;
    if (window.createObjectURL!=undefined) { // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }
    return url ;
}

function imgShow(outerdiv, innerdiv, bigimg, _this){
    var src = _this.attr("id");//获取当前点击的pimg元素中的id属性  
    $(bigimg).attr("src", src);//设置#bigimg元素的src属性  
  
        /*获取当前点击图片的真实大小，并显示弹出层及大图*/  
    $("<img/>").attr("src", src).load(function(){  
        var windowW = $(window).width();//获取当前窗口宽度  
        var windowH = $(window).height();//获取当前窗口高度  
        var realWidth = this.width;//获取图片真实宽度  
        var realHeight = this.height;//获取图片真实高度  
        var imgWidth, imgHeight;  
        var scale = 0.8;//缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放  
          
        if(realHeight>windowH*scale) {//判断图片高度  
            imgHeight = windowH*scale;//如大于窗口高度，图片高度进行缩放  
            imgWidth = imgHeight/realHeight*realWidth;//等比例缩放宽度  
            if(imgWidth>windowW*scale) {//如宽度扔大于窗口宽度  
                imgWidth = windowW*scale;//再对宽度进行缩放  
            }  
        } else if(realWidth>windowW*scale) {//如图片高度合适，判断图片宽度  
            imgWidth = windowW*scale;//如大于窗口宽度，图片宽度进行缩放  
                        imgHeight = imgWidth/realWidth*realHeight;//等比例缩放高度  
        } else {//如果图片真实高度和宽度都符合要求，高宽不变  
            imgWidth = realWidth;  
            imgHeight = realHeight;  
        }  
                $(bigimg).css("width",imgWidth);//以最终的宽度对图片缩放  
          
        var w = (windowW-imgWidth)/2+100;//计算图片与窗口左边距  
        var h = (windowH-imgHeight)/2;//计算图片与窗口上边距  
        $(innerdiv).css({"top":h, "left":w});//设置#innerdiv的top和left属性  
        $(outerdiv).fadeIn("fast");//淡入显示#outerdiv及.pimg  
    });  
      
    $(outerdiv).click(function(){//再次点击淡出消失弹出层  
        $(this).fadeOut("fast");  
    });  
}