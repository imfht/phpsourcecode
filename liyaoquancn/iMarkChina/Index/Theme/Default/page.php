<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
?>
<div class="job_content">
<dl id="sgd">
	<dt class="sgd"><?php Mark_Post_Title(); ?><b>Time: <?php Mark_The_Data(); ?>/<?php Mark_The_time();?></b></dt>
<dd>
<p><?php  Mark_The_Content(); ?></p>
</dd>
</dl>
By: <?php Mark_Nick_Name(); ?>&nbsp;&nbsp;&nbsp;<a href="#" class="cc cc_1" title=" 署名-非商业性使用-禁止演绎 3.0">&nbsp;</a>
</div>
<p><?php  if (Mark_Can_Comment()) { Mark_Comment_Code(); }else{echo '评论已关闭';} ?></p>
</div>
<div id="sidebar">
    <div class="search">
	<form method="post" action="<?php Mark_keyword(); ?>" id="searchform" >
    <input name="keyword" type="text" value="<?php Mark_Site_Name();?>" onfocus="this.value='';" onblur="if(this.value==''){this.value='<?php Mark_Site_Name();?>';}" />
    <input name="" type="submit" value="" class="so" onmouseout="this.className='so'" onmouseover="this.className='soHover'" />
	</form>
    </div>
    <h1>标签云</h1>
    <div class="classify">
	<ul>
	<div id="div1">
	<?php Mark_Index_Tag(); ?>
</div>
	</ul>
	</div>
	    <h1>时间云</h1>
    <div class="classify">
	<ul>
		<ul><?php Mark_Index_Date('<li>','</li>') ?></ul>	</ul>
	</div>
</div>
</div>
<?php include 'footer.php'; ?>