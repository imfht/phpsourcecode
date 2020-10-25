( function() {
	CKEDITOR.plugins.add('etranmore',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'etranmore';   
			CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/etranmore.js');          
			editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));

            editor.ui.addButton('etranmore',  
            {                 
                label: '上传多图片',  
                command: pluginName,
				icon: this.path + 'images/tranmore.gif',
				toolbar: 'etranmore'
            });  
        }  
    })
	} )();
	