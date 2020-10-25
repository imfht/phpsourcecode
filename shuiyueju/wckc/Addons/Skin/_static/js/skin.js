var root=_ROOT_;
var DefaultSkinHref;
$(function(){
    DefaultSkinHref=$('[data-role="skin_link"]').attr('href');
    $('[data-role="SkinPreview"]').find('[data-role="colorBox"]').mouseenter(function(){
        $(this).find('[data-role="skin_title"]').animate({bottom:'0px'});
    }).mouseleave(function(){
        $(this).find('[data-role="skin_title"]').animate({bottom:'-27px'});
    });
    $('[data-role="USelectSkin"]').click(function(){
        $('[data-role="skin_link"]').attr('href',DefaultSkinHref);
    });
    $('[data-role="SelectSkin"]').click(function(){
        var skin=$('#default').val();
        var url=$('#saveAddonUrl').val();
        $.post(url,{skin:skin},function(msg){
            if(msg.status){
                $('.mfp-close').click();
                toast.success(msg.info);
            }else{
                handleAjax(msg);
            }
        },'json');
    });
    $('[data-role="SelectSkinDefault"]').click(function(){
        var url=$('#saveAddonUrl').val();
        $.post(url,{set_default:1},function(msg){
            if(msg.status){
                $('.mfp-close').click();
                var href=root+'/Addons/Skin/Skins/'+msg.defaultSkin+'/style.css';
                $('[data-role="skin_link"]').attr('href',href);

                toast.success(msg.info);
            }else{
                handleAjax(msg);
            }
        },'json');
    });
});

/**
 * 改变选中样式
 * @param string key 选中样式Key值
 * @param object this 点击按钮对象
 * @return void
 */
var fChange = function (key, obj) {
    $('#style_list').find('[data-role="colorBox"]').removeClass('current');
    $(obj).addClass('current');
    $('#default').val(key);
    var href=root+'/Addons/Skin/Skins/'+key+'/style.css';
    $('[data-role="skin_link"]').attr('href',href);
    return false;
}