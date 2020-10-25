jQuery.noConflict();
var currPage = 0;
var lastPage = 0;
var loading = false;
$(function(){
    var pageId = $('#shopHomePageId').val();
      $('.wst-se-search').on('submit', '.input-form', function(event){
          event.preventDefault();
      })
    if(pageId==undefined){
        // 加载商品列表
        var catId = $("#catId").val();
        shopsList(catId);
        var w = WST.pageWidth();
        // 广告
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
        // 商家推荐
        new Swiper('.swiper-container1', {
            slidesPerView: 'auto',
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
                slideChange: function(){
                    echo.init();
                }
            }
        });
        // 推荐
        WST.imgAdapt('j-imgRec');
        // 热卖
        WST.imgAdapt('j-imgRec1');
        $('.wst-gol-adsb').css('height',$('.j-imgRec').width()+20);
        // 商品分类
        var h = WST.pageHeight();
        var dataHeight = $("#frame").css('height');
        if(parseInt(dataHeight)>h-42){
            $('#content').css('overflow-y','scroll').css('height',h-42);
        }
        $(window).scroll(function(){
            if (loading) return;
            if (($(window).scrollTop()) >= ($(document).height() - screen.height-10)) {
                var catId = $('#catId').val();
                currPage = Number( $('#currPage_'+catId).val() );
                lastPage = Number( $('#lastPage_'+catId).val() );
                if(currPage != lastPage ){
                    $('.wst-load-text').html('加载中');
                    shopsList(catId);
                }else{
                    $('.wst-load-text').html('加载完啦');
                }
            }
        });
    }else{
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
        new Swiper('.swiper-container1', {
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
                slideChange: function(){
                    echo.init();
                }
            }
        });
        WST.imgAdapt('j-goods-swiper-img');
    }
});

//弹框
function dataShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
}
function dataHide(){
    jQuery('#frame').animate({'right': '-100%'}, 500);
    jQuery('#cover').hide();
}

function showRight(obj, index){
    $('.wst-goodscate').removeClass('wst-goodscate_selected');
    $(obj).addClass('wst-goodscate_selected');
    $('.goodscate1').eq(index).show().siblings('.goodscate1').hide();
}
function searchGoods(){
    location.href=WST.U('mobile/shops/index','goodsName='+$('#searchKey').val(),true);
}
/*分类*/
function goGoodsList(shopId,ct1,ct2){
    var param = 'shopId='+shopId+'&ct1='+ct1;
    if(ct2)
        param += '&ct2='+ct2;
    param.shopId = 1;
    location.href=WST.U('mobile/shops/goods',param,true);
}

function shopAds(){
     //广告
    var slider = new fz.Scroll('.ui-slider', {
        role: 'slider',
        indicator: true,
        autoplay: true,
        interval: 3000
    });
    var w = WST.pageWidth();
    var h = w*2/6;
        var o = $('.ui-slider').css("padding-top",h);
        var scroll = new fz.Scroll('.ui-slider', {
            scrollY: true
        });
}
//列表
function changeShopTab(obj){
    var catId = $(obj).attr("data");
    $("#catId").val(catId);
    shopsList(catId);
    $(".g_tab_item").removeClass("active");
    $(obj).addClass("active");
    $(".g_item_content").removeClass("g_tab_show").addClass("g_tab_hide");
    $("#g_item_content_"+catId).addClass("g_tab_show").removeClass("g_tab_hide");
}
//获取商品列表
function shopsList(catId){
    $('#Load').show();
     loading = true;
     var param = {};
     param.page = Number( $('#currPage_'+catId).val() ) + 1;
    param.catId = catId;
     $.post(WST.U('mobile/shops/getFloorData'), param, function(data){
         var json = WST.toJson(data);
         if(json){
             $('#currPage_'+catId).val(json.current_page);
             $('#lastPage_'+catId).val(json.last_page);
             var gettpl = document.getElementById('gList').innerHTML;
             laytpl(gettpl).render(json, function(html){
                 $('#goods-list-'+catId).append(html);
             });
             echo.init();
             //WST.imgAdapt('j-imgAdapt');
         }
         loading = false;
         $('#Load').hide();
     });
}

function toShopInfo(sid){
    location.href=WST.U('mobile/shops/view',{'shopId':sid},true)
}
function init(longitude,latitude) {
  var shopName = $('#shopName').val();
  var myLatlng = new qq.maps.LatLng(latitude,longitude);
  var myOptions = {
    zoom: 15,               
    center: myLatlng,      
    mapTypeId: qq.maps.MapTypeId.ROADMAP  
  }
  var map = new qq.maps.Map(document.getElementById("map"), myOptions);
  var marker = new qq.maps.Marker({
        position: myLatlng,
        map: map
    }); 
  var label = new qq.maps.Label({
        position: myLatlng,
        map: map,
        content:shopName
    });
  var cssC = {
        background:'#3A9BFF',
        padding:"2px",
        color: "#fff",
        fontSize: "18px",
    };
  label.setStyle(cssC);
  mapShow();
}
//地图弹框
function mapShow(){
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#container').animate({"right": 0}, 500);
}
function mapHide(){
    var dataHeight = $("#container").css('height');
    var dataWidth = $("#container").css('width');
    jQuery('#container').animate({'right': '-'+dataWidth}, 500);
    jQuery('#cover').hide();
}