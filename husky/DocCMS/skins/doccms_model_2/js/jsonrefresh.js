function loadjson(){
	$.ajaxSetup({ cache: false });
	$.ajax({
    	url: "/api/indexdata",
    	data: {},
    	type: "get",
    	dataType: "json",
    	success: function(data) {
		$('#onlineUserJson').html(data.OLCM);
		$('#onlineJoomeJson').html(data.OLCL);
		$('#netUsageJson').html(data.RATE);
		}
	}); 
}

Counter = 0;
function DoCounter()
{
    SlideParent = document.getElementById("sliderParent");
    Slide = document.getElementById("slider");
    Refresh = document.getElementById("refresh");
    Slide.style.width = Counter/10 + "%";
    if(Counter++ < 1000) {
        setTimeout("DoCounter()",100);
    }
	else {
		Counter = 0;
		DoCounter();
		loadjson();
		$("#info").animate({ color: "#CC0000" }, 1000);
		$("#info").animate({ color: "#FFFFFF" }, 1000);
	}
	Refresh.onclick = function() {
		Counter = 0;
		Img.rotate('refresh', 360);
		loadjson();
		$("#info").animate({ color: "#CC0000" }, 1000);
		$("#info").animate({ color: "#FFFFFF" }, 1000);
	}
}

var Img = function() {
	var T$ = function(id) { return document.getElementById(id); }
	var ua = navigator.userAgent,
		isIE = /msie/i.test(ua) && !window.opera;
	var i = 0, sinDeg = 0, cosDeg = 0, timer = null ;
	var rotate = function(target, degree) {
		target = T$(target);
		var orginW = target.clientWidth, orginH = target.clientHeight;
			clearInterval(timer);
		function run(angle) {
			if (isIE) { // IE

			} else if (target.style.MozTransform !== undefined) {  // Mozilla
				target.style.MozTransform = 'rotate(' + angle + 'deg)';
			} else if (target.style.OTransform !== undefined) {   // Opera
				target.style.OTransform = 'rotate(' + angle + 'deg)';
			} else if (target.style.webkitTransform !== undefined) { // Chrome Safari
				target.style.webkitTransform = 'rotate(' + angle + 'deg)';
			} else {
				target.style.transform = "rotate(" + angle + "deg)";
			}
		}
		
		timer = setInterval(function() {
			i += 10;
			run(i);
			if (i > degree - 1) {
				i = 0;
				clearInterval(timer);
			} 
		}, 10); 
	}
	return {rotate: rotate}
}();

$(document).ready(function(){ 
	$.ajaxSetup({ cache: false });
	loadjson();
	DoCounter();
}); 