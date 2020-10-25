// JavaScript Document
(function($){
	$.fn.extend({
		"insert":function(value){
			//默认参数
			value=$.extend({
				"text":"test123"
			},value);
			
			var dthis = $(this)[0]; //将jQuery对象转换为DOM元素
			
			//IE下
			if(document.selection){
				
				$(dthis).focus();		//输入元素textara获取焦点
				var fus = document.selection.createRange();//获取光标位置
				fus.text = value.text;	//在光标位置插入值
				$(dthis).focus();	///输入元素textara获取焦点
				
			
			}
			//火狐下标准	
			else if(dthis.selectionStart || dthis.selectionStart == '0'){
				
				var start = dthis.selectionStart; 
				var end = dthis.selectionEnd;
				var top = dthis.scrollTop;
				
				//以下这句，应该是在焦点之前，和焦点之后的位置，中间插入我们传入的值
				dthis.value = dthis.value.substring(0, start) + value.text + dthis.value.substring(end, dthis.value.length);
			}
			
			//在输入元素textara没有定位光标的情况
			else{
				this.value += value.text;
				this.focus();	
			};
			
			return $(this);
		}
	})
})(jQuery)

//$(".tarea").insert({"text":"表情"});