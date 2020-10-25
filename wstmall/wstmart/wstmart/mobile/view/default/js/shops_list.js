jQuery.noConflict();
var latitude = '';
var longitude = '';
var locationObj = {};
function getLocation(){
    if(WST.conf.MAP_KEY!=''){
        if(WST.getCookie('locationObj')!=undefined){
            var obj = JSON.parse(WST.getCookie('locationObj'));
            latitude = obj.latitude;
            longitude = obj.longitude;
            shopsList();
        }else{
            shopsList();
            WST.location(function(position){
                latitude = position.latitude;
                longitude = position.longitude;
                locationObj.latitude = latitude;
                locationObj.longitude = longitude;
                WST.setCookie("locationObj",JSON.stringify(locationObj),1800);
                if(latitude && longitude) {
                    $(".wst-shop-dialog").removeClass('shop-dialog-hide').addClass('shop-dialog-show');
                }
            });
        }
    }else{
        shopsList();
    }
}
function recommendShop(){
    $(".wst-shop-dialog").removeClass('shop-dialog-show').addClass('shop-dialog-hide');
    $('#shops-list').html('');
    $('#currPage').val(0);
    shopsList();
}
//  隐藏对话框
function hideDialog(){
    $(".wst-shop-dialog").removeClass('shop-dialog-show').addClass('shop-dialog-hide');
}
//排序条件
function orderCondition(obj,condition){
     var classContent = $(obj).attr('class');
    var status = $(obj).attr('status');
    var theSiblings = $(obj).parent().siblings('.evaluate').children();
    theSiblings.removeClass('active').attr('status','down');
    $(obj).addClass('active');
    $('.wst-shl-select').removeClass('active');
    if(classContent.indexOf('active')==-1){
        $(obj).children('i').addClass('down2').removeClass('down');
        theSiblings.children('i').addClass('down').removeClass('down2');
    }
    if(status.indexOf('down')>-1){
        if(classContent.indexOf('active')==-1){
            $(obj).children('i').addClass('down2').removeClass('up2');
            $('#desc').val('0');
        }else{
            $(obj).children('i').addClass('up2').removeClass('down2');
            $(obj).attr('status','up');
            $('#desc').val('1');
        }
    }else{
        $(obj).children('i').addClass('down2').removeClass('up2');
        $(obj).attr('status','down');
        $('#desc').val('0');
    }
    $('#condition').val(condition);//排序条件
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    $('#screenAttr').html('');
    $('#graded').html('');
    getLocation(1);
}
function orderSelect(id){
    $('.wst-shl-select').addClass('active');
    $('.evaluate .choice').removeClass('active');
    $('.wst-shl-head .evaluate i').addClass('down').removeClass('down2');
    $('#catId').val(id);
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    $('#screenAttr').html('');
    $('#graded').html('');
    $('#accredId').val('');
    $('#totalScore').val('');
    getLocation(1);
}
function searchCondition(id){
    $("#wst-shops-search").hide();
    $('#catId').val(id);
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    $('#screenAttr').html('');
    $('#graded').html('');
    $('#accredId').val('');
    $('#totalScore').val('');
    getLocation();
}
//获取店铺列表
function shopsList(from){
    $('#Load').show();
    loading = true;
    var param = WST.getParams('.ipt');
    param.pagesize = 10;
    param.page = Number( $('#currPage').val() ) + 1;
    param.latitude = latitude;
    param.longitude = longitude;
    $.post(WST.U('mobile/shops/pageQuery'), param,function(data){
        var json = WST.toJson(data);
        $('#currPage').val(json.current_page);
        $('#totalPage').val(json.last_page);
        $('#minScore').val(json.minScore);
        var gettpl = document.getElementById('list').innerHTML;
        laytpl(gettpl).render(json.data, function(html){
            $('#shops-list').append(html);
        });
        if(from != 1 && from != 2 && from != 3 ){
            if(json.screen){
                var gettp2 = document.getElementById('accredList').innerHTML;
                laytpl(gettp2).render(json.screen.accreds, function(html){
                    $('#screenAttr').append(html);
                }); 
            }
        }
        if(from != 1 && from != 2 && $('#totalScore').val() == ''&& from != 5){
            if(json.screen){
                var gettp2 = document.getElementById('scoreList').innerHTML;
                laytpl(gettp2).render(json.screen.scores, function(html){
                    $('#graded').append(html);
                });
            }
        }
        imgShop('j-imgAdapt');
        imgShop('goods-item');
        loading = false;
        $('#Load').hide();
        echo.init();//图片懒加载
    });
}
var currPage = totalPage = 0;
var loading = false;
$(document).ready(function(){
    WST.initFooter('home');
    $('.wst-se-search').on('submit', '.input-form', function(event){
        event.preventDefault();
    })
    
    if($('.wst-shl-ads a').hasClass("adsImg")){
        //中间小广告
        new Swiper('.swiper-container', {
            slidesPerView: 4,
            freeMode : true,
            spaceBetween: 0,
            autoplay : true,
            speed:1200,
            loop : false,
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
        $('.wst-shl-ads').hide();
    }
    getLocation();
    $(window).scroll(function(){  
        if (loading) return;
        if ((5 + $(window).scrollTop()) >= ($(document).height() - screen.height)) {
            currPage = Number( $('#currPage').val() );
            totalPage = Number( $('#totalPage').val() );
            if( totalPage > 0 && currPage < totalPage ){
             shopsList(2);
            }
        }
    });
});
function goShopHome(sid){
    location.href=WST.U('mobile/shops/index','shopId='+sid,true);
}
//适应图片大小正方形
function imgShop(name){
    var w = $('.'+name).width();
     if(name == 'j-imgAdapt'){
       $('.'+name).css({"width": w+"px","height": w+"px"});
    }else{
       $('.'+name).css({"width": w+"px","height": w+20+"px"});
    }
    $('.'+name+' a').css({"width": w+"px","height": w+"px"});
    $('.'+name+' a img').css({"width": w+"px","height": w+"px"});
    $('.'+name+' a .goodsPrice').css({"width": w+"px"});
}
/*打开筛选层*/
function screenTier(){
    $('.screen').addClass('screen1').removeClass('screen').next().css('color','#ec7070');
    $('#backgroundTier').show();
    jQuery("#screen").animate({height:WST.pageHeight(),right:"0"},500);
    $('.screen-top').css('height',WST.pageHeight()-44);
    $(".ui-container").css({height:WST.pageHeight()-88,overflow:"hidden"})
}
/*关闭筛选层*/
function closeScreenTier(){
    jQuery("#screen").animate({right:"-91%"},500);
    setTimeout(function(){
        $('#backgroundTier').hide();
        $(".ui-container").css({height:"auto",overflow:"visible"})
    },570);
}
/*展开属性*/
function showAll(obj){
    if($(obj).attr('s') == 0){
        $(obj).attr('s',1);
        $(obj).addClass('arrowed').removeClass('arrow');
        $(obj).parent().next('.option-box').addClass('expand');
    }else{
        $(obj).attr('s',0);
        $(obj).addClass('arrow').removeClass('arrowed');
        $(obj).parent().next('.option-box').removeClass('expand');
    }
}
function selectAccred(obj){
    $('#cancelAccred').html($(obj).html()).attr('d',$(obj).attr('d')).show();
    $('.accred-lines').hide();
    $('#accredId').val($(obj).attr('d'));
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    if($('#totalScore').val() == ''){
       $('#graded').html('');   
    }
    shopsList(3);
}
function selectScore(obj){
    $('#cancelScore').html($(obj).html()).attr('d',$(obj).attr('d')).show();
    $('.wrap-lines').hide();
    $('#totalScore').val($(obj).attr('d'));
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    shopsList(1);
}
/*重置所有*/
function resetAll(){
    $('.attrs').removeClass('selected').css('background-color','#f0f2f5').siblings().show();
    $('#accredId').val('');
    $('#totalScore').val('');
    $('#currPage').val('0');//当前页归零
    $('#shops-list').html('');
    $('#screenAttr').html('');
    $('#graded').html('');
    shopsList(4);
}

function cancelAccred(obj){
   $('.accred-box').remove();
   $('#accredId').val('');
   $('#currPage').val('0');//当前页归零
   $('#shops-list').html('');
    shopsList(5);
}

function cancelScore(obj){
   $('.score-box').remove();
   $('#totalScore').val('');
   $('#currPage').val('0');//当前页归零
   $('#shops-list').html('');
    shopsList(3);
}