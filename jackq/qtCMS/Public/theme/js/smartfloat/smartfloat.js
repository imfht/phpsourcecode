/**
 * Created by Administrator on 2014/12/16.
 */
jQuery.fn.smartfloat = function(loaded) {
    var obj = this;
    body_height = parseInt($(window).height());
    block_height = parseInt(obj.height());
    scrollTop = 280;
    if($(window).scrollTop()>280) {
        scrollTop = $(window).scrollTop();
    }
    top_position = parseInt((body_height/2) - (block_height/2) + scrollTop);
    if (body_height<block_height) { top_position = 0 + scrollTop; };
    if(!loaded) {
        obj.css({'position': 'absolute'});
        obj.css({ 'top': top_position });
        $(window).bind('resize', function() {
            obj.smartfloat(!loaded);
        });
        $(window).bind('scroll', function() {
            obj.smartfloat(!loaded);
        });
    } else {
        obj.stop();
        obj.css({'position': 'absolute'});
        obj.animate({ 'top': top_position }, 400, 'linear');
    }
}