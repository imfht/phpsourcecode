<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');
?>
<?php
if($this->category_template)foreach ($this->category_template as $key => $value) {
    $template_id = 'template_'.$key;
?>
<div class="input-prepend input-append"> <span class="add-on"><?php echo $value[0];?>模板</span>
  <input type="text" name="template[<?php echo $key;?>]" class="span6" id="<?php echo $template_id;?>" value="<?php echo isset($rs['template'][$key])?$rs['template'][$key]:$value[1]; ?>"/>
  <?php echo filesAdmincp::modal_btn('模板',$template_id);?>
</div>
<div class="clearfloat mb10"></div>
<?php }?>
<div class="help-inline">
<span class="label label-info">{iTPL}</span>为系统设置的模板,自动匹配<br />
<span class="label label-info">{DEVICE}</span>为系统设置的设备,自动匹配,默认两个设备desktop、mobile,请分别制作两套模板
<hr />
<span class="label label-info">栏目首页模板</span> 每个栏目的首页模板(可制作用于频道封面、单页等)<br />
<span class="label label-info">栏目列表模板</span> 当栏目有分页且页号大于1时使用
</div>
