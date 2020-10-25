(function($) {
	

$.fn.extend({
   
	commonMask: function(settings)
    
	{
        
		var options = 
		{
			upperletter:true,
			lowerletter:false,
			number:false,
			underline:false
		};
		settings = settings || {};
		$.extend(options, settings);
		return this.each(function(){
	        
			$(this).keydown(function(event){
				var nKeyCode = event.keyCode; 
				//如果是下划线
				if(options.underline){
					if(event.shiftKey && event.keyCode==189) return;
				}
				//大小写都可以(大小写不能通过键值来区别)
				if(options.upperletter && options.lowerletter && nKeyCode >=65 && nKeyCode<=90) return;
				
				//按键常量
				var KEY = {
					BACKSPACE: 8,
					TAB: 9,
					ENTER: 13,
					SHIFT: 16,
					END: 35,
					HOME: 36,
					LEFT: 37,
					RIGTH: 39,
					DEL: 46
				};
				// 特殊处理的按键 
				switch(nKeyCode){
					case KEY.TAB:
					case KEY.HOME:
					case KEY.SHIFT:
					case KEY.END:
					case KEY.LEFT:
					case KEY.RIGTH:
					case KEY.BACKSPACE:
					case KEY.DEL:
					case KEY.ENTER:
						return;
				}
				// 当前光标位置 
				var start = $.common.GetCursor(this).start;
				var end = $.common.GetCursor(this).end;
				//忽略按键
				event.returnValue = false; 
				//阻止冒泡
				event.preventDefault();
				var isValid = false;
				//如果是数字
				if(options.number){
					if(nKeyCode >=48 && nKeyCode<=57 || nKeyCode >=96 && nKeyCode<=105)
					{
						isValid = true;
						if (nKeyCode > 95) nKeyCode -= (95-47);
					}
				}
				//如果是字母
				if(!isValid && (options.upperletter || options.lowerletter) && nKeyCode >=65 && nKeyCode<=90){
					if(options.upperletter & options.lowerletter) return;
					isValid = true;
				}
				//如果是有效的KeyCode
				if(isValid)
				{
					//根据配置进行字符转换
					var keycode = String.fromCharCode(nKeyCode); 
					if(options.upperletter && !options.lowerletter) keycode = keycode.toUpperCase();
					if(!options.upperletter && options.lowerletter) keycode = keycode.toLowerCase();
					//连接值
					var strText = $(this).val();
					strText =  strText.substr(0,start) + keycode + strText.substr(end);
					$(this).val(strText);
					//设置光标的位置
					$.common.Selection(this,start + 1,start + 1);
				}
			});
	    
		});
 		
	}	

});



$.common = 
{
	//动作：获取光标所在的位置，包括起始位置和结束位置
	GetCursor : function(textBox){
		var obj = new Object();
		var start = 0,end = 0;
		if ($.browser.mozilla) {
			start = textBox.selectionStart;
			end = textBox.selectionEnd;
		}
		if ($.browser.msie) {
			var range=textBox.createTextRange(); 
			var text = range.text;
			var selrange = document.selection.createRange();
			var seltext = selrange.text;
			while(selrange.compareEndPoints("StartToStart",range)>0){ 
				selrange.moveStart("character",-1); 
				start ++;
			}
			while(selrange.compareEndPoints("EndToStart",range)>0){ 
				selrange.moveEnd("character",-1); 
				end ++;
			}
		}
		obj.start = start;
		obj.end = end;
		return obj;
	},
	
	//动作：让field的start到end被选中
	Selection : function(field, start, end) 
	{
		if( field.createTextRange ){
			var r = field.createTextRange();
			r.moveStart('character',start);
			r.collapse(true);
			r.select(); 
		} else if( field.setSelectionRange ){
			field.setSelectionRange(start, end);
		} else {
			if( field.selectionStart ){
				field.selectionStart = start;
				field.selectionEnd = end;
			}
		}
		field.focus();
	}

}


})(jQuery);