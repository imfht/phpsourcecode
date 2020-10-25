$.fn.selectOption=function(opt) {
	$("#"+opt+"").live("click", function(){
		if(opt=="subbrand"){
			var brandid = $("input[name=brand]").val();
		}
		$.get("index.php?m=ajax&ajax=1&"+opt+"=1&brandid="+brandid, { 
		}, function (data, textStatus){
			$("#sub_option").show();
			$("#sub_option").html(data); // 把返回的数据添加到页面上
			$("#superbox").hide();
			if(opt=="brand"){
				var subbrand = $("#subbrand");
				if(subbrand.length==0){
					$("#"+opt+"").parent().after("<li><a href='javascript:void(0);' id='subbrand'><span class='all'>不限</span><label>车系</label><input type='hidden' name='subbrand' value=''></a>");
				}
			}
		});
	});
};