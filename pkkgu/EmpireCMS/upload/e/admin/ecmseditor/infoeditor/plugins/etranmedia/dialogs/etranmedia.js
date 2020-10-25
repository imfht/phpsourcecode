(function() {  
    CKEDITOR.dialog.add("etranmedia",   
    function(ecmseditorv) {  
        return {  
            title: "插入视频",  
            minWidth: "500",  
            minHeight:"400",  
            contents: [{  
                id: "etmediainfo1",  
                label: "常规",  
                title: "常规",  
                expand: true,  
                width: "500px",  
                height: "400px",  
                padding: 0,  
                elements: [
					{type:"hbox",widths:["90%","10%"],align:"right",children:[
					{id:"etmediaurl",type:"text",label:"<strong>视频地址</strong> ",style:"width:100%;float:left;","default":""}
					,{type:"button",id:"browse",filebrowser:{action:"Browse",target:"etmediainfo1:etmediaurl",url:ecmseditorv.config.filebrowserFlashUploadUrl+'FileMain.php?'+ecmseditorv.config.filebrowserImageBrowseUrl+'&doecmspage=TranMedia&type=3&tranfrom=1&field=&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name},style:"display:inline-block;margin-top:14px;",hidden:!0,label:"选择"}]},
					
					{type:"hbox",widths:["25%","25%","25%","25%"],align:"right",children:[
					{id:"etmediawidth",type:"text",label:"宽度",style:"width:100%;float:left","default":"480"},
					{id:"etmediaheight",type:"text",label:"高度",style:"width:100%;float:left","default":"360"},
					
					{
						id: 'etmediatoplay',
						type: 'select',
						label: '播放器',
						items: [
							[ '自动识别', '0' ],
							[ 'HTML5Video', '5' ],
							[ 'HTML5Audio', '6' ],
							[ 'Media Player', '1' ],
							[ 'Real Player', '2' ],
							[ 'Flv Player', '3' ],
							[ 'Flash Player', '4' ]
						]
					},
					
					{
						id: 'etmediaplaymod',
						type: 'select',
						label: '播放模式',
						items: [
							[ '自动播放', '0' ],
							[ '手动播放', '1' ]
						]
					}
					]},
					
					{type:"html",id:"preview",style:"width:100%;",html:"<div><strong>"+CKEDITOR.tools.htmlEncode(ecmseditorv.lang.common.preview)+
"</strong>：<a onclick=etmediaview('cke_MediaPreviewBox_"+ecmseditorv.name+"');>[点击这里显示预览]</a><br><div id='cke_MediaPreviewBox_"+ecmseditorv.name+"' class='MediaPreviewBox'></div></div>"}
				
				]  
            },
			
{id:"Upload",label:ecmseditorv.lang.image.upload,elements:[	
	{ 
  	 	type: "html",
	 	style: "width:500;height:250",
	 	html: ' <IFRAME frameBorder="0" id="edtmedia'+ecmseditorv.name+'" name="edtmedia'+ecmseditorv.name+'" scrolling="auto" src="'+ecmseditorv.config.filebrowserFlashUploadUrl+'editorpage/ecmseditorpage.php?'+ecmseditorv.config.filebrowserFlashBrowseUrl+'&doecmspage=TranMedia&type=3&InstanceId='+ecmseditorv.id+'&InstanceName='+ecmseditorv.name+'&CKEditorFuncNum='+ecmseditorv._.filebrowserFn+'&langCode='+ecmseditorv.langCode+'" style="HEIGHT:250px;VISIBILITY:inherit;WIDTH:100%;Z-INDEX:2"></IFRAME>'
	}
]},
			
			
			],  
            onOk: function() {  
				var ehtmlstr='';
				var emediaurl=this.getValueOf('etmediainfo1','etmediaurl');
				var ewidth=this.getValueOf('etmediainfo1','etmediawidth');
				var eheight=this.getValueOf('etmediainfo1','etmediaheight');
				var etoplay=this.getValueOf('etmediainfo1','etmediatoplay');
				var eplaymod=this.getValueOf('etmediainfo1','etmediaplaymod');
				ehtmlstr=etmediaViewFile(emediaurl,ewidth,eheight,etoplay,eplaymod);
                //点击确定按钮后的操作    
				ecmseditorv.insertHtml(ehtmlstr);
				document.getElementById('cke_MediaPreviewBox_'+ecmseditorv.name).innerHTML='';
            },
			
			onCancel: function() {
				document.getElementById('cke_MediaPreviewBox_'+ecmseditorv.name).innerHTML='';
            }
			
        }  
    })  
})(); 


//预览
function etmediaview(viewid){	
	var ehtmlstr='';
	var emediaurl=CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediaurl').getValue();
	var ewidth=CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediawidth').getValue();
	var eheight=CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediaheight').getValue();
	var etoplay=CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediatoplay').getValue();
	var eplaymod=CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediaplaymod').getValue();
	ehtmlstr=etmediaViewFile(emediaurl,ewidth,eheight,etoplay,eplaymod);
	document.getElementById(viewid).innerHTML=ehtmlstr;
}

//返回播放器代码
function etmediaViewFileCode(toplay,width,height,autostart,furl){
	var fname='';
	var addauto='';
	if(autostart=="true")
	{
		addauto=' autoplay="autoplay"';
	}
	if(toplay==1)//media
	{
		imgstr="<object align=middle classid=\"CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95\" class=\"OBJECT\" id=\"MediaPlayer\" width=\""+width+"\" height=\""+height+"\"><PARAM NAME=\"AUTOSTART\" VALUE=\""+autostart+"\"><param name=\"ShowStatusBar\" value=\"-1\"><param name=\"Filename\" value=\""+furl+"\"><embed type=\"application/x-oleobject codebase=http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701\" flename=\"mp\" src=\""+furl+"\" width=\""+width+"\" height=\""+height+"\"></embed></object>";
	}
	else if(toplay==5)//html5video
	{
		imgstr='<video id="ecmsvideoid" width="'+width+'" height="'+height+'" controls="controls"'+addauto+'><source src="'+furl+'"></source> Your browser is not supported </video>';
	}
	else if(toplay==6)//html5audio
	{
		imgstr='<audio id="ecmsaudioid" src="'+furl+'" controls="controls"'+addauto+'> Your browser is not supported </audio>';
	}
	else if(toplay==3)//flv
	{
		imgstr="<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\""+width+"\" height=\""+height+"\"><param name=\"movie\" value=\"/e/data/images/flvplayer.swf?vcastr_file="+furl+"&vcastr_title="+fname+"&BarColor=0xFF6600&BarPosition=1&IsAutoPlay="+autostart+"\"><param name=\"quality\" value=\"high\"><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"/e/data/images/flvplayer.swf?vcastr_file="+furl+"&vcastr_title="+fname+"&BarColor=0xFF6600&BarPosition=1&IsAutoPlay="+autostart+"\" allowFullScreen=\"true\"  quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\""+width+"\" height=\""+height+"\"></embed></object>";
	}
	else if(toplay==4)//flash
	{
		imgstr="<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\""+width+"\" height=\""+height+"\"><param name=\"movie\" value=\""+furl+"\"><param name=\"quality\" value=\"high\"><embed src=\""+furl+"\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\""+width+"\" height=\""+height+"\"><param name=\"autoplay\" value=\""+autostart+"\" /></embed></object>";
	}
	else//reaplayer
	{
		imgstr="<object classid=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" HEIGHT=\""+height+"\" ID=\"Player\" WIDTH=\""+width+"\" VIEWASTEXT><param NAME=\"_ExtentX\" VALUE=\"12726\"><param NAME=\"_ExtentY\" VALUE=\"8520\"><param NAME=\"AUTOSTART\" VALUE=\""+autostart+"\"><param NAME=\"SHUFFLE\" VALUE=\"0\"><param NAME=\"PREFETCH\" VALUE=\"0\"><param NAME=\"NOLABELS\" VALUE=0><param NAME=CONTROLS VALUE=ImageWindow><param NAME=CONSOLE VALUE=_master><param NAME=LOOP VALUE=0><param NAME=NUMLOOP VALUE=0><param NAME=CENTER VALUE=0><param NAME=MAINTAINASPECT VALUE=\""+furl+"\"><param NAME=BACKGROUNDCOLOR VALUE=\"#000000\"></object><br><object CLASSID=\"clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA\" HEIGHT=32 ID=\"Player\" WIDTH=\""+width+"\" VIEWASTEXT><param NAME=_ExtentX VALUE=18256><param NAME=_ExtentY VALUE=794><param NAME=AUTOSTART VALUE=\""+autostart+"\"><param NAME=SHUFFLE VALUE=0><param NAME=PREFETCH VALUE=0><param NAME=NOLABELS VALUE=0><param NAME=CONTROLS VALUE=controlpanel><param NAME=CONSOLE VALUE=_master><param NAME=LOOP VALUE=0><param NAME=NUMLOOP VALUE=0><param NAME=CENTER VALUE=0><param NAME=MAINTAINASPECT VALUE=0><param NAME=BACKGROUNDCOLOR VALUE=\"#000000\"><param NAME=SRC VALUE=\""+furl+"\"></object>";
	}
	return imgstr;
}

//返回
function etmediaViewFile(furl,width,height,toplay,playmod){
	var imgstr="";
	var autostart;
	var mediatypes=",.wmv,.asf,.wma,.mp3,.asx,.mid,.midi,";
	var realtypes=",.rm,.ra,.rmvb,.mp4,.mov,.avi,.wav,.ram,.mpg,.mpeg,";
	var html5types=",.mp4,.ogg,.webm,";
	var html5audiotypes=",.mp3,.ogg,.wav,";
	var filetype;
	if(furl=='')
	{
		return '';
	}
	autostart="true";
	if(playmod==1)
	{
		autostart="false";
	}
	if(toplay==0)
	{
		filetype=etmediaToGetFiletype(furl);
		if(filetype=='.flv')
		{
			toplay=3;
		}
		else if(html5types.indexOf(','+filetype+',')!=-1)
		{
			toplay=5;
		}
		else if(html5audiotypes.indexOf(','+filetype+',')!=-1)
		{
			toplay=6;
		}
		else if(filetype=='.swf')
		{
			toplay=4;
		}
		else if(mediatypes.indexOf(','+filetype+',')!=-1)
		{
			toplay=1;
		}
		else
		{
			toplay=2;
		}
	}
	imgstr=etmediaViewFileCode(toplay,width,height,autostart,furl);
	return imgstr;
}

function etmediaToGetFiletype(sfile){
	var filetype,s;
	s=sfile.lastIndexOf(".");
	filetype=sfile.substring(s+1).toLowerCase();
	return '.'+filetype;
}


function EHEcmsEditorDoTranMedia(str){
	CKEDITOR.dialog.getCurrent().getContentElement('etmediainfo1','etmediaurl').setValue(str);
	CKEDITOR.dialog.getCurrent().selectPage('etmediainfo1');
}

