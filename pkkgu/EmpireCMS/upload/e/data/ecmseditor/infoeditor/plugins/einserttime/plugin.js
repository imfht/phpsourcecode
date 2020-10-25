    CKEDITOR.plugins.add('einserttime',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'einserttime';   
			//CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/flvPlayer.js');          
			editor.addCommand(pluginName,{exec:function(editor){		
				editor.insertHtml(eDoInsertTime());
			}});

            editor.ui.addButton('einserttime',  
            {                 
                label: '当前时间',  
                command: pluginName,
				icon: this.path + 'images/inserttime.gif'
            });  
        }  
    }); 
	
	
function efunformatTime(date, format) {
    var hh = ('0' + date.getHours()).slice(-2),
                ii = ('0' + date.getMinutes()).slice(-2),
                ss = ('0' + date.getSeconds()).slice(-2);
            format = format || 'hh:ii:ss';
            return format.replace(/hh/ig, hh).replace(/ii/ig, ii).replace(/ss/ig, ss);
}
function efunformatDate(date, format) {
            var yyyy = ('000' + date.getFullYear()).slice(-4),
                yy = yyyy.slice(-2),
                mm = ('0' + (date.getMonth()+1)).slice(-2),
                dd = ('0' + date.getDate()).slice(-2);
            format = format || 'yyyy-mm-dd';
            return format.replace(/yyyy/ig, yyyy).replace(/yy/ig, yy).replace(/mm/ig, mm).replace(/dd/ig, dd);
}
		
function eDoInsertTime(){
	var date = new Date;
	var datetimestr='';
	datetimestr=efunformatDate(date,'')+' '+efunformatTime(date,'');
	return datetimestr;
}
