
/*
**************************
(C)2010-2014 phpMyWind.com
update: 2014-5-31 00:09:35
person: Feng
**************************
*/

$(function(){
	$(".mainBody").css("height", $(window).height() - 210);
})

$(window).resize(function(){
	$(".mainBody").css("height", $(window).height() - 210);
});