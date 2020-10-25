<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
 if ($Mark_Get_Type_Action == 'date' || $Mark_Get_Type_Action =='tag') { ?>
<div class="job_content">
<dl id="ure">
<?php if (Mark_Is_Tag()) { ?>
	<dt class="ure">与 <font color="#000"><strong><?php Mark_Tag_Name(); ?> </strong></font><b>相关的日志:</b></dt>
				<?php } elseif (Mark_Is_Date()) { ?>
				<dt class="ure"><font color="#000"><strong><?php Mark_Date_Name(); ?>
					</strong></font><b>发表的全部日志: </b></dt>
<?php  } ?>
<dd>
</dd>
</dl>
</div>
<?php } ?>