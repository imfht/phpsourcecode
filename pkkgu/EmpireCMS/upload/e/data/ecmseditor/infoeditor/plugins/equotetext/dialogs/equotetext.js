(function() {  
    CKEDITOR.dialog.add("equotetext",   
    function(ecmseditorv) {  
        return {  
            title: "插入引用",  
            minWidth: "180",  
            minHeight:"110",  
            contents: [{  
                id: "eqtinfo1",  
                label: "常规",  
                title: "常规",  
                expand: true,  
                width: "180px",  
                height: "110px",  
                padding: 0,  
                elements: [
					{id:"eqtbgcolor",type:"text",label:"<strong>背景颜色:</strong>",style:"width: 100%","default":"#DDEDFB"},
					{id:"eqtbordercolor",type:"text",label:"<strong>边框颜色:</strong>",style:"width: 100%","default":"#0099CC"}
				]  
            }
			],  
            onOk: function() {
				var ebgcolor='';
				var ebordercolor='';
				var ecodetext=" ";
				var ehtmlstr='';
				ebgcolor=this.getValueOf('eqtinfo1','eqtbgcolor');
				ebordercolor=this.getValueOf('eqtinfo1','eqtbordercolor');
				ehtmlstr='<table border="0" width="100%" cellspacing="1" cellpadding="10" bgcolor="'+ebordercolor+'"><tr><td width="100%" bgcolor="'+ebgcolor+'" style="word-break:break-all;line-height:18px">'+ecodetext+'</td></tr></table>';
                //点击确定按钮后的操作
				ecmseditorv.insertHtml(ehtmlstr);
            }  
        }  
    })  
})(); 



