

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0, maximum-scale=1.0" />
    
    <script type='text/javascript' src="http://cdn.bootcss.com/markdown.js/0.6.0-beta1/markdown.min.js"></script>
    <script type="text/javascript" src="http://www.gonjay.com/editor/highlight.pack.js"></script>
	
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
       <!--link rel="stylesheet" href="css/style.css" /-->
        <link rel="stylesheet" href="/editor/css/editormd.css" />
    <style type="text/css">
    html,body{ margin:0; height:100%; }
   
    #body {
	height:80%;
        padding: 10px;
    }

    #form_ctr {
	height:100%;	
    }
    #flow {
	display:flex;
	height:100%;
	margin-top:20px;
    }
   
    
    img{
        max-width: 100%;
        height: auto;
    }

    #display  table{
        border-collapse: collapse;
        border-spacing: 0;
    }


    #display tr:nth-child(even) td, tr:nth-child(even) th {
        background: #eee;
    }
	
    #display {
        height: 100%;
    	max-height: 100%;
    	border: 1px solid #eee;
    	overflow-y: scroll;
    	padding: 10px;
    }
    #origin {
	height:100%;
	width:50%;
    }
    #display {
	height:100%;
	width:50%;
    }
    
    #push {
	margin-top:20px; 
    }
   
    </style>

</head>
<body>
	
    <div class="row" id="body">
	<div style="display:flex; margin-left:15%; margin-right:15%" > 
		<input type="text" class="form-control"  placeholder="标题" name="title" style="width:80%" id="title" <?php if(isset($title)) echo "value=\"".$title."\""; ?>>
		<select class="form-control" name='state' style="max-width:100px" id='state'>
			<option <?php if(isset($state) && $state == 'draft') echo 'selected="selected"'; ?>>draft</option>
			<option <?php if(isset($state) && $state == 'posted') echo 'selected="selected"'; ?>>posted</option>
		</select>
		<select class="multiselect" multiple="multiple" name="labels">
			<?php foreach($labels as $one){ ?>
 			 	<option value=<?php 
							echo '"'.$one['name'].'"'; 
							if(in_array($one['name'],$articlelabels))
								echo " selected"; 
						?> 
				><?php echo $one['name'] ?></option>
			<?php } ?>
		</select>
		<button  class="btn btn-success" onclick="saveOnline()">Submit</button> 
 
        </div>
	<div id="layout">
            <div id="test-editormd">
                <textarea style="display:none;" name="body" id="original"><?php if(isset($content)) echo $content; ?></textarea>
            </div>
        </div>
	<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
	<?php if(isset($id)){ ?>
		<input type="hidden" name="id" value="<?php echo $id; ?>">
  	<?php } ?>
   </div>
 
</body>
	<script src="/js/jquery.min.js"></script>
        <script src="/editor/editormd.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>	

    	<script src="//cdn.bootcss.com/showdown/1.2.2/showdown.min.js"></script>
    	<script type="text/javascript" src="/js/showdown-table.js"> </script>
 	<script type="text/javascript" src="/js/bootstrap-multiselect.js"></script>
    	<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>

    	<script type='text/javascript' src='/js/jquery.lazyload.js'></script>


    <script type='text/javascript'>
	$(function(){
		$("img").lazyload({
			plachold : 200,
			holder:'/img/grey.gif',
		});
	});
</script>
        <script type="text/javascript">
			var testEditor;
			var summaryEditor;
            $(function() {
                testEditor = editormd("test-editormd", {
                    width   : "90%",
                    height  : 640,
                    syncScrolling : "single",
                    path    : "/editor/lib/",
			htmlDecode      : true,       // 开启 HTML 标签解析，为了安全性，默认不开启
                        htmlDecode      : "style,script,iframe",  // you can filter tags decode
                        //toc             : false,
                        tocm            : true,    // Using [TOCM]
                        //tocContainer    : "#custom-toc-container", // 自定义 ToC 容器层
                        //gfm             : false,
                        //tocDropdown     : true,
                        // markdownSourceCode : true, // 是否保留 Markdown 源码，即是否删除保存源码的 Textarea 标签
                        emoji           : true,
                        taskList        : true,
                        tex             : true,  // 默认不解析
                        flowChart       : true,  // 默认不解析
                        sequenceDiagram : true,  // 默认不解析
			saveHTMLToTextarea:true,
                });
                            /*
                // or
                testEditor = editormd({
                    id      : "test-editormd",
                    width   : "90%",
                    height  : 640,
                    path    : "../lib/"
                });
                */
            });
        </script>

<script>
$(document).ready(function() {
    $('.multiselect').multiselect({
	includeSelectAllOption:true,
	selectAllValue: 'multiselect-all',
        enableCaseInsensitiveFiltering: true,
        enableFiltering: true,
        maxHeight: '300',
        buttonWidth: '235',
        onChange: function(element, checked) {
            var brands = $('#multiselect1 option:selected');
            var selection = [];
            $(brands).each(function(index, brand){
                selection.push(brand);
            });

            console.log(selection);
        }
    });
});

function encodeHTML(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
}
function convert(){

    	var html = $('#origin').val();
	var converter = new showdown.Converter({ extensions: ['table'] });
    	var msg = converter.makeHtml(html);
			
        $('#display').html(msg);
        $('code').each(function(i, e) {hljs.highlightBlock(e)});
}
/*
$('#origin').focus(function(){
    
	$(this).bind("keyup", function(){
		convert();	
        })
});
*/
$('#convert').click(convert);
$("textarea").keydown(function(e) {
    if(e.keyCode === 9) { // tab was pressed
        // get caret position/selection
        var start = this.selectionStart;
        var end = this.selectionEnd;

        var $this = $(this);
        var value = $this.val();

        // set textarea value to: text before caret + tab + text after caret
        $this.val(value.substring(0, start)
                    + "\t"
                    + value.substring(end));

        // put caret at right position again (add one for the tab)
        this.selectionStart = this.selectionEnd = start + 1;

        // prevent the focus lose
        e.preventDefault();
    }
});
/**
* @复制内容到剪贴板
*/
var clip = function(){
    if(window.clipboardData && window.clipboardData.setData){
        window.clipboardData.setData('text', "www.camnpr.com 郑州网建"); 
    }else{
        alert("您的浏览器不支持此复制功能，请使用Ctrl+C或鼠标右键。"); 
        $("#makecode").select(); // 选中要复制的内容，给用户提供方便
    }
}
$("#test-editormd").bind({
    dragover: function (ev) {
        $(this).addClass('hover');
        ev.preventDefault();
        return false;
    },
    dragend: function () {
        $(this).removeClass('hover');
        return false;
    },
    dragleave: function () {
        $(this).removeClass('hover');
        return false;
    },
    drop: function (ev) {
        ev.preventDefault();
        var file = ev.originalEvent.dataTransfer.files[0]; //获取拖入的第一个文件
        var reader = new FileReader();
	var textObj = $('#original');
        reader.onloadend = function () { //文件读取完成时

            $(ev.currentTarget).val($(ev.currentTarget).val() + "\n [ 正在为您上传 ]");
            $.ajax({
                url: '/upload/base64',
                type: 'POST',
                success: function(data){
                    var str = $(ev.currentTarget).val();

                    var img = '<img src="/pics/' + data + '">';
                    var href = '<a href="/pics/' + data + '">' + img + '</a>';
                    str = str.replace("[ 正在为您上传 ]", href);
		    window.prompt("Copy to clipboard: Ctrl+C, Enter", href);
                    $(ev.currentTarget).val(str);//插入图片代码
                },
                error: function(){
                    var str = $(ev.currentTarget).val();
                    str = str.replace("[ 正在为您上传 ]", "[ 图片上传失败 ]");//上传失败时
		    alert('upload failed!');
                    $(ev.currentTarget).val(str);
                },
                // Form data
                data:{
                    "imgstr":reader.result,
		    "_token":"<?php echo csrf_token() ?>"
                },
            });
        }

        reader.readAsDataURL(file);//读取文件
        return false;
    }
}); 

var url= window.location.href;
var artId = url.substring(url.lastIndexOf('/') + 1);

function getWordCount(word){
	word.replace(/[\x80-\xff]{1,3}/,' ch ');
	var arrWord = word.split(" ");
	return arrWord.length; 
}


function getSummaryFromString(text){
	var maxWord 	= 180;
	var maxLine 	= 6;
	var pieces 	= text.split("\n");	
	var lines	= pieces.length;
	var nowLine	= 1;
	var nowWord	= 0;
	var result 	= '';
 	while(nowLine <= lines && nowLine < maxLine && nowWord < maxWord){
                        result = result + pieces[nowLine - 1] + "\n";
                        nowWord +=  getWordCount(pieces[nowLine - 1]);
                        nowLine ++;
        }	
	return result;
}

function getSummary(){
	var oriHtml 	= testEditor.getHTML();	
	var oriMarkdown	= testEditor.getMarkdown();
	var summary	= getSummaryFromString(oriMarkdown);	

	var converter = new showdown.Converter();
	summary = converter.makeHtml(summary);

	summary = summary.replace(/img src/g,'img data-original');
	return {
		'summary':summary,
		'html':oriHtml,
	};
}
	
function saveFirstTime(){
	var htmls = getSummary();
	$.ajax({
             type: "POST",
             url: "/commit",
             data: {
		body	:testEditor.getMarkdown(),
		_token	:"<?php echo csrf_token() ?>",
		title	:$('#title').val(),
		state	:$('#state').val(),
		labels	:$('.multiselect').val(),
		bodyhtml:htmls['html'],
		summaryhtml:htmls['summary'],
	     },
             dataType: "json",
             success: function(data){
                        if(data != 0){
				alert("保存成功");
				artId = data.id;
                        }else{
				alert('保存失败');
                        }
                },
             error: function(data){
			alert('保存出错');
                }
         });

}

function saveOnline(){
	var htmls = getSummary();
	if(artId == 'publish'){
		saveFirstTime();
		return 0;
	}
        $.ajax({
             type: "POST",
             url: "/commit",
             data: {
		id	:artId,
		body	:testEditor.getMarkdown(),
		_token	:"<?php echo csrf_token() ?>",
		title	:$('#title').val(),
		state	:$('#state').val(),
		labels	:$('.multiselect').val(),
		bodyhtml:htmls['html'],
		summaryhtml:htmls['summary'],
	     },
             dataType: "json",
             success: function(data){
                        if(data != 0){
				alert('保存成功');
                        }else{
				alert('保存失败');
                        }
                },
             error: function(data){
			alert('保存出错');
                }
         });
}
$('#origin').scroll(function(){
	var preView = document.getElementById('display');
	var totalH = this.clientHeight;//整体高度
        var contentH = this.scrollHeight;//内容高度	

	var perc = this.scrollTop / (contentH - totalH);
	preView.scrollTop = ( preView.scrollHeight - preView.clientHeight ) * perc;
});
$(window).keydown(function(event) {
		//alert( event.ctrlKey + '  ' +  event.metaKey + '   ' + event.which );
		if ( event.ctrlKey && event.which == 83) {
			saveOnline();
			event.preventDefault();
			return false;			
		}else{
			return true; 
		}		
});
</script>

</html>
