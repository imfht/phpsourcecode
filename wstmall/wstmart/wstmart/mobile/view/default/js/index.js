//列表
function changeIndexTab(obj){
	var catId = $(obj).attr("data");
	$("#catId").val(catId);
	indexList(catId);
	$(".g_tab_item").removeClass("active");
	$(obj).addClass("active");
	$(".g_item_content").removeClass("g_tab_show").addClass("g_tab_hide");
	$("#g_item_content_"+catId).addClass("g_tab_show").removeClass("g_tab_hide");
}
function indexList(catId){
	$('#Load').show();
	loading = true;
	var param = {};
	param.page = Number( $('#currPage_'+catId).val() ) + 1;
	param.catId = catId;
    $.post(WST.U('mobile/index/pageQuery'), param,function(data){
        var json = WST.toJson(data);
        if(json){
	        $('#currPage_'+catId).val(json.current_page);
	        $('#lastPage_'+catId).val(json.last_page);
            var gettpl = document.getElementById('list').innerHTML;
            laytpl(gettpl).render(json, function(html){
                $('#goods-list-'+catId).append(html);
            });
            echo.init();

            if(json.current_page>=json.last_page){
            	$('.wst-load-text').html('加载完啦');
            }else{
            	$('.wst-load-text').html('加载中');
            }
	        //WST.imgAdapt('j-imgAdapt');
        }
	    loading = false;
	    $('#Load').hide();
    });
}
//商品列表页
function getGoodsList(goodsCatId){
	location.href = WST.U('mobile/goods/lists','cat='+goodsCatId);
}
var currPage = 0;
var lastPage = 0;
var loading = false;
$(document).ready(function(){
	var pageId = $('#pageId').val();
	if(pageId == 0){
		WST.initFooter('home');
		//搜索
		$(window).scroll(function(){
			if( $(window).scrollTop() > 42 ){
				$('#j-header').addClass('active');
				$('#j-searchs').addClass('active');
			}else{
				$('#j-header').removeClass('active');
				$('#j-searchs').removeClass('active');
			}
			// 当页面滚动到商品tab栏，商品tab栏悬浮在顶部
			var searchHeight = $('.wst-in-search').height();
			var goodsTabHeight = $("#goodsTab").height();
			var goodsTabScrollTop = parseInt($("#goodsTab").offset().top) - parseInt($(window).scrollTop());
			var goodsContentScrollTop = parseInt($("#goodsContent").offset().top) - parseInt($(window).scrollTop());
			if(goodsTabScrollTop<=searchHeight){
				$("#goodsTab").addClass('goods-tab-fixed');
				$("#goodsContent").css("padding-top",goodsTabHeight+10);
				goodsContentScrollTop = parseInt($("#goodsContent").offset().top) - parseInt($(window).scrollTop());
				if(goodsContentScrollTop>=searchHeight){
					$("#goodsTab").removeClass('goods-tab-fixed');
					$("#goodsContent").css("padding-top",0);
				}
			}
		});
		$('.wst-se-search').on('submit', '.input-form', function(event){
			event.preventDefault();
		})
		$('.wst-in-search').on('submit', '.input-form', function(event){
			event.preventDefault();
		})
		//广告
		new Swiper('.banner', {
			autoplay: true,
			autoHeight: true, //高度随内容变化
			on: {
				resize: function(){
					this.update();
				},
			} ,
			pagination: {
				el: '.swiper-pagination',
				type: 'bullets',
			},
		});
		//文章
		if($('.wst-in-news a').hasClass("words")){
			new Swiper('.swiper-container1', {
				autoplay: true,
				autoHeight: true, //高度随内容变化
				width: window.innerWidth,
				on: {
					resize: function(){
						this.params.width = window.innerWidth;
						this.update();
					}
				}
			});
		}

		var w = WST.pageWidth();
		//咨询上广告
		if($('.wst-in-activity a').hasClass("advert4")){
		}else{
			$('.wst-in-activity .advert4').hide();
		}
		//中间大广告
		if($('.wst-in-adst a').hasClass("advert2")){
		}else{
			$('.wst-in-adst ').hide();
		}

		//中间小广告
		if($('.wst-in-adsb a').hasClass("advert3")){
			new Swiper('.swiper-container2', {
				slidesPerView: 3,
				freeMode : true,
				spaceBetween: 0,
				autoplay : 2000,
				speed:1200,
				loop : true,
				autoHeight: true, //高度随内容变化
				on: {
					resize: function(){
						this.params.width = window.innerWidth;
						this.update();
					},
					slideChange(){
						echo.init();
					}
				}
			});

		}else{
			$('.wst-in-adsb').hide();
		}


		//刷新
		var catId = $("#catId").val();
		indexList(catId);
		$(window).scroll(function(){
			if (loading) return;
			var catId = $("#catId").val();
			if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
				currPage = Number( $('#currPage_'+catId).val() );
				lastPage = Number( $('#lastPage_'+catId).val() );
				if(currPage < lastPage ){
					$('.wst-load-text').html('加载中');
					indexList(catId);
				}else{
					$('.wst-load-text').html('加载完啦');
				}
			}
		});
	}else{
		WST.selectCustomMenuPage('index');
		new Swiper('.banner', {
			autoplay: true,
			autoHeight: true, //高度随内容变化
			width: window.innerWidth,
			on: {
				resize: function(){
					this.params.width = window.innerWidth;
					this.update();
				},
			} ,
			pagination: {
				el: '.swiper-pagination',
				type: 'bullets',
			},
		});
	}
});

