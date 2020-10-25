(function() {  
    CKEDITOR.dialog.add("etranfile",   
    function(ecmseditorv) {  
        return {  
            title: "附件",  
            minWidth: "550",  
            minHeight:"440",  
            contents: [{  
                id: "etfileinfo1",  
                label: "插入附件",  
                title: "插入附件",  
                expand: true,  
                width: "550px",  
                height: "440px",  
                padding: 0,  
                elements: [
					{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl1",type:"text",label:"<strong>附件1</strong> (格式： 附件地址##附件名称##附件大小)",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl1",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl2",type:"text",label:"<strong>附件2</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl2",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl3",type:"text",label:"<strong>附件3</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl3",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl4",type:"text",label:"<strong>附件4</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl4",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl5",type:"text",label:"<strong>附件5</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl5",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl6",type:"text",label:"<strong>附件6</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl6",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl7",type:"text",label:"<strong>附件7</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl7",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl8",type:"text",label:"<strong>附件8</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl8",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl9",type:"text",label:"<strong>附件9</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl9",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
				{type:"hbox",widths:["90%","10%"],align:"right",children:[
				{id:"etfileurl10",type:"text",label:"<strong>附件10</strong> ",style:"width:100%;float:left","default":""}
				,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etfileinfo1:etfileurl10",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranFile&type=0&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]}
				
				]  
            },
			
{id:"Upload",label:ecmseditorv.lang.image.upload,elements:[	
	{ 
  	 	type: "html",
	 	style: "width:500;height:250",
	 	html: ' <IFRAME frameBorder="0" id="edtfile'+ecmseditorv.name+'" name="edtfile'+ecmseditorv.name+'" scrolling="auto" src="'+ecmseditorv.config.filebrowserFlashUploadUrl+'editorpage/ecmseditorpage.php?'+ecmseditorv.config.filebrowserFlashBrowseUrl+'&doecmspage=TranFile&type=0&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name+'&CKEditorFuncNum='+ecmseditorv._.filebrowserFn+'&langCode='+ecmseditorv.langCode+'" style="HEIGHT:250px;VISIBILITY:inherit;WIDTH:100%;Z-INDEX:2"></IFRAME>'
	}
]},

			
			],  
            onOk: function() {
				var ehtmlstr=''; 
				var files1=this.getValueOf('etfileinfo1','etfileurl1');
				var files2=this.getValueOf('etfileinfo1','etfileurl2');
				var files3=this.getValueOf('etfileinfo1','etfileurl3');
				var files4=this.getValueOf('etfileinfo1','etfileurl4');
				var files5=this.getValueOf('etfileinfo1','etfileurl5');
				var files6=this.getValueOf('etfileinfo1','etfileurl6');
				var files7=this.getValueOf('etfileinfo1','etfileurl7');
				var files8=this.getValueOf('etfileinfo1','etfileurl8');
				var files9=this.getValueOf('etfileinfo1','etfileurl9');
				var files10=this.getValueOf('etfileinfo1','etfileurl10');
                //点击确定按钮后的操作  
				ehtmlstr+=etranfilehtmlstr(files1,'');
				ehtmlstr+=etranfilehtmlstr(files2,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files3,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files4,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files5,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files6,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files7,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files8,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files9,ehtmlstr);
				ehtmlstr+=etranfilehtmlstr(files10,ehtmlstr);
				
				ecmseditorv.insertHtml(ehtmlstr);
            }  
        }  
    })  
})(); 


function etranfilehtmlstr(filestr,firstfile){
	var expstr='##';
	var filer;
	var htmlstr='';
	var efilename='';
	var efilesize='';
	var efiletype='';
	var efileno='';
	var efileurl='';
	var addfilesize='';
	var addbr='';
	if(filestr=='')
	{
		return '';
	}
	filer=filestr.split(expstr);
	if(filer[0]!=''&&filer[0]!=undefined)
	{
		efileurl=filer[0];
		efilename=etfileReturnFilename(efileurl);
		efiletype=etfileReturnFiletype(efileurl);
	}
	if(filer[1]!=''&&filer[1]!=undefined)
	{
		efileno=filer[1];
	}
	else
	{
		efileno=efilename;
	}
	if(filer[2]!=''&&filer[2]!=undefined)
	{
		efilesize=filer[2];
		addfilesize='&nbsp;('+efilesize+')';
	}
	else
	{
		addfilesize='';
	}
	if(firstfile!='')
	{
		addbr='';
	}
	htmlstr=addbr+'<div style="padding:6px"><fieldset><legend>'+efileno+'</legend><table cellpadding=0 cellspacing=0 border=0><tr><td><a href="'+efileurl+'" title="'+efileno+'" target="_blank">'+efilename+'</a>'+addfilesize+'</td></tr></table></fieldset></div>';
	return htmlstr;
}

//filename
function etfileExpStr(str,exp){
	var pos,len,ext;
	pos=str.lastIndexOf(exp)+1;
	len=str.length;
	ext=str.substring(pos,len);
	return ext;
}

function etfileReturnFilename(fileurl){
	var filename,str,exp;
	if(fileurl=='')
	{
		return '';
	}
	str=fileurl;
	if(str.indexOf("\\")>=0)
	{
		exp="\\";
	}
	else
	{
		exp="/";
	}
	filename=etfileExpStr(str,exp);
	return filename;
}

function etfileReturnFiletype(fileurl){
	var filetype;
	if(fileurl=='')
	{
		return '';
	}
	filetype=etfileExpStr(fileurl,'.');
	return filetype;
}


function EHEcmsEditorDoTranFile(str){
	var i;
	for(i=1;i<=10;i++)
	{
		if(CKEDITOR.dialog.getCurrent().getContentElement('etfileinfo1','etfileurl'+i).getValue()=='')
		{
			CKEDITOR.dialog.getCurrent().getContentElement('etfileinfo1','etfileurl'+i).setValue(str);
			break;
		}
	}
	CKEDITOR.dialog.getCurrent().selectPage('etfileinfo1');
}



