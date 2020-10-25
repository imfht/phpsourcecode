<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
?>
<div class="job_content">
<dl id="ure">
	<dt class="ure">月份：<b>Month: </b></dt>
<dd>
<p><?php Mark_Date_List(); ?></p>
</dd>
</dl>
</div>
<div class="job_content">
<dl id="ued">
<dt class="hcid">标签 :<b>Tag: </b></dt>
<dd>
<p><p>	<?php Mark_Tag_List();?></p></p>
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
            <h1>热门</h1>
    <div class="classify">
    <ul>
    <?php Mark_Hot_Post('<li>','</li>'); ?>
    </ul>
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