    CKEDITOR.plugins.add('einsertbr',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'einsertbr';   
			//CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/flvPlayer.js');          
			editor.addCommand(pluginName,{exec:function(editor){
				editor.insertHtml('<br/>');
			}});

            editor.ui.addButton('einsertbr',  
            {                 
                label: '插入<br>',  
                command: pluginName,
				icon: this.path + 'images/insertbr.gif'
            });  
        }  
    }); 
	