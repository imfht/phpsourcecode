jQuery.noConflict();
var loading = false;
$(function(){
	$('.wst-se-search').on('submit', '.input-form', function(event){
	    event.preventDefault();
	})
    // 加载商品列表
    shopsList();
    // 楼层商品
    WST.imgAdapt('j-imgAdapt');
    // 商品分类
    var h = WST.pageHeight();
    var dataHeight = $("#frame").css('height');
    if(parseInt(dataHeight)>h-42){
        $('#content').css('overflow-y','scroll').css('height',h-42);
    }
    $(window).scroll(function(event){
        var wScrollY = window.scrollY; // 当前滚动条位置    
        var wInnerH = window.innerHeight; // 设备窗口的高度（不会变）    
        var bScrollH = document.body.scrollHeight; // 滚动条总高度     
        if ((wScrollY + wInnerH+10) >= bScrollH) {  
            var currPage = Number( $('#currPage').val() );
            var totalPage = Number( $('#totalPage').val() );
            if(currPage < totalPage ){
                if(!loading){
                    shopsList();
                }
            }
        }    
    });
});

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
    $('.wst-goodscate').removeClass('wst-goodscate_selected');
    $(obj).addClass('wst-goodscate_selected');
    $('.goodscate1').eq(index).show().siblings('.goodscate1').hide();
}
//排序条件
function orderCondition(obj,condition){
    var classContent = $(obj).attr('class');
    var status = $(obj).attr('status');
    var theSiblings = $(obj).siblings('.sorts');
    theSiblings.children('i').addClass('down').removeClass('down2').removeClass('up2');
    theSiblings.removeClass('active').attr('status','down');
    $(obj).addClass('active');
    if(classContent.indexOf('active')==-1){
        $(obj).children('i').addClass('down2').removeClass('down');
        theSiblings.children('i').addClass('down').removeClass('down2');
    }
    if(status.indexOf('down')>-1){
        if(classContent.indexOf('active')==-1){
            $(obj).children('i').addClass('down2').removeClass('up2');
            $('#mdesc').val('0');
        }else{
            $(obj).children('i').addClass('up2').removeClass('down2');
            $(obj).attr('status','up');
            $('#mdesc').val('1');
        }
    }else{
        $(obj).children('i').addClass('down2').removeClass('up2');
        $(obj).attr('status','down');
        $('#mdesc').val('0');
    }
    $('#msort').val(condition);//排序条件
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    shopsList();
}


//获取商品列表
function shopsList(){
    $('#Load').show();
    loading = true;
    var param = WST.getParams('.ipt');
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    $.post(WST.U('mobile/shops/getShopGoods'), param, function(data){
        var json = WST.toJson(data);
        var html = '';
        if(json && json.data && json.data.length>0){
            var gettpl = document.getElementById('shopList').innerHTML;
              laytpl(gettpl).render(json.data, function(html){
                $('#shops-list').append(html);
              }); 

            $('#currPage').val(json.current_page);
            $('#totalPage').val(json.last_page);
        }else{
            html += '<div style="width:100%;">';
            html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
  	        html += '<div class="wst-prompt-info">';
  	        html += '<p>对不起，没有相关商品。</p>';
  	        html += '</div>';
            html += '</div>';
            $('#shops-list').html(html);
        }
        WST.imgAdapt('j-imgAdapt');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}

/*分类*/
function getGoodsList(ct1,ct2){
    $('#ct2').val('');
    $('#ct1').val(ct1);
    if(ct2)$('#ct2').val(ct2);
    $('#currPage').val('0');
    $('#shops-list').html('');
    shopsList();
    $("#wst-shops-search").hide();
    dataHide();
}