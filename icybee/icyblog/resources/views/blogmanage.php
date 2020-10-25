<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>icybee</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="/style.css" rel="stylesheet" type="text/css" />
<link href="/css/bootstrap-modal.css" rel="stylesheet" />
<link href="/css/animate.css" rel="stylesheet" />
<link href="http://getbootstrap.com/2.3.2/assets/css/bootstrap.css" rel="stylesheet" />
<!-- CuFon ends -->
</head>
<body>
<div id="static" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-body">
    <p>Do you Really want to delete this article?</p>
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
    <button type="button" data-dismiss="modal" class="btn btn-primary" onClick="ondelete()">Continue Task</button>
  </div>
</div>

<div id="deleteLabel" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-body">
    <p>Do you Really want to delete this label?</p>
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
    <button type="button" data-dismiss="modal" class="btn btn-primary" onClick="ondeletelabel()">Continue Task</button>
  </div>
</div>

<div id="addLabel" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-body">
    <p>Input the label name:</p>
	<input type="text" id="label_name" />
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
    <button type="button" data-dismiss="modal" class="btn btn-primary" onClick="addLabel()">Add</button>
  </div>
</div>

<div id="hits" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-body">
    <p>Operation successful!</p>
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-primary">Ok</button>
  </div>
</div>

<div id="fail" class="modal hide fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-body">
    <p>Operation Failed,please check the server log!</p>
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">Ok</button>
  </div>
</div>

<div class="main">
  <div class="header">
    <div class="header_resize">
      <div class="logo" style="display:flex">
        <h1><a href="/publish" target="_blank">Icybee<span>BMS</span></a></h1>
      </div>
      <div class="clr"></div>
      <div class="menu_nav">
        <ul>
          <!--li><a href="support.html">Support</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="blog.html">Blog</a></li>
          <li class="last"><a href="contact.html">Contact Us</a></li-->
        </ul>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  <div class="clr"></div>
  <div class="content">
    <div class="content_resize">
      <div class="mainbar">
<?php
	foreach($content as $article){
?>
        <div class="article" style="max-height=50px" id="<?php echo $article['id']; ?>">

          <h2><span><?php echo "[".$article['state']."]  ".$article['title']; ?></span></h2>
          <div class="clr"></div>
          <p>Posted on <?php echo $article['created_at']; ?></p>
          <!--img src="images/img_1.jpg" width="613" height="193" alt="image" /-->
          <!--div class="clr"></div>
          <p>This is a free CSS website template by CoolWebTemplates. This 
work is distributed under the Creative Commons Attribution 3.0 License, 
which means that you are free to use it for any personal or commercial purpose provided you credit me in the form of a link back to <a href="http://www.cssmoban.com/" title="ģ°å¼Ò>ģ°å¼Ò/a>.</p>
          <p>Maecenas dignissim mauris in arcu congue tincidunt. Vestibulum elit  nunc, accumsan vitae faucibus vel, scelerisque a quam. Aenean at metus id elit bibendum faucibus. Curabitur ultrices ante nec neque consectetur a aliquet libero lobortis. Ut nibh sem, pellentesque in dictum eu, convallis blandit erat. Cras vehicula tellus nec purus sagittis id scelerisque risus congue. Quisque sed semper massa. Donec id lacus mauris, vitae pretium risus. Fusce sed tempor erat. </p-->
          <p>
		<a href="/article?id=<?php echo $article['id']; ?>" target="_blank">Read more </a>
		<a href="#static" data-toggle="modal" style="margin-left:20px" onClick="toDelete=<?php echo $article['id'] ?>">Delete this shit </a>
		<a href="/publish/<?php echo $article['id']; ?>" style="margin-left:20px" target="_blank" >Edit </a>
	</p>
        </div>
<?php
	}
?>
      <div class="article" style=" background:none; border:0;">
          <p>Page <?php echo $currentpage." of ".$pages;?> <span class="butons">
	<?php if($currentpage < $pages){ ?>
		<a href="/blogadmin/<?php echo $currentpage + 1 ?>">older posts</a>
	<?php }if($currentpage > 1){ ?>
		<a href="/blogadmin/<?php echo $currentpage - 1 ?>">newer posts</a>
	<?php } ?>

	</span></p>
      </div>
      </div>
      <div class="sidebar">
        <div class="gadget">
          <h2>Actions</h2>
          <div class="clr"></div>
    	<li><a href="/publish" target="_blank">Publish an article!</a></li>
        <li><a href="/auth/logout" >登出</a></li>
         
          <ul class="sb_menu">
            <!--li><a href="#">Home</a></li-->
         </ul>
        </div>
        <div class="gadget">
          <h2><span>标签</span></h2>
          <div class="clr"></div>
		<?php foreach($labels as $label){ ?>
	   <li><a href="#deletelabel" title="<?php echo $label['name'] ?>"><?php echo $label['name'] ?></a><a href="#deleteLabel"  data-toggle="modal" style="margin-left:20px" title="<?php echo $label['name'] ?>" onClick= "labelname='<?php echo $label['name'] ?>'" >delete</a><br />
           	<?php } ?>
	<li><a href="#addLabel" data-toggle="modal" title="Website Templates">添加一个标签</a><br />
             
         <!--ul class="ex_menu">
            <li><a href="#" title="Website Templates">DreamTemplate</a><br />
              Over 6,000+ Premium Web Templates</li>
            <li><a href="#" title="WordPress Themes">TemplateSOLD</a><br />
              Premium WordPress &amp; Joomla Themes</li>
            <li><a href="#" title="Affordable Hosting">ImHosted.com</a><br />
              Affordable Web Hosting Provider</li>
            <li><a href="#" title="Stock Icons">MyVectorStore</a><br />
              Royalty Free Stock Icons</li>
            <li><a href="#" title="Website Builder">Evrsoft</a><br />
              Website Builder Software &amp; Tools</li>
            <li><a href="#" title="CSS Templates">CSS Hub</a><br />
              Premium CSS Templates</li>
          </ul-->
        </div>
        <div class="gadget">
          <h2>Cache Status</h2>
          <div class="clr"></div>
		page hit:miss <?php echo $pageHitCount['hit'].":".$pageHitCount['miss']."  hitrate:".$pageHitCount['hitRate']."%"; ?> </br>
		article hit:miss <?php echo $articleHitCount['hit'].":".$articleHitCount['miss']."  hitrate:".$articleHitCount['hitRate']."%"; ?> </br>
          </div>
      </div>
      <div class="clr"></div>
    </div>
  </div>
  <div class="fbg">
    <div class="fbg_resize">
    <div class="footer">
      <div class="clr"></div>
    </div>
  </div>
</div>
<div style="display:none"><script src='http://v7.cnzz.com/stat.php?id=155540&web_id=155540' language='JavaScript' charset='gb2312'></script></div>

<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="/js/cufon-yui.js"></script>
<script type="text/javascript" src="/js/arial.js"></script>
<script type="text/javascript" src="/js/cuf_run.js"></script>
<!-- 新 Bootstrap 核心 CSS 文件 -->
<script src="/js/bootstrap-modalmanager.js"></script>
<script src="/js/bootstrap-modal.js"></script>


<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript">
var toDelete = 0;
var labelname = 'null'; 
function addLabel(){
	 $.ajax({
             type: "GET",
             url: "/blogadmin/addLabel/" + $('#label_name').val(),
             dataType: "json",
             success: function(data){
			$('#hits').modal('show');
                },
	     error: function(data){
		$('#fail').modal('show');
 		}
         });

}

function ondeletelabel(){
	 $.ajax({
             type: "GET",
             url: "/blogadmin/deleteLabel/" + labelname,
             dataType: "json",
             success: function(data){
			$('#hits').modal('show');
                },
	     error: function(data){
		$('#fail').modal('show');
 		}
         });

}

function ondelete(){
	 $.ajax({
             type: "GET",
             url: "/blogadmin/delete",
             data: {id:toDelete},
             dataType: "json",
             success: function(data){
			if(data == 1){
				$('#' + toDelete).remove();	
				$('#hits').modal('show');
			}else{
				$('#fail').modal('show');
			}
                },
	     error: function(data){
		$('#fail').modal('show');
 		}
         });
}
</script>
</body>
</html>

