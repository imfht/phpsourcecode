<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
include 'header.php'; ?>
<body>
<?php include 'top.php'; ?>
<div id="container">
<div id="content">
        <?php
	if (Mark_Is_Post()){  
                include 'post.php';die; 
                    }elseif(Mark_Is_Page()){
                            include 'page.php';die;
                                }elseif(Mark_Is_Archive()){
                                    include 'archive.php';die; 
                                        }elseif (Mark_Is_Search()){ 
                                            include 'search.php';die;  
                                                }else{  
                                                    include 'tag.php';    
                                                        while (Mark_Next_Post()){
        ?><br />
    <div class="content_text">
    <div class="title">
    <i class="line_h"></i>
    <h3><?php Mark_The_Link(); ?></h3>
    <p>Tags: <?php Mark_The_Tags('', '', ''); ?> </p>
    <a class="up" href="<?php Mark_The_Url(); ?>" title="Hits: <?php Mark_The_Hits(); ?> "><?php Mark_The_Hits(); ?></a>
    </div>
    <div class="content_banner">    
    <p>       
    <div class="markimage"> 
                    <a href="<?php Mark_The_Url(); ?>" title="<?php Mark_The_Title(); ?>"><?php Mark_Images(); ?></a>
                            </div>
    <?php Mark_The_Des(); ?>...... </p>
    </div>
    <div class="toolBar">  <br />
    <ul>
    <li class="browse">Date: <?php Mark_The_Data(); ?></li>
    <li class="like">By: <?php Mark_Nick_Name(); ?></li>
    <li>
<div class="cc cc_1" title=" 署名-非商业性使用-禁止演绎 3.0">&nbsp;</div>
    </li>
    </ul>
    <a href="<?php Mark_The_Url(); ?>" class="more">阅读全文</a>    
    </div> 
     </div>
<?php }   ?>
	<div class="page">
	 <?php if (Mark_Has_New()) {  Mark_Goto_New('<'); }  Page_List(); if (Mark_Has_Old()) {  Mark_Goto_Old('>'); } } ?>
	<a href="#top" class="back_top"></a></div>    <!--Page End-->
</div>
<div id="sidebar">
    <div class="search">
	<form method="post" action="<?php Mark_keyword(); ?>" id="searchform" >
    <input name="keyword" type="text" value="<?php Mark_Site_Name();?>" onfocus="this.value='';" onblur="if(this.value==''){this.value='<?php Mark_Site_Name();?>';}" />
    <input name="" type="submit" value="" class="so" onmouseout="this.className='so'" onmouseover="this.className='soHover'" />
	</form>
    </div>
	    <h1>时间云</h1>
    <div class="classify">
	<ul>
		<ul><?php Mark_Index_Date('<li>','</li>'); ?></ul>	</ul>
	</div>
    <h1>标签云</h1>
    <div class="classify">
	<ul>
	<div id="div1">
	<?php Mark_Index_Tag(); ?>
</div>
	</ul>
	</div>
	<?php
                $fdlinks = $Mark_Config_Action['fdlinks'];
                if ($fdlinks == open) {
                 if ($Mark_Url_Action != ''){ echo "<!--------->"; 
                }else {
                 if ($Mark_Get_Type_Action == 'index') { ?>
    <h1>友情链接</h1>
    <div class="classify">
	<ul>
                <?php  Mark_Links('<li class="links_pic">','</li>'); ?>
</ul>
	</div>
                <?php  } } } ?>
    <div class="loginBar">
    <p><?php Root_Login(); ?></p>
    </div>
</div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>