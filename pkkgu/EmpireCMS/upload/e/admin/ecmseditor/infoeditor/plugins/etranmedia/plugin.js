( function() {
	CKEDITOR.plugins.add('etranmedia',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'etranmedia';   
			CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/etranmedia.js');          
			editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));

            editor.ui.addButton('etranmedia',  
            {                 
                label: '视频',  
                command: pluginName,
				icon: this.path + 'images/tranmedia.gif',
				toolbar: 'etranmedia'
            });  
        }  
    })
	} )();
	