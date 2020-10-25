( function() {
	CKEDITOR.plugins.add('etranfile',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'etranfile';   
			CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/etranfile.js');          
			editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));

            editor.ui.addButton('etranfile',  
            {                 
                label: '附件',  
                command: pluginName,
				icon: this.path + 'images/tranfile.gif',
				toolbar: 'etranfile'
            });  
        }  
    })
	} )();
	