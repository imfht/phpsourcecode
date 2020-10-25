window.calm={};
calm.bd = $('body')
calm.is_signin = calm.bd.hasClass('logged-in') ? true : false;

if( $('.widget-nav').length ){
    $('.widget-nav li').each(function(e){
        $(this).hover(function(){
            $(this).addClass('active').siblings().removeClass('active')
            $('.widget-navcontent .item:eq('+e+')').addClass('active').siblings().removeClass('active')
        })
    })
}


if( Number(calm.ajaxpager) > 0 && ($('.excerpt').length || $('.excerpt-minic').length) ){
    tbquire(['ias'], function() {
        if( !calm.bd.hasClass('site-minicat') && $('.excerpt').length ){
            $.ias({
                triggerPageThreshold: calm.ajaxpager?Number(calm.ajaxpager)+1:5,
                history: false,
                container : '.content',
                item: '.excerpt',
                pagination: '.pagination',
                next: '.next-page a',
                loader: '<div class="pagination-loading"><img src="'+calm.uri+'/img/loading.gif"></div>',
                trigger: 'More',
                onRenderComplete: function() {
                    tbquire(['lazyload'], function() {
                        $('.excerpt .thumb').lazyload({
                            data_attribute: 'src',
                            placeholder: calm.uri + '/img/thumbnail.png',
                            threshold: 400
                        });
                    });
                }
            });
        }

        if( calm.bd.hasClass('site-minicat') && $('.excerpt-minic').length ){
            $.ias({
                triggerPageThreshold: calm.ajaxpager?Number(calm.ajaxpager)+1:5,
                history: false,
                container : '.content',
                item: '.excerpt-minic',
                pagination: '.pagination',
                next: '.next-page a',
                loader: '<div class="pagination-loading"><img src="'+calm.uri+'/img/loading.gif"></div>',
                trigger: 'More',
                onRenderComplete: function() {
                    tbquire(['lazyload'], function() {
                        $('.excerpt .thumb').lazyload({
                            data_attribute: 'src',
                            placeholder: calm.uri + '/img/thumbnail.png',
                            threshold: 400
                        });
                    });
                }
            });
        }
    });
}


/* 
 * prettyprint
 * ====================================================
*/
$('pre').each(function(){
    if( !$(this).attr('style') ) $(this).addClass('prettyprint')
})

if( $('.prettyprint').length ){
    tbquire(['prettyprint'], function(prettyprint) {
        prettyPrint()
    })
}



/* 
 * rollbar
 * ====================================================
*/
calm.rb_comment = ''
if (calm.bd.hasClass('comment-open')) {
    calm.rb_comment = "<li><a href=\"javascript:(scrollTo('#comments',-15));\"><i class=\"fa fa-comments\"></i></a><h6>去评论<i></i></h6></li>"
}

calm.bd.append('\
    <div class="m-mask"></div>\
    <div class="rollbar"><ul>'
    +calm.rb_comment+
    '<li><a href="javascript:(scrollTo());"><i class="fa fa-angle-up"></i></a><h6>去顶部<i></i></h6></li>\
    </ul></div>\
')



var _wid = $(window).width()
video_ok()
$(window).resize(function(event) {
    _wid = $(window).width()
    video_ok()
});



var scroller = $('.rollbar')
var _fix = (calm.bd.hasClass('nav_fixed') && !calm.bd.hasClass('page-template-navs')) ? true : false
$(window).scroll(function() {
    var h = document.documentElement.scrollTop + document.body.scrollTop

    if( _fix && h > 0 && _wid > 720 ){
        calm.bd.addClass('nav-fixed')
    }else{
        calm.bd.removeClass('nav-fixed')
    }

    h > 200 ? scroller.fadeIn() : scroller.fadeOut();
})

/* 
 * comment
 * ====================================================
*/
if (calm.bd.hasClass('comment-open')) {
    tbquire(['comment'], function(comment) {
        comment.init()
    })
}

/* 
 * page search
 * ====================================================
*/
if( calm.bd.hasClass('search-results') ){
    var val = $('.site-search-form .search-input').val()
    var reg = eval('/'+val+'/i')
    $('.excerpt h2 a, .excerpt .note').each(function(){
        $(this).html( $(this).text().replace(reg, function(w){ return '<b>'+w+'</b>' }) )
    })
}


/* 
 * search
 * ====================================================
*/
$('.search-show').bind('click', function(){
    $(this).find('.fa').toggleClass('fa-remove')
    calm.bd.toggleClass('search-on')
    if( calm.bd.hasClass('search-on') ){
        $('.site-search').find('input').focus()
        calm.bd.removeClass('m-nav-show')
    }
})

/* 
 * phone
 * ====================================================
*/

calm.bd.append( $('.site-navbar').clone().attr('class', 'm-navbar') )

$('.m-icon-nav').on('click', function(){
    calm.bd.addClass('m-nav-show')

    $('.m-mask').show()

    calm.bd.removeClass('search-on')
    $('.search-show .fa').removeClass('fa-remove') 
})

$('.m-mask').on('click', function(){
    $(this).hide()
    calm.bd.removeClass('m-nav-show')
})




if ($('.article-content').length){
    $('.article-content img').attr('data-tag', 'bdshare')
}

function video_ok(){
    $('.article-content embed, .article-content video, .article-content iframe').each(function(){
        var w = $(this).attr('width'),
            h = $(this).attr('height')
        if( h ){
            $(this).css('height', $(this).width()/(w/h))
        }
    })
}


/* functions
 * ====================================================
 */
function scrollTo(name, add, speed) {
    if (!speed) speed = 300
    if (!name) {
        $('html,body').animate({
            scrollTop: 0
        }, speed)
    } else {
        if ($(name).length > 0) {
            $('html,body').animate({
                scrollTop: $(name).offset().top + (add || 0)
            }, speed)
        }
    }
}


$.fn.serializeObject = function(){
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


function strToDate(str, fmt) { //author: meizz   
    if( !fmt ) fmt = 'yyyy-MM-dd hh:mm:ss'
    str = new Date(str*1000)
    var o = {
        "M+": str.getMonth() + 1, //月份   
        "d+": str.getDate(), //日   
        "h+": str.getHours(), //小时   
        "m+": str.getMinutes(), //分   
        "s+": str.getSeconds(), //秒   
        "q+": Math.floor((str.getMonth() + 3) / 3), //季度   
        "S": str.getMilliseconds() //毫秒   
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (str.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


