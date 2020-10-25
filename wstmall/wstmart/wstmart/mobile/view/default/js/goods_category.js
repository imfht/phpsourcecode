jQuery.noConflict();
function showRight(obj, index){
    $('.wst-goodscate').removeClass('wst-goodscate_selected');
    $(obj).addClass('wst-goodscate_selected');
	jQuery('.goodscate1').eq(index).fadeIn(200).siblings('.goodscate1').hide();
    inSwiper(index+1);
}
//商品列表页
function getGoodsList(goodsCatId){
	location.href = WST.U('mobile/goods/lists','cat='+goodsCatId,true);
}

//品牌-商品列表页
function getBrandGoodsList(brandId){
	location.href = WST.U('mobile/goods/lists','brandId='+brandId,true);
}
//适应高度
function selfAdapt(h){
	var o = document.getElementById('ui-scrollerl');
	var a = h-86;
	o.style.height=a+'px';
}
//轮播图
function inSwiper(n){
    if($('.category-ads'+n+' a').hasClass("img")){
        new Swiper('.category-ads'+n, {
            autoplay: true,
            autoHeight: true, //高度随内容变化
            on: {
                resize: function(){
                    this.params.width = window.innerWidth;
                    this.update();
                },
            } ,
            pagination: {
                el: '.pagination-ads'+n,
                type: 'bullets',
            },
        });
    }else{
        $('.category-ads'+n).hide();
    }
}
var height = WST.pageHeight();
$(document).ready(function(){
    if(parseInt($('#pageId').val()) == 0){
        WST.initFooter('category');
    }else{
        WST.selectCustomMenuPage('category');
    }
	$('.wst-se-search').on('submit', '.input-form', function(event){
	    event.preventDefault();
	})
	selfAdapt(height);
    inSwiper(1);
    $(window).scroll(function(event){
    	var h = WST.pageHeight();
    	selfAdapt(h);
    });
    var scroll = new fz.Scroll('.ui-scrollerl', {
        scrollY: true,
        slidingY: 'y'
    });
    var w = WST.pageWidth();
    var wImg=(w*0.76-30)/3;
    var hImg = wImg*9/14;
    $('.wst-gc-br img').css('height',hImg);
    var w = $('.goods-cat-img').width();
    $('.goods-cat-img').css('height',w);
});


