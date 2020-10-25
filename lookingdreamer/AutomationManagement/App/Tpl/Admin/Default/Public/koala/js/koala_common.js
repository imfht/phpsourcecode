/**
 * KoalaUI公共函数库
 */

/**
 * ajax方式获取json
 */
var jsonData = null;
function getJsonData(_url,_param){
	$.ajax({
		type:"POST",
		url:_url,
		dataType:"text",
		data:_param,
		success:function(data){
			jsonData = eval(data);
			console.info("success,data = " + data);
		},error:function(){
			console.info("error");
		}
	});
	
}  