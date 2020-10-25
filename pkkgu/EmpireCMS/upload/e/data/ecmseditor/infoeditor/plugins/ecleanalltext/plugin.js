    CKEDITOR.plugins.add('ecleanalltext',  
    {  
        init: function(editor)      
        {          
            //plugin code goes here  
            var pluginName = 'ecleanalltext';   
			//CKEDITOR.dialog.add(pluginName, this.path + 'dialogs/flvPlayer.js');          
			editor.addCommand(pluginName,{exec:function(editor){
				var htmlContent=editor.getData();				
				//允许的标签项
				allowTags = ['p', 'a', 'img', 'br'];
				//验证的正则
				tagPatrn = /<\s*([\/]?)\s*([\w]+)[^>]*>/ig;
				//删除允许范围之外的标签
				htmlContent = htmlContent.replace(tagPatrn, function(withTag, isClose, htmlTag){
					var htmlReturn = '';
					//alert('isClose:'+isClose+'#### tag:'+htmlTag);
					htmlTag = htmlTag.toLowerCase();
					for (i = 0; i < allowTags.length; i++){
						if(allowTags[i] != htmlTag){
							continue;
						}
						if(isClose == ''){
							switch(htmlTag){
								case 'p':
									htmlReturn = '<p>';
									break;
								case 'a':
									htmlReturn = withTag;
									break;
								case 'br':
									htmlReturn = '</p><p>';
									break;
								default:
									htmlReturn = withTag;
									break;
							}
						}else
							htmlReturn = withTag;
						break;
					}
					return htmlReturn;
				});
				htmlContent = htmlContent.replace(/<a\s[^>]*>([^<]*)<\/a>/img,'$1');// remove link
				htmlContent = htmlContent.replace(/<p>(\s|&nbsp;|\u20)*(.*)<\/p>/img,function(a, b, c){
					if(c =='') return  '';
					else return '<p>'+c+'</p>';
				});
				editor.setData(htmlContent);
			}});

            editor.ui.addButton('ecleanalltext',  
            {                 
                label: '一键清理代码',  
                command: pluginName,
				icon: this.path + 'images/cleanalltext.gif'
            });  
        }  
    }); 
	