( function() {
	CKEDITOR.plugins.add('equotetext',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'equotetext';   
			CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/equotetext.js');          
			editor.addCommand(pluginName, new CKEDITOR.dialogCommand(pluginName));

            editor.ui.addButton('equotetext',  
            {                 
                label: '插入引用',  
                command: pluginName,
				icon: this.path + 'images/equotetext.gif',
				toolbar: 'equotetext'
            });  
        }  
    })
	} )();
	