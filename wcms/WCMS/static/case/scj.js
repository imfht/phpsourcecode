/**
* 收藏夹功能
* @author wolf
* @Email 116313116@qq.com
*/
function scj(id) {
	var scjArr = new Array();
	var cookie = $.cookie("scj");

	if (cookie != undefined) {
		scjArr = $.parseJSON(cookie);
	}
	//检查是否重复
	for (var i = 0; i < scjArr.length; i++) {
		if (scjArr[i] == id) {
			alert("已经添加");
			return false;
		}

	}

	scjArr.push(id);
	$(".scj_num").html(scjArr.length);
	var str = JSON.stringify(scjArr);
	$.cookie("scj", str, {
		expires : 7,
		path : "/"
	});
	
}
function getScjNum() {
	var cookie = $.cookie("scj");
	if (cookie == undefined) {
		return;
	}
	var scjArr = $.parseJSON(cookie);
	$(".scj_num").html(scjArr.length);
}

function emptyCart(){
	var cookie = $.cookie("scj");
	if (cookie == undefined) {
		return;
	}
	$.cookie('scj', '', { expires: -1 }); 
}

function removeCart(id) {
	$("#scj_" + id).remove();

	var cookie = $.cookie("scj");
	if (cookie == undefined) {
		return;
	}
	var goodsArr = $.parseJSON(cookie);
	for (var i = 0; i < goodsArr.length; i++) {
		if (goodsArr[i] == id) {
			goodsArr.splice(i, 1);
			break;
		}

	}
	$(".scj_num").html(goodsArr.length);
	
	var str = JSON.stringify(goodsArr);
	$.cookie("scj", str, {
		expires : 7,
		path : "/"
	});
}
