<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
?>
<div class="job_content">
<dl id="urei">
<dt class="urei">搜索 <b>Search</b></dt>
<dd>
	<?php Mark_Search_Search('<p>','</p>'); ?>
</dd>
</dl>
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
</div>
</div>
<?php include 'footer.php'; ?>