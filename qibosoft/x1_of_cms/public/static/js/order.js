function check_post(){
	var ck = true;
	$(".must-choose").each(function(){
		var obj = $(this).find('input');
		if(obj.length<0){
			//obj = $(this).find('select');
		}
		if(obj.length==1 && obj.val()==''){
			obj.focus();
			layer.alert("必填项,不能为空!");
			ck = false;
			return ;
		}
	});
	
	var array = [];
	$(".customize").each(function(){
		var va = '';
		var tp = $(this).data('type');
		if(tp=="select"){
			va = $(this).find('select').val();
		}else if(tp=="checkbox"){			
			$(this).find('input:checked').each(function(){
				va += "、" + $(this).val();
			});
			va = va.substring(1);
		}else if( $(this).data('name') && $(this).find('input[name='+$(this).data('name')+']').length>0 ){
			va = $(this).find('input[name='+$(this).data('name')+']').val();
		}else{
			va = $(this).find('input').length>0 ? $(this).find('input').val() : $(this).find('textarea').val();
		}
		if(va!=''){
			array.push( {
			title:$(this).data('title'),
			type:tp,
			value:va
			} );
		}		
	});
	if(array.length>0){
		$("#order_field").val( JSON.stringify(array)  );
	}
	return ck;
}

$(function(){
	$('form input[type="text"]').each(function(){
		if($(this).prev().hasClass("title")){
			$(this).prev().hide();	//文本框就不需要显示标题提示描述
		}
	});
})