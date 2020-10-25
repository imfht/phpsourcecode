jQuery.noConflict();
var loading = false;
$(function(){
    var pageId = $('#shopHomePageId').val();
    $('.wst-se-search').on('submit', '.input-form', function(event){
        event.preventDefault();
    })
    if(pageId==undefined) {
        fixedTerm();
        shopBest();
        WST.imgAdapt('j-imgIndex');
        // 商品分类
        var h = WST.pageHeight();
        var dataHeight = $("#frame").css('height');
        if (parseInt(dataHeight) > h - 42) {
            $('#content').css('overflow-y', 'scroll').css('height', h - 42);
        }
        // 广告
        new Swiper('.banner', {
            autoplay: true,
            autoHeight: true, //高度随内容变化
            width: window.innerWidth,
            on: {
                resize: function () {
                    this.params.width = window.innerWidth;
                    this.update();
                },
            },
            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
            },
        });
        $(window).scroll(function () {
            if (loading) return;
            if ((($(window).scrollTop() + $(window).height()) + 50) >= $(document).height()) {
                if ($('#currPage').val() < $('#totalPage').val())shopsList();
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

function shopAds(){
     //广告
    var slider = new fz.Scroll('.ui-slider', {
        role: 'slider',
        indicator: true,
        autoplay: true,
        interval: 3000
    });
}
//排序条件
function orderCondition(obj,condition){
	jQuery('html,body').scrollTop($('#j-top').offset().top);
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

//查看
function switchTerm(n){
	if(parseInt($('#j-top').offset().top)>$(window).scrollTop()){
		jQuery('html,body').animate({scrollTop:$('#j-top').offset().top}, 800);
	}
	$('#j-top'+n).addClass('active');
	if(n==1){
        $('#j-index1').show();
        $('#j-index0').hide();
        $('#j-top0').removeClass('active');
    }else{
        $('#j-index0').show();
        $('#j-index1').hide();
        $('#currPage').val('0');
        $('#shops-list').html('');
        $('#j-top1').removeClass('active');
	    shopsList();
	}
}
function fixedTerm(){
    var offsetTop = $("#j-top").offset().top;
    $(window).scroll(function() {
        var scrollTop = $(window).scrollTop(); 
        if (scrollTop > offsetTop){
            $("#j-top").addClass('active');
        }else{  
            $("#j-top").removeClass('active');
        }
    });
}

//获取商品列表销量
function shopBest(){
    loading = true;
    var param = {};
    param.shopId = $('#shopId').val();
    param.msort = 2;
    param.mdesc = 1;
    param.pagesize = 6;
    $.post(WST.U('mobile/shops/getShopGoods'), param, function(data){
        var json = WST.toJson(data);
        var html = '';
        if(json && json.data && json.data.length>0){
            var gettpl = document.getElementById('shopBest').innerHTML;
              laytpl(gettpl).render(json.data, function(html){
                $('#best-list').append(html);
              });
        }
        WST.imgAdapt('j-imgBest');
        loading = false;
    });
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
function goGoodsList(shopId,ct1,ct2){
    var param = 'shopId='+shopId+'&ct1='+ct1;
    if(ct2)
        param += '&ct2='+ct2;
    param.shopId = 1;
    location.href=WST.U('mobile/shops/goods',param,true);
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