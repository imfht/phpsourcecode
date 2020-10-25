$(document).ready(function(){
	$('#pub-imgadd .pr').hover(function(){
		$('i',this).fadeIn();
		$('span',this).fadeIn();
	},function(){
		$('i',this).fadeOut();
		$('span',this).fadeOut();
	});
	$('#searchform .input-group-addon').click(function(){
		$('#searchform').submit();
	});
	$('#cart .list-group-item').hover(function(){
		$('.delete-cart',this).animate({right:"0px"});
	},function(){
		$('.delete-cart',this).animate({right:"-120px"});
	});
	$('#pro-index-trin .dropdown-menu a').click(function(){
		var num = $(this).text()*1;
		$('#buy-num').text(num);
		$('#buy-num-input').val(num);
	});
	$('#home-main-2 .thumbnail').hover(function(){
		$('.caption',this).fadeIn();
	},function(){
		$('.caption',this).fadeOut();
	});
	$("input.password").focus(function(){
		$(this).attr('type','password');
	});
	$("input.password").blur(function(){
		var val = $(this).val();
		if(val=='') {
			$(this).attr('type','text');
		}
	});
	$('.btn-huifu').click(function(){
		var huifu = $(this).attr('huifu-data');
		$('#comment-textarea').text(huifu).focus();
		var gotop = $($(this).attr("href")).offset().top-45;
        $("html, body").animate({
            scrollTop: gotop + "px"
        }, {
            duration: 500,
            easing: "swing"
        });
        return false;
	});
});

function search_type(type,text) {
	$('#search-type').val(type);
	$('#search-type-text').text(text);
	return false;
};

//列表图片剧中
function imgresize() {
	// IMAGE RESIZE
    $('.img-div').each(function() {
        var width = $(this).width();
        var height = $(this).height();
        var img_width = $('img',this).width();
        var img_height = $('img',this).height();
     
        if(height > img_height){
            ratio = height / img_height;
            ratio1 = width * ratio;
            $('img',this).css("height", height);
            $('img',this).css("width", ratio1);
            ratio2 = width - ratio1;
            ratio3 = ratio2 / 2;
            $('img',this).css("margin-left", ratio3);
        } else {
	        martop = (img_height - height)/2;
	        $('img',this).css("margin-top", -martop);
        }
    });
};
$(window).bind("load", function() {
    imgresize();
});

//内容页导航
$(function(){
	$.fn.scrollToTop2=function(){
		var scrollDiv2=$(this);
		var height = $(window).height()-50;
		$(window).scroll(function(){
			if($(window).scrollTop()<height){
				$(scrollDiv2).removeClass("fixed-top")
			}else{
				$(scrollDiv2).addClass("fixed-top")
			}
		});
	}
});
$(function() {
	$("#topnav").scrollToTop2();
});

//平滑滚动到锚点
$("a.goto").click(function() {
        var gotop = $($(this).attr("href")).offset().top-76;
        $("html, body").animate({
            scrollTop: gotop + "px"
        }, {
            duration: 500,
            easing: "swing"
        });
        return false;
    });