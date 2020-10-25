<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
Mark_Hits();
?>
    <div class="content_text2">
    <div class="title">
    <i class="line_h"></i>
    <h3><?php Mark_Post_Title(); ?></h3>
    <p>Tags: <?php Mark_The_Tags('', '', ''); ?>&nbsp;</p>
<a class="up" href="<?php Mark_The_Url(); ?>" title="Hits: <?php Mark_The_Hits(); ?> "><?php Mark_The_Hits(); ?></a>
    </div>
    <div class="content_banner">
    <div class="text">
<p><?php Mark_The_Content(); ?></p>
</div>
	<a href="#" class="cc cc_1" title=" 署名-非商业性使用-禁止演绎 3.0">&nbsp;</a>
	日志固定链接: <a href="<?php Mark_The_Url(); ?>"><?php Mark_The_Url(); ?></a>
    <div class="appendInfo">
    <ul>
    <li class="come_from">(<span>本文出自 <a href="<?php Mark_Website_Url(); ?>"><?php Mark_Site_Name();?> </a>，转载时请注明出处</span>)</li>
    <li class="add_like">Time: <?php Mark_The_Data(); ?>/<?php Mark_The_time();?></li>
    <li class="share" onmouseover="share_more(8118,this);"><a href="javascript:;">By: <?php Mark_Nick_Name(); ?></a></li>
    </ul>
    </div>
    </div>
	<div id="ds-ssr">
 <ol id="commentlist">
                <?php  if (Mark_Can_Comment()) { Mark_Comment_Code(); }else{echo '评论已关闭';} ?>
	  </ol>
  </div> 
   </div>
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
		<ul><div id="div1"><?php Mark_Index_Date('',''); ?></div></ul>	</ul>
	</div>
        <h1>热门</h1>
    <div class="classify">
    <ul>
    <?php Mark_Hot_Post('<li>','</li>'); ?>
    </ul>
    </div>
    <h1>新的</h1>
    <div class="classify">
	<ul>
	<?php Post_Links('<li>','</li>'); ?>
	</ul>
	</div>

</div>
</div>
<?php include 'footer.php'; ?>