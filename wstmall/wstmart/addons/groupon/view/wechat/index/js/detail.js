jQuery.noConflict();
//切换
function pageSwitch(obj,type){
	$(obj).addClass('active').siblings('.ui-tab-nav li.switch').removeClass('active');
	$('#goods'+type).show().siblings('section.ui-container').hide();
}
//商品评价列表
function evaluateList(){
    loading = true;
    var param = {};
    param.goodsId = $('#goodsId').val();
	param.pagesize = 10;
	param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('wechat/goodsappraises/getById'), param,function(data){
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
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
	time();
	//商品图片
    var slider = new fz.Scroll('.ui-slider', {
        role: 'slider',
        indicator: true,
        autoplay: true,
        interval: 3000
    });
	var w = WST.pageWidth();
    evaluateList();
	WST.imgAdapt('j-imgAdapt');
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - $(window).height())) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	evaluateList();
            }
        }
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
    var dataHeight = $("#frame").css('height');
    var cartHeight = parseInt($("#frame-cart").css('height'))+52+'px';
    $("#frame").css('bottom','-'+dataHeight);
    $("#frame-cart").css('bottom','-'+cartHeight);
});
//弹框
function dataShow(){
	jQuery('#cover').attr("onclick","javascript:dataHide();").show();
	jQuery('#frame').animate({"bottom": 0}, 500);
}
function dataHide(){
	var dataHeight = $("#frame").css('height');
	jQuery('#frame').animate({'bottom': '-'+dataHeight}, 500);
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
	var cartHeight = parseInt($("#frame-cart").css('height'))+52+'px';
	jQuery('#frame-cart').animate({'bottom': '-'+cartHeight}, 500);
	jQuery('#cover').hide();
}
//加入购物车
function addCart(goodsType){
	if(WST.conf.IS_LOGIN==0){
		WST.inLogin();
		return;
	}
	var buyNum = $("#buyNum").val()?$("#buyNum").val():1;
	$.post(WST.AU('groupon://carts/addCart'),{id:goodsInfo.grouponId,buyNum:buyNum,rnd:Math.random()},function(data,textStatus){
	     var json = WST.toJson(data);
	     if(json.status==1){
	    	 WST.msg(json.msg,'success');
	    	 cartHide();
    		 setTimeout(function(){
    			 location.href=WST.AU('groupon://carts/wxSettlement','goodsType='+goodsType);
    		 },1000);
	     }else{
	    	 WST.msg(json.msg,'info');
	     }
	});
}
function time(){
	var g = $('#groupon-time');
	var nowTime = new Date(Date.parse(g.attr('sc').replace(/-/g, "/")));
    var startTime = new Date(Date.parse(g.attr('sv').replace(/-/g, "/")));
    var endTime = new Date(Date.parse(g.attr('ev').replace(/-/g, "/")));
    var groupStatus = g.attr('st');
    if(groupStatus==-1){
    	$('#buyBtn').addClass('active').attr('disabled', 'disabled');
		$('#grouptime').html('团购活动已结束');
    }else{
	    if(startTime.getTime()> nowTime.getTime()){
	        var opts = {
	        	nowTime:nowTime,
				endTime: startTime,
				callback: function(data){
				    if(data.last>0){
				    	var html = [];
					    if(data.day>0)html.push(data.day+"天");
					    html.push(data.hour+"小时"+data.mini+"分"+data.sec+"秒");
					    $('#grouptime').html("团购活动还有"+html.join('')+"开始");
				    }else{
				    	var opts2 = {
		                    nowTime: data.nowTime,
							endTime: endTime,
							callback: function(data2){
							    if(data2.last>=0){
							    	var html = [];
								    if(data2.day>0)html.push(data2.day+"天");
								    html.push(data2.hour+"小时"+data2.mini+"分"+data2.sec+"秒");
								    $('#grouptime').html("团购活动剩余"+html.join(''));
								    $('#buyBtn').removeClass('active').removeAttr('disabled');
							    }else{
							    	$('#grouptime').html('团购活动已结束');
							    }
							    	
							}
						}
				    	WST.countDown(opts2);
				    }		
				}
			};
			WST.countDown(opts);
	    }else if(startTime.getTime()<= nowTime.getTime() && endTime.getTime() >=nowTime.getTime()){
	        var opts = {
	        	nowTime:nowTime,
				endTime: endTime,
				callback: function(data){
				    if(data.last>0){
				    	var html = [];
					    if(data.day>0)html.push(data.day+"天");
					    html.push(data.hour+"小时"+data.mini+"分"+data.sec+"秒");
					    $('#grouptime').html("团购活动剩余"+html.join(''));
					    $('#buyBtn').removeClass('active').removeAttr('disabled');
				    }else{
				    	$('#buyBtn').addClass('active').attr('disabled', 'disabled');
				    	$('#grouptime').html('团购活动已结束');
				    }			    	
				}
			};
			WST.countDown(opts);
	    }else{
	        $('#buyBtn').addClass('active').attr('disabled', 'disabled');
	        $('#grouptime').html('团购活动已结束');
	    }
	}
}