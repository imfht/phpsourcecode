$(function(){
	$(window).scroll(function(){
		var h = $(window).scrollTop();
			if(h>=70){
				$('.bootsnav').addClass('scroll');
			}else{
				$('.bootsnav').removeClass('scroll');
			};
	})
});

$(function(){
	//顶部导航处理
	var coverH = $('#section1').scrollTop()+$('#section1').height()-$('.bootsnav').height();

	$(document).scroll(function() {
		var nowTop = parseInt($(window).scrollTop());
		if(nowTop>coverH){
			$('.bootsnav').addClass('white_nav');
		}else{
			$('.bootsnav').removeClass('white_nav');
		}
	  	//console.log(nowTop);
	});
});

$(function(){
	reWidth();
	//顶部下载、演示按钮响应式处理
	$(window).resize(function(){
		reWidth();
	});

	function reWidth(){
		var w = $(window).width();
		if(w<=768){
			$('.downBtn a').addClass('btn-block');
		}else{
			$('.downBtn a').removeClass('btn-block');
		};
	}

});

$(function(){
	//section5鼠标经过事件
	$('#section5 .icon').each(function(){
		
		var _this = $(this);
		_this.mouseover(function(){
			_this.find('.shade').removeClass('hidden');
			_this.find('img').addClass('imghover');
		});
		_this.mouseout(function(){
			_this.find('.shade').addClass('hidden');
			_this.find('img').removeClass('imghover');
		});
	});
});