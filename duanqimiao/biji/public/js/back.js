/**
 * Created by dell on 2016/9/5.
 */
jQuery( document ).ready(function( $ ) {
   /* $('#start a').click(function(){
        $('#user_img').addClass('animated rotateIn');
        setTimeout(function(){
            $('#user_img').removeClass('rotateIn');
        },3000);
    });*/
    var count;
    $('.star1').click(function(){
        count=1;
        $('.star1').attr('src','/images/blue_star.png');
        $('.star1').addClass('animated rotateIn');
        setTimeout(function(){
            $('.star1').removeClass('rotateIn');
        },3000);
    });
    $('.star2').click(function(){
        count=2;
        $('.star1').attr('src','/images/blue_star.png');
        $('.star2').attr('src','/images/blue_star.png');
        $('.star2').addClass('animated rotateIn');
        setTimeout(function(){
            $('.star2').removeClass('rotateIn');
        },3000);
    });
    $('.star3').click(function(){
        count=3;
        $('.star1').attr('src','/images/blue_star.png');
        $('.star2').attr('src','/images/blue_star.png');
        $('.star3').attr('src','/images/blue_star.png');
        $('.star3').addClass('animated rotateIn');
        setTimeout(function(){
            $('.star3').removeClass('rotateIn');
        },3000);
    });
    $('.star4').click(function(){
        count=4;
        $('.star1').attr('src','/images/blue_star.png');
        $('.star2').attr('src','/images/blue_star.png');
        $('.star3').attr('src','/images/blue_star.png');
        $('.star4').attr('src','/images/blue_star.png');
        $('.star4').addClass('animated rotateIn');
        setTimeout(function(){
            $('.star4').removeClass('rotateIn');
        },3000);
    });
    $('.star5').click(function(){
        count=5;
        $('.star1').attr('src','/images/blue_star.png');
        $('.star2').attr('src','/images/blue_star.png');
        $('.star3').attr('src','/images/blue_star.png');
        $('.star4').attr('src','/images/blue_star.png');
        $('.star5').attr('src','/images/blue_star.png');
        $('.star5').addClass('animated rotateIn');
        setTimeout(function(){
            $('.star5').removeClass('rotateIn');
        },3000);
    });

    $('.star1').dblclick(function(){
        count=0;
        $('.star1').attr('src','/images/star.png');
        $('.star2').attr('src','/images/star.png');
        $('.star3').attr('src','/images/star.png');
        $('.star4').attr('src','/images/star.png');
        $('.star5').attr('src','/images/star.png');

    });
    $('.star2').dblclick(function(){
        count=1;
        $('.star2').attr('src','/images/star.png');
        $('.star3').attr('src','/images/star.png');
        $('.star4').attr('src','/images/star.png');
        $('.star5').attr('src','/images/star.png');

    });
    $('.star3').dblclick(function(){
        count=2;
        $('.star3').attr('src','/images/star.png');
        $('.star4').attr('src','/images/star.png');
        $('.star5').attr('src','/images/star.png');

    });
    $('.star4').dblclick(function(){
        count=3;
        $('.star4').attr('src','/images/star.png');
        $('.star5').attr('src','/images/star.png');

    });
    $('.star5').dblclick(function(){
        count=4;
        $('.star5').attr('src','/images/star.png');

    });

    $('.star_btn').click(function(){
        $.ajax({
            type:'GET',
            url:'/fedBack/count',
            data:{
                'count':count
            },
            success:function(data){
                var d = dialog({
                    title: '提示',
                    content: data.info,
                    width: 220
                });
                d.show();
                setTimeout(function () {
                    window.history.back(-1);
                }, 3000);
            }
        })
    });
});
