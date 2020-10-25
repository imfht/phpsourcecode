console.log(window);
	window.SITE_URL = window.SITE_URL || '';
	if (document.location.href.indexOf('http://') != 0)	{
		var path = '../script/treeTable/';
	} else {
		var path = SITE_URL + '/script/treeTable/';
	}
	console.log(path);

define(function (require,exports,module){
	//加载依赖CSS
	require('./effect.css');
	//加载依赖JS
	var $ = require('jquery');
	var dance = require('dance');

	//私有变量
	var defaultVal='effect';
	//私有方法
	function cc(idVal){
		if(!idVal) idVal= defaultVal;
		$('#'+idVal).animate({
			fontSize: "40px",
		},1000).animate({
			fontSize: "20px",
		},1000);
		$('#'+idVal).addClass('blue').append(dance);
		$('#'+idVal);
	}

	$.fn.myDance = function(time) {$(this).html(dance+'$.fn.myDance');}

	$.ourDance = function(id) {$(id).html(dance+'$.ourDance');}

	//公用变量
	exports.myStyle = 'myStyleClassName';
	//公用方法
	exports.changeSize=function(idVal){
		cc(idVal);
	};

	//公共对象effect.myObject
	exports.myObject = {
		li0:'123456',
		li1:'456798',
	};
	exports.myObject.done=function(){
		alert(1111);
	}

	//若返回 则只有对外只有一个接口
	//return defaultVal;

});

define('dance',function (require){
	return '<b>10:00 开始跳舞</b>';
})