function loadStat(){
	var load = WST.load({msg:'正在加载数据，请稍后...'})
	$.post(WST.U('home/reports/getTopSaleGoods'),WST.getParams('.j-ipt'),function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
		if(json.status=='1' && json.data){
			var gettpl = document.getElementById('top-sale-tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#list-box').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
		    });
		}else{
	    	WST.msg('没有查询到记录',{icon:5});
	    }
	});
}