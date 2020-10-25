$(function(){

    var viewSwiper = new Swiper('.view .swiper-container', {
        // 如果需要前进后退按钮
        prevButton:'.swiper-button-prev',
        nextButton:'.swiper-button-next',
        onInit: function(swiper){
            $('.preview .swiper-slide:first').addClass('active-nav');
        },
        onSlideChangeStart: function(swiper){
            updateNavPosition();
        }
    })

    var previewSwiper = new Swiper('.preview .swiper-container', {
        visibilityFullFit: true,
        slidesPerView: 'auto',
        allowTouchMove: false,
        onTap: function() {
            viewSwiper.slideTo(previewSwiper.clickedIndex);
        }
    })

    function updateNavPosition() {
        $('.preview .swiper-slide').removeClass('active-nav');
        $('.preview .swiper-slide').each(function(){
            $(this).removeClass('active-nav');

            if($(this).data('id') == viewSwiper.activeIndex+1){
                $(this).addClass('active-nav');
            }
        });
    }
});


$(function(){
    var product_sku = $('input[data-toggle="product_sku"]').val();
        product_sku = $.parseJSON(product_sku);
    $('.sku-content').Muusku({
        sku_data:product_sku
    });
});

$(function(){
    /*
    直接购买订单
    */
    var product_sku = $('input[data-toggle="product_sku"]').val();
        product_sku = $.parseJSON(product_sku);
    $('.buyImmediately').click(function () {
        /*数量*/
        var product_id = $('[data-type="title"]').data('id');
        var quantity = $('.count-input').val();
        quantity = (isNaN(quantity))?1:quantity;
        /*规格*/
        if(product_sku){
            var sku_index = $('.sku-content').find('input').val();
            if(sku_index){
                //console.log('sku-ready',sku_index);
                window.location.href='index.php?s=/muushop/order/makeorder'+'&id='+product_id+'&quantity='+quantity+'&sku='+sku_index;
            }else{
                alert('请选择商品规格');
            }
        }else{
            window.location.href='index.php?s=/muushop/order/makeorder'+'&id='+product_id+'&quantity='+quantity;
        } 
    });
})

$(function(){
    /*
    加入购物车
     */
    var product_sku = $('input[data-toggle="product_sku"]').val();
        product_sku = $.parseJSON(product_sku);
    $('.addCartNow').click(function () {
        /*数量*/
        var quantity = $('.count-input').val();
        quantity = (isNaN(quantity))?1:quantity;
        var sku_uid = $('[data-type="title"]').data('id');
        /*规格*/
        if(product_sku){
            var sku_index = $('.sku-content').find('input').val();
            if(sku_index){
                sku_uid += ';'+sku_index;
            }else{
                alert('请选择商品规格');
                return;
            }
        }
        var data = {
            sku_id:sku_uid,
            quantity:quantity
        };
        var url = $('input[data-toggle="add_cart_url"]').val();
        $.post(url,data, function (ret) {
            if(ret.status==1){
                $.get_cart_count();
                toast.success(ret.info, '温馨提示');
            }
            else{
                toast.error(ret.info, '温馨提示');
            }
        })
    });
});



//商品购买数量
$(function(){
    var cutBtn = $('.count-content .cut-btn');
    var addBtn = $('.count-content .add-btn');
    var countInput = $('.count-content .count-input');
    cutBtn.click(function(){
        if(countInput.val()<=1)return;
        countInput.val(parseInt(countInput.val())-1);
    });
    addBtn.click(function(){
        var quantity =parseInt($('.quantity span').text());
        if(countInput.val()>=quantity)return;
        countInput.val(parseInt(countInput.val())+1);
    });
})

//评价
$(function(){

    //默认加载第一页
    ajax_get(1,10);

    function ajax_get(page,r){

        page=page||1;
        r=r||10; 
        var product_id = $('[data-toggle="product_id"]').val();
        var url = '/muushop/api/comment/page/'+page+'/r/'+r+'/product_id/'+product_id;
        $.get(url,function(ret){
            if(ret.status==1){
                var data = ret.data.list;
                if(data==null){
                    data='';
                }
                $('#comment .list-box').html('');
                for(var i=0;i<data.length;i++){
                    
                    var sku = '';

                    for(var k=0;k<data[i]['sku'].length;k++){
                        sku += '<span class="sku_item">'+data[i]['sku'][k]+'</span>';
                    }
                    var html_str = '';
                        html_str = '<div class="comment-item clearfix">'+
                                   '<div class="col-xs-2">'+
                                   '<div class="user-item">'+
                                   '<div class="avatar"><img src="'+data[i].user.avatar32+'" class="img-circle" /></div>'+
                                   '<div class="nickname">'+data[i].user.nickname+'</div>'+
                                   '</div>'+
                                   '</div>'+
                                   '<div class="col-xs-10">'+
                                   '<div class="score clearfix"><div class="atar_Show"><p tip="'+data[i].score+'"></p></div><span></span></div>'+
                                   '<div class="brief">'+data[i].brief+'</div>'+
                                   '<div class="sku">'+sku+' <span class="create_time">'+ data[i].create_time+'</span></div>'+
                                   '</div>'+
                                   '</div>';

                    $('#comment .list-box').append(html_str);
                }
                //显示分数
                $("#comment .list-box .score p").each(function(index, element) {
                    var num=$(this).attr("tip");
                    var w=num*2*16;//
                    $(this).css("width",w);
                    $(this).parent(".atar_Show").siblings("span").text(num+"分");
                });
            //页码 
            var totalCount = ret.data.totalCount;
            
            var totalPage = Math.ceil(totalCount/r);
            //当前页数给dom
            $('#comment .page-box .now_page').text(page);
            //测试写死总页数
            //totalPage =30;    
            $('#comment .page-box .pager').html('');//清空页码
                if(totalPage>1){
                    //显示上一页按钮
                    if(page>1){
                        var next_html = '<li class="previous"><a href="javascript:;">« 上一页</a></li>';
                        $('#comment .page-box .pager').append(next_html);
                    }
                    if(page>3){
                        var one_html = '<li class="page_num"><a href="javascript:;">1</a></li>';
                            one_html += '<li><a>...</a></li>';
                         $('#comment .page-box .pager').append(one_html);
                    }
                    //遍历页码
                    for(var i=1;i<=totalPage;i++){
                        var page_html = '';
                            
                            if(page+3>i && page-3<i){
                                page_html = '<li class="page_num"><a href="javascript:;">'+i+'</a></li>';
                            }

                            if(page<3 && i<6){
                                page_html = '<li class="page_num"><a href="javascript:;">'+i+'</a></li>';
                            }
                            if(i+1>totalPage){
                                page_html = '<li><a>...</a></li>'
                                page_html += '<li class="page_num"><a href="javascript:;">'+i+'</a></li>';
                            }
                            if(page==i){
                                page_html = '<li class="page_num active"><a href="javascript:;">'+i+'</a></li>';
                            }
                        $('#comment .page-box .pager').append(page_html);
                    }
                   

                    //显示下一页按钮
                    if(totalPage > page){
                        var next_html = '<li class="next"><a href="javascript:;">下一页 »</a></li>';
                        $('#comment .page-box .pager').append(next_html);
                    }
                }
            }
        })  
    }
    //绑定事件
    //点击数字
    $(document).on('click','.page-box .pager li.page_num a',function(){
        var goPage = parseInt($(this).text());
        ajax_get(goPage);
        return false;
    });
    //下一页
    $(document).on('click','.page-box .pager li.next a',function(){
        var goPage = parseInt($('#comment .page-box .now_page').text());
        ajax_get(goPage+1);
        //$('.page-box .pager li.next a').unbind('click');
        return false;
    });
    //上一页
    $(document).on('click','.page-box .pager li.previous a',function(){
        var goPage = parseInt($('#comment .page-box .now_page').text());
        ajax_get(goPage-1);
        //$('.page-box .pager li.previous a').unbind('click');
        return false;
    });
})