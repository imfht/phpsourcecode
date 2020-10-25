<link rel="stylesheet" href="../inc/css/CodeMirror/codemirror.css" />
<link rel="stylesheet" href="../inc/css/CodeMirror/night.css"/>
<link rel="stylesheet" href="../inc/css/CodeMirror/editSkin.css" />
<link rel="stylesheet" href="../inc/css/CodeMirror/codeEditor.css" />
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system">操作员后台</a> → <a href="./index.php?m=system&s=changeskin">模板管理</a> → <a href="./index.php?m=system&s=changeskin&a=editSkin&skin=<?php echo $request['skin'];?>">模板编辑</a></div>
<!-- 导航 -->
<div id="daohang">
<div class="templatename">当前模板：<?php echo $request['skin']; ?></div>
</div>
<div id="editor-main">
	<div id="editor-main-left">
		<!-- 目录树 -->
	    <div id="editor-resource-tree" >
			<?php echo $skinTreeHtml;?>
		</div>
	</div>
	<div id="editor-main-middle">
		<div id="editor-resource-selected">
			<a href="/路径" title="/路径"></a>
		</div>
		<!-- 这里编辑内容 -->
		<div id="editor-resource-edit">
			<input    id="fileName"  name="fileName"  type="hidden" value=""/>
	    	<textarea id="fileCode" name="fileCode" class="filecode" >
	    	
	    	
	    	
	    	
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //
            //                                    欢迎使用在线代码编辑器codeEditor  
            //
            //            文件操作 :
            //                创建/删除/重命名 文件夹;
            //
            //                创建/删除/重命名 文件（.php、.js、.css、.xml）;
            //
            //                上传/重命名 图片（.gif、.png、.jpg）和.flv视频;
            //
            //            快捷键: 
            //                保存：Ctrl+s
            //
            //                撤销：Ctrl+z
            //
            //                全屏切换：F11
            //
            //            codeEditor是基于codeMirror的应用，在此非常感谢codeMirror作者Marijn Haverbeke.
            //
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            
            
            
            </textarea>
	    </div>
    </div>
	<div id="editor-main-right">
		 <!-- 在线文档 -->
		<div id="helper">
			<span>标签在线帮助</span>
		    <ul>
		    	<li><a href="http://www.doccms.com/faq/index.html" target="_blank">index.php首页标签</a></li>
		        <li><a href="http://www.doccms.com/faq/common.html" target="_blank">common.php通用页标签</a></li>
		        <li><a href="http://www.doccms.com/faq/seolabel/index.html" target="_blank">模板标题、SEO标签</a></li>
                <li><a href="http://www.doccms.com/faq/navlabel/index.html" target="_blank">模板导航、子导航标签</a></li>
                <li><a href="http://www.doccms.com/faq/doclabel/index.html" target="_blank">模块应用、调用标签</a></li>
                <li><a href="http://www.doccms.com/faq/syslabel/index.html" target="_blank">系统应用、功能标签</a></li>
                <li><a href="http://www.doccms.com/faq/taglabel/index.html" target="_blank">系统路径、常量输出标签</a></li>
		    </ul>
		    <a href="http://www.doccms.com/" target="_blank" class="more">了解更多>></a>
            
		</div>
        <div id="helper">
			<span>友情提示</span>
            <ul>
		    	<li><a href="#"> 编辑后可按键盘Ctrl+S来保存编辑内容。</a></li>
		    </ul>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<div class="contextMenu" id="rightMenu"></div>
<div id="createUI"></div>
<script src="../inc/js/jquery.form.js"></script>
<script src="../inc/js/CodeMirror/lib/codemirror.js" ></script>
<script src="../inc/js/CodeMirror/mode/xml/xml.js"></script>
<script src="../inc/js/CodeMirror/mode/javascript/javascript.js"></script>
<script src="../inc/js/CodeMirror/mode/css/css.js"></script>
<script src="../inc/js/CodeMirror/mode/clike/clike.js"></script>
<script src="../inc/js/CodeMirror/mode/php/php.js"></script>
<script src="../inc/js/s_changeskin/jquery.contextmenu.r2.js"></script>
<script src="../inc/js/s_changeskin/codeEditorHelper.js"></script>
<script src="../inc/js/s_changeskin/codeEditorServe.js"></script>
<script src="../inc/js/s_changeskin/codeEditorUI.js"></script>
<script src="../inc/js/s_changeskin/codeEditorInit.js"></script>
<script src="../inc/js/s_changeskin/codeEditorOtherInit.js"></script>
<script src="../inc/js/s_changeskin/codeEditorPopup.js"></script>