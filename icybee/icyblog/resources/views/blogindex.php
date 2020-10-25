<!DOCTYPE html>
<!-- saved from url=(0028)http:////page/3/ -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="http://gmpg.org/xfn/11" rel="profile">
  

  
  <meta name="viewport" content="width=device-width">

  <title>
    
      IcyBee · Hacking the world. 
    
  </title>

  
  <link rel="stylesheet" href="/css/poole.css">
  <link rel="stylesheet" href="/css/syntax.css">
  <link rel="stylesheet" href="/css/lanyon.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <!--link rel="stylesheet" href="/css/family.css"-->


    <!--script type='text/javascript' src="http://cdn.bootcss.com/markdown.js/0.6.0-beta1/markdown.min.js"></script>
    <script type="text/javascript" src="http://www.gonjay.com/editor/highlight.pack.js"></script>
    <!--script src="//cdn.bootcss.com/showdown/1.2.2/showdown.min.js"></script-->
    <!--script src="//cdn.bootcss.com/showdown/1.2.2/showdown.min.js"></script-->
    <!--script type="text/javascript" src="/js/showdown-table.js"> </script-->
    <link rel="stylesheet" href="/editor/css/editormd.css" />
 
  <link rel="alternate" type="application/rss+xml" title="RSS" href="http:////index.xml">

  
  <link rel="canonical" href="http:////">
  
<style type="text/css">#yddContainer{display:block;font-family:Microsoft YaHei;position:relative;width:100%;height:100%;top:-4px;left:-4px;font-size:12px;border:1px solid}#yddTop{display:block;height:22px}#yddTopBorderlr{display:block;position:static;height:17px;padding:2px 28px;line-height:17px;font-size:12px;color:#5079bb;font-weight:bold;border-style:none solid;border-width:1px}#yddTopBorderlr .ydd-sp{position:absolute;top:2px;height:0;overflow:hidden}.ydd-icon{left:5px;width:17px;padding:0px 0px 0px 0px;padding-top:17px;background-position:-16px -44px}.ydd-close{right:5px;width:16px;padding-top:16px;background-position:left -44px}#yddKeyTitle{float:left;text-decoration:none}#yddMiddle{display:block;margin-bottom:10px}.ydd-tabs{display:block;margin:5px 0;padding:0 5px;height:18px;border-bottom:1px solid}.ydd-tab{display:block;float:left;height:18px;margin:0 5px -1px 0;padding:0 4px;line-height:18px;border:1px solid;border-bottom:none}.ydd-trans-container{display:block;line-height:160%}.ydd-trans-container a{text-decoration:none;}#yddBottom{position:absolute;bottom:0;left:0;width:100%;height:22px;line-height:22px;overflow:hidden;background-position:left -22px}.ydd-padding010{padding:0 10px}#yddWrapper{color:#252525;z-index:10001;background:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ab20.png);}#yddContainer{background:#fff;border-color:#4b7598}#yddTopBorderlr{border-color:#f0f8fc}#yddWrapper .ydd-sp{background-image:url(chrome-extension://eopjamdnofihpioajgfdikhhbobonhbb/ydd-sprite.png)}#yddWrapper a,#yddWrapper a:hover,#yddWrapper a:visited{color:#50799b}#yddWrapper .ydd-tabs{color:#959595}.ydd-tabs,.ydd-tab{background:#fff;border-color:#d5e7f3}#yddBottom{color:#363636}#yddWrapper{min-width:250px;max-width:400px;}</style>

<style type="text/css">
.contentblog{
	font-family: "Microsoft YaHei", Helvetica, "Meiryo UI", "Malgun Gothic", "Segoe UI", "Trebuchet MS", "Monaco", monospace, Tahoma, STXihei, "华文细黑", STHeiti, "Helvetica Neue", "Droid Sans", "wenquanyi micro hei", FreeSans, Arimo, Arial, SimSun, "宋体", Heiti, "黑体", sans-serif;
}
</style>
</head>
<!--link rel="icon" href="/favicon.ico" mce_href="/favicon.ico" type="image/x-icon"l-->

  <body class="layout-reverse sidebar-overlay" style="zoom: 100%;">

    
<input type="checkbox" class="sidebar-checkbox" id="sidebar-checkbox">


<div class="sidebar" id="sidebar">
  <div class="sidebar-item">
	  <p>IcyBee · Hacking the world!</p>
  </div>

  <nav class="sidebar-nav">
    <a class="sidebar-nav-item" href="/">Home</a>
    <a class="sidebar-nav-item" href="/">Posts</a>
<?php foreach($labels as $one){ ?>
    <a class="sidebar-nav-item" style="margin-left:10%" href="/label/<?php echo $one['name']; ?>"><?php echo $one['name']; ?></a>
<?php } ?>
        <!--a class="sidebar-nav-item" href="http:////about/">About</a>
    
        <a class="sidebar-nav-item" href="http:////essential-books/">Essential Books</a>
    
        <a class="sidebar-nav-item" href="http:////rx-toolkit/">Regex Toolkit</a>
    
        <a class="sidebar-nav-item" href="http:////blog/2013/12/18/secure-your-accounts-and-devices/">Staying Secure Online</a>
    
        <a class="sidebar-nav-item" href="http:////blog/2013/07/10/ultimate-notebook-and-journal-face-off/">The Ultimate Notebook</a>
    
        <a class="sidebar-nav-item" href="http:////blog/2015/03/07/ultimate-pen-marker-face-off/">The Ultimate Pen</a-->
    
  </nav>

  <div class="sidebar-item">
	  <p>© 2015 Bupt::icybee
	  Contact: icybee@yeah.net <!--a href="/">Twitter</a>,
	  <a href="https://linkedin.com/in/xaprb">LinkedIn</a-->
	      <br>Powered by <a href="http://laravel.com/" target="_blank">Laravel</a></p>
  </div>
</div>


    
    <div class="wrap">
      <div class="masthead">
        <div class="container">
          <h3 class="masthead-title">
            <a href="/" title="Home">IcyBee</a>
          </h3>
        </div>
      </div>

      <div class="container content">
<?php
	foreach($content as $article){
		if(strpos($article['title'],' ') || strpos($article['title'],'?')){
			$article['htmldata'] = $article['id'];
		}else{
			$article['htmldata'] = $article['title'];
		}
?>
 

<div class="posts">
	
      <div class="post">
        <h1 class="post-title" style="font-family:Microsoft YaHei;"><a href="/article/<?php echo $article['htmldata'].'.html'; ?>"><?php echo$article['title']; ?></a></h1>
        <span class="post-date"><!--Mon, Apr 28, 2014--><?php echo $article['time']; ?></span>
        <!--p>I was listening to a conversation recently and heard an experienced engineer express an interesting point of view on joins and key-value databases. I don’t entirely agree with it. Here’s why.</p-->
	<p>
	<div class="contentblog" id="<?php echo $article['id']; ?>" style="min-width=100%;font-size:1.1em;color: #515151;padding:0px;">
<?php if(!empty($article['summaryhtml']))echo $article['summaryhtml']; ?>
</div>
	</p>
		  <p style="text-align:right"><a href="/article/<?php echo $article['htmldata'].'.html'; ?>"> » Continue Reading (about <?php echo $article['words'] ?> words)</a></p>
      </div>
 
</div>
<?php
	}
?>
 

<div class="pagination">
  <?php if($currentpage < $pages){ ?>

    <a class="pagination-item older" href="<?php if(!empty($label)){ echo "/label/".$label;}echo '/page/'.($currentpage + 1); ?>">Older</a>
  
  <?php }else{ ?>

    <span class="pagination-item newer">Older</span>
  <?php } ?>

  <?php if($currentpage > 1){ ?>

    <a class="pagination-item newer" href="<?php if(!empty($label)){ echo "/label/".$label;}echo '/page/'.($currentpage - 1); ?>">Newer</a>
  <?php }else{ ?>
    <span class="pagination-item newer">Newer</span>
  
  <?php } ?>


</div>



      </div>
    </div>

    <label for="sidebar-checkbox" class="sidebar-toggle"></label>


  


<div id="st-main-container" style="display: none;">   <div class="swiftype">   <a class="close" data-dismiss="st-modal" href="http:////page/3/#">×</a>   <div class="st-search-bar st-only-input">     <form>       <div class="st-input-wrapper">         <div class="st-input-inner">           <input type="text" value="" id="st-overlay-search-input" placeholder="search this website" autocomplete="off" autocorrect="off" autocapitalize="off" style="outline: none;">           <span class="st-input-icon"></span>           <a href="https://swiftype.com/?ref=pbo" class="st-input-powered-by st-powered-by" target="_blank"></a>         </div>         <input type="submit" value="search" id="submitbutton">       </div>     </form>   </div>   <div class="st-result-wrapper" style="min-height: 478px;">     <div class="st-result-listing">       <div class="st-results">       </div>       <div class="st-indexing-notice" style="display: none;">         This site is still being indexed. Please try your search again in a few minutes.       </div>       <div class="st-logo-footer st-powered-by">         <a href="http://swiftype.com/?ref=pbo" target="_blank">search by swiftype</a>       </div>     </div>   </div> </div> </div><div class="swiftype-widget"><div class="autocomplete" style="position: absolute; z-index: 199999; width: 210px; top: 1000px; left: -8px; display: none;"><ul></ul></div></div></body>

  
    <script type='text/javascript' src='/js/jquery.min.js'></script>
    <script type='text/javascript' src='/js/prettify.js'></script>
    <script type='text/javascript' src='/js/jquery.lazyload.js'></script>

        <script src="/editor/lib/marked.min.js"></script>
        

        <script src="/editor/editormd.js"></script>


    <script type='text/javascript'>
	$(function(){
		$("img").lazyload({
			effect : "fadeIn",
			failure_limit : 10,
			plachold : 800,
			holder:'/img/grey.gif',
		});
	});
</script>
 
<script type="text/javascript">

function getSummary(id){
if($("#" + id).text() != "\n"){
	return 0;
}
$.ajax({
		data:"name="+name,       //Ҫ·¢ËµÄýpe:"GET",           //·¢Ëµķ½ʽ
		url:"articleSummary?id=" + id, //urlµØ·
		error:function(msg){ //´¦À³öÄÅ¢
		alert("error " + msg);
	},
		success:function(msg){  //´¦ÀÕȷʱµÄÅ¢
			//msg = marked(msg);
			
			//var converter = new showdown.Converter({ extensions: ['table'] });
    			//msg = converter.makeHtml(msg);
		    editormd.markdownToHTML(id, {
                        markdown        : msg ,//+ "\r\n" + $("#append-test").text(),
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
                        //flowChart       : true,  // 默认不解析
                        //sequenceDiagram : true,  // 默认不解析
                    });

			
		}
	});

}
$(function(){
var ids=[<?php foreach($content as $one)echo $one['id'].","; ?>];
for(i in ids){
id = ids[i];
getSummary(id);
}
});

</script>

</html>
