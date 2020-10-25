$(function(){
	$('.goodsImg2').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:WST.conf.RESOURCE_PATH+'/'+WST.conf.GOODS_LOGO});//商品默认图片
	var nowTime = new Date(Date.parse($('#groupon-container').attr('sc').replace(/-/g, "/")));
	$('.goods').each(function(){
        var g = $(this);
        var startTime = new Date(Date.parse(g.attr('sv').replace(/-/g, "/")));
        var endTime = new Date(Date.parse(g.attr('ev').replace(/-/g, "/")));
        var groupStatus = g.attr('st');
        if(groupStatus==-1){
        	g.find('.goods-txt').addClass('goods-txt2').removeClass('goods-txt');
			g.find('.countDown').html('团过活动已结束');
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
					        g.find('.countDown').html("还有"+html.join('')+"开始");
				    	}else{
				    		var opts2 = {
					            nowTime: data.nowTime,
							    endTime: endTime,
							    callback: function(data2){
							    	if(data2.last>0){
							    		var html = [];
								    	if(data2.day>0)html.push(data2.day+"天");
								    	html.push(data2.hour+"小时"+data2.mini+"分"+data2.sec+"秒");
								        g.find('.countDown').html("剩余"+html.join(''));
							    	}else{
							    		g.find('.goods-txt').addClass('goods-txt2').removeClass('goods-txt');
							    		g.find('.countDown').html('团过活动已结束');
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
					        g.find('.countDown').html("剩余"+html.join(''));
				    	}else{
				    		g.find('.p-add-cart').remove();
				    		g.find('.goods-txt').addClass('goods-txt2').removeClass('goods-txt');
				    		g.find('.countDown').html('团购活动已结束');
				    	}
				    	
				    }
				};
				WST.countDown(opts);
	        }else{
	        	g.find('.p-add-cart').remove();
	        	g.find('.goods-txt').addClass('goods-txt2').removeClass('goods-txt');
	        	g.find('.countDown').html('团购活动已结束');
	        }
		}
	})
});
$('.goods').hover(function(){
	$(this).find('.sale-num').hide();
	$(this).find('.p-add-cart').show();
},function(){
	$(this).find('.sale-num').show();
	$(this).find('.p-add-cart').hide();
})
