jQuery.noConflict();
//获取店铺列表
function goodsList(){
	$('#Load').show();
    loading = true;
    var param = {};
    param.catId = $('#goodsCatId').val();
    param.goodsName = $('#keyword').val();
	param.pagesize = 10;
	param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.AU('groupon://goods/wxGrouplists'), param,function(data){
        var json = WST.toJson(data);
        $('#currPage').val(json.current_page);
        $('#totalPage').val(json.last_page);
        var gettpl = document.getElementById('list').innerHTML;
        laytpl(gettpl).render(json, function(html){
            $('#goods-list').append(html);
        });
        WST.imgAdapt('j-imgAdapt');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
        time(json.current_page);
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
	WST.initFooter('home');
	goodsList();
    var dataHeight = $("#frame").css('height');
    $('.goodscate1').css('overflow-y','scroll').css('height',WST.pageHeight()-50);
    $("#frame").css('top',0);
     var dataWidth = $("#frame").css('width');
    $("#frame").css('right','-'+dataWidth);

    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - $(window).height())) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
            	goodsList();
            }
        }
    });
});
function goGoods(id){
    location.href=WST.AU('groupon://goods/wxdetail','id='+id);
}
function searchGoods(){
	var data = $('#wst-search').val();
	location.href = WST.AU('groupon://goods/wxlists','keyword='+data);
}
function time(n){
	var nowTime = new Date(Date.parse($('#groupon-container').attr('sc').replace(/-/g, "/")));
	$('.goods_'+n).each(function(){
        var g = $(this);
        var startTime = new Date(Date.parse(g.attr('sv').replace(/-/g, "/")));
        var endTime = new Date(Date.parse(g.attr('ev').replace(/-/g, "/")));
        var gruopStatus = g.attr('st');
        if(gruopStatus==-1){
            g.addClass('wst-shl-list2');
			g.find('.countDown_'+n).html('团购活动已结束');
        }else{
	        if(startTime.getTime()> nowTime){
	            var opts = {
		            nowTime: nowTime,
				    endTime: startTime,
				    callback: function(data){
				    	if(data.last>0){
				    		var html = [];
					    	if(data.day>0)html.push(data.day+"天");
					    	html.push(data.hour+"小时"+data.mini+"分"+data.sec+"秒");
					        g.find('.countDown_'+n).html("还有"+html.join('')+"开始");
				    	}else{
				    		var opts2 = {
					            nowTime: data.nowTime,
							    endTime: endTime,
							    callback: function(data2){
							    	if(data2.last>0){
							    		var html = [];
								    	if(data2.day>0)html.push(data2.day+"天");
								    	html.push(data2.hour+"小时"+data2.mini+"分"+data2.sec+"秒");
								        g.find('.countDown_'+n).html("剩余"+html.join(''));
							    	}else{
							    		g.addClass('wst-shl-list2');
							    		g.find('.countDown_'+n).html('团购活动已结束');
							    	}
							    	
							    }
							}
				    	    WST.countDown(opts2);
				    	}
				    		
				    }
				};
				WST.countDown(opts);
	        }else if(startTime.getTime()<= nowTime && endTime.getTime() >=nowTime){
	            var opts = {
		            nowTime: nowTime,
				    endTime: endTime,
				    callback: function(data){
				    	if(data.last>0){
				    		var html = [];
					    	if(data.day>0)html.push(data.day+"天");
					    	html.push(data.hour+"小时"+data.mini+"分"+data.sec+"秒");
					        g.find('.countDown_'+n).html("剩余"+html.join(''));
				    	}else{
				    		g.addClass('wst-shl-list2');
				    		g.find('.countDown_'+n).html('团购活动已结束');
				    	}
				    	
				    }
				};
				WST.countDown(opts);
	        }else{
	        	g.addClass('wst-shl-list2');
	        	g.find('.countDown_'+n).html('团购活动已结束');
	        }
		}
	})
}
//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
}
function dataHide(){
    var dataHeight = $("#frame").css('height');
    var dataWidth = $("#frame").css('width');
    jQuery('#frame').animate({'right': '-'+dataWidth}, 500);
    jQuery('#cover').hide();
}

function showRight(obj, index){
    $(obj).addClass('wst-goodscate_selected').siblings('#goodscate').removeClass('wst-goodscate_selected');
    $('.goodscate1').eq(index).show().siblings('.goodscate1').hide();
}
/*分类*/
function goodsCat(goodsCatId){
    $('#goodsCatId').val(goodsCatId);
    $('#currPage').val('');
    $('#goods-list').html('');
    goodsList();
    dataHide();
}
