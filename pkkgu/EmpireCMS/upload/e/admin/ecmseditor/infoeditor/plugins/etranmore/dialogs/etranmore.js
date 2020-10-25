(function() {  
    CKEDITOR.dialog.add("etranmore",   
    function(ecmseditorv) {  
        return {  
            title: "上传多图片",  
            minWidth: "780",  
            minHeight:"600",  
            contents: [{  
                id: "info1",  
                label: "常规",  
                title: "常规",  
                expand: true,  
                width: "780px",  
                height: "560px",  
                padding: 0,  
                elements: [{  
                    type: "html",  
                    style: "width:780px;height:560px",  
                    html: ' <IFRAME frameBorder="0" id="edtmore'+ecmseditorv.name+'" name="edtmore'+ecmseditorv.name+'" scrolling="auto" src="'+ecmseditorv.config.filebrowserFlashUploadUrl+'editorpage/ecmseditorpage.php?'+ecmseditorv.config.filebrowserFlashBrowseUrl+'&doecmspage=TranMore&type=1&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name+'&CKEditorFuncNum='+ecmseditorv._.filebrowserFn+'&langCode='+ecmseditorv.langCode+'" style="HEIGHT:500px;VISIBILITY:inherit;WIDTH:100%;Z-INDEX:2"></IFRAME>'  
                }
				
	
				]  
            }
			
			
			
			],  
			
			buttons : [ CKEDITOR.dialog.cancelButton ],
			
            onOk: function() {  
				mywidth　=　this.getValueOf('info1',　'txtUrl2'); 
                //点击确定按钮后的操作  
                //ecmseditorv.insertHtml("编辑器追加内容");  
				ecmseditorv.insertHtml("编辑器追加内容"+mywidth);
				//CKEDITOR.dialog.hide();
            }  
        }  
    })  
})(); 


function EHEcmsEditorDoTranMore(str){
	CKEDITOR.dialog.getCurrent().getParentEditor().insertHtml(str);
	CKEDITOR.dialog.getCurrent().hide();
}

function EHEcmsEditorDoTranMoreTool(str){
	CKEDITOR.dialog.getCurrent().getParentEditor().insertHtml(str);
	CKEDITOR.dialog.getCurrent().hide();
}

