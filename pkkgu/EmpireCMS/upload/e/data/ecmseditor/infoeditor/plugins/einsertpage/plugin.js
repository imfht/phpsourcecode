    CKEDITOR.plugins.add('einsertpage',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'einsertpage';   
			//CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/flvPlayer.js');          
			editor.addCommand(pluginName,{exec:function(editor){
				var pagestr='';
				pagestr=prompt('分页标题(不写请直接点确定)','');
				if(pagestr!=''&&pagestr!=null)
				{
					pagestr=pagestr+'[/!--empirenews.page--]';
				}
				if(pagestr==null)
				{return '';}
				editor.insertHtml('[!--empirenews.page--]'+pagestr);
			}});

            editor.ui.addButton('einsertpage',  
            {                 
                label: '插入分页符',  
                command: pluginName,
				icon: this.path + 'images/insertpage.gif'
            });  
        }  
    }); 
	