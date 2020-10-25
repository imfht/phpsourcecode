/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
$('<a id="btn_list" href="javascript:;"></a>').insertAfter('#nav').on('click',function(){var menu=$('#menu');if(!menu.data('display')){menu.slideDown('fast');menu.data('display',1)}else{menu.slideUp('fast');menu.data('display',0)}});$('body').append($('<a id="goTop" href="#"></a>'));$('#content img').attr('width','').attr('height','');$('a.previous').html('&lt;');$('a.next').html('&gt;');$(document).ready(function(){window.scrollTo(0,0)});