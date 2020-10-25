/* -----------H-ui前端框架-------------
* H-ui.admin.www.js v2.5
* http://www.h-ui.net/
* Created & Modified by guojunhui
* Date modified 2016.12.13
*
* Copyright 2013-2016 北京颖杰联创科技有限公司 All rights reserved.
* Licensed under MIT license.
* http://opensource.org/licenses/MIT
*
*/
/*左侧菜单响应式*/
function Huiasidedisplay(){
    if($(window).width()>=768){
        $(".Hui-aside").show()
    } 
}

$(function(){
    Huiasidedisplay();
    var resizeID;
    $(window).resize(function(){
        clearTimeout(resizeID);
        resizeID = setTimeout(function(){
            Huiasidedisplay();
        },500);
    });
    
    $(".nav-toggle").click(function(){
        $(".Hui-aside").slideToggle();
    });
    $(".Hui-aside").on("click",".menu_dropdown dd li a",function(){
        if($(window).width()<768){
            $(".Hui-aside").slideToggle();
        }
    });
    /*左侧菜单*/
    $.Huifold(".menu_dropdown dl dt",".menu_dropdown dl dd","fast",1,"click");  
});