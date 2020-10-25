<script type="text/javascript" src="view/javascript/upload/ajaxfileupload.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#fileToUpload").live('change',function(){
    ajaxFileUpload();
  });
});
  function ajaxFileUpload()
  {
    $("#loading")
    .ajaxStart(function(){
		
      $('#cupload').html("<img src='http://fyunimage.b0.upaiyun.com/loading.gif'>");
    })
    .ajaxComplete(function(){
       $('#cupload').html('<b>图片上传云端完成</b>');

    });

    $.ajaxFileUpload
    (
      {
        url:'index.php?route=common/doajaxfileupload&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
        secureuri:false,
        fileElementId:'fileToUpload',
        dataType: 'json',
        data:{name:'logan', id:'id'},
        success: function (data, status)
        {
          if(typeof(data.error) != 'undefined')
          {
            if(data.error != '')
            {
              alert(data.error);
            }else
            {
            $('#imageBox').html("<img src="+ data.msg+"!home>");
		    $('input[id=\'image\']').val(data.msg+"!home");
            }
          }
        },
        error: function (data, status, e)
        {
          alert(e);
        }
      }
    )
    
    return false;

  }
  </script> 
  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title_store_setting; ?></h2>
      <div class="buttons"><button class="btn btn-primary" onclick="$('#form').submit();" ><?php echo $button_save; ?></button>   <button class="btn"  onclick="location = '<?php echo $cancel; ?>';" ><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?php echo $tab_general; ?></a><a href="#tab-store"><?php echo $tab_store; ?></a><a href="#tab-local"><?php echo $tab_local; ?></a><a href="#tab-option"><?php echo $tab_option; ?></a><a href="#tab-home">首页配置</a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <table class="form">
              <td><span class="required">*</span> <?php echo $entry_name; ?></td>
              <td><input type="text" name="config_name" value="<?php echo $config_name; ?>" size="40" />
                <?php if ($error_name) { ?>
                <span class="help-inline error"><?php echo $error_name; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_owner; ?></td>
              <td><input type="text" name="config_owner" value="<?php echo $config_owner; ?>" size="40" />
                <?php if ($error_owner) { ?>
                <span class="help-inline error"><?php echo $error_owner; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_address; ?></td>
              <td><textarea name="config_address" cols="40" rows="5"><?php echo $config_address; ?></textarea>
                <?php if ($error_address) { ?>
                <span class="help-inline error"><?php echo $error_address; ?></span>
                <?php } ?></td>
            </tr>
              <tr>
              <td>地图坐标</td>
              <td><input type="text" name="config_latlng" value="<?php echo $config_latlng; ?>" size="40" />
                </td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_email; ?></td>
              <td><input type="text" name="config_email" value="<?php echo $config_email; ?>" size="40" />
                <?php if ($error_email) { ?>
                <span class="help-inline error"><?php echo $error_email; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
              <td><input type="text" name="config_telephone" value="<?php echo $config_telephone; ?>" />
                <?php if ($error_telephone) { ?>
                <span class="help-inline error"><?php echo $error_telephone; ?></span>
                <?php } ?></td>
            </tr>
          </table>
        </div>
        <div id="tab-store">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?php echo $entry_title; ?></td>
              <td><input type="text" name="config_title" value="<?php echo $config_title; ?>" size="40" />
                <?php if ($error_title) { ?>
                <span class="help-inline error"><?php echo $error_title; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_meta_description; ?></td>
              <td><textarea name="config_meta_description" cols="40" rows="5"><?php echo $config_meta_description; ?></textarea></td>
            </tr>
             <tr>
	            <td><?php echo $entry_meta_keyword; ?></td>
	            <td><textarea name="config_meta_keyword" cols="40" rows="5"><?php echo $config_meta_keyword; ?></textarea></td>
            </tr>
            <tr>
              <td><?php echo $entry_template; ?></td>
              <td><select name="config_template" onchange="$('#template').load('index.php?route=setting/setting/template&token=<?php echo $token; ?>&template=' + encodeURIComponent(this.value));">
                  <?php foreach ($templates as $template) { ?>
                  <?php if ($template == $config_template) { ?>
                  <option value="<?php echo $template; ?>" selected="selected"><?php echo $template; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $template; ?>"><?php echo $template; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td></td>
              <td id="template"></td>
            </tr>
           
          </table>
        </div>
        <div id="tab-local">
          <table class="form">
            <tr>
              <td><?php echo $entry_country; ?></td>
              <td><select name="config_country_id" onchange="$('select[name=\'config_zone_id\']').load('index.php?route=setting/setting/zone&token=<?php echo $token; ?>&country_id=' + this.value + '&zone_id=<?php echo $config_zone_id; ?>');">
                  <?php foreach ($countries as $country) { ?>
                  <?php if ($country['country_id'] == $config_country_id) { ?>
                  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_zone; ?></td>
              <td><select name="config_zone_id">
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_language; ?></td>
              <td><select name="config_language">
                  <?php foreach ($languages as $language) { ?>
                  <?php if ($language['code'] == $config_language) { ?>
                  <option value="<?php echo $language['code']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $language['code']; ?>"><?php echo $language['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
         
            <tr>
              <td><?php echo $entry_currency; ?></td>
              <td><select name="config_currency">
                  <?php foreach ($currencies as $currency) { ?>
                  <?php if ($currency['code'] == $config_currency) { ?>
                  <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
          </table>
        </div>
        <div id="tab-option">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?php echo $entry_catalog_limit; ?></td>
              <td><input type="text" name="config_catalog_limit" value="<?php echo $config_catalog_limit; ?>" size="3" />
                <?php if ($error_catalog_limit) { ?>
                <span class="help-inline error"><?php echo $error_catalog_limit; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td> <?php echo $entry_seat; ?></td>
              <td><input type="text" name="config_seat" value="<?php echo $config_seat; ?>" size="3" />
                </td>
            </tr>
             <tr>
              <td> <?php echo $entry_hours; ?></td>
              <td><input type="text" name="config_hoursfrom" value="<?php echo $config_hoursfrom; ?>" size="1" /> - <input type="text" name="config_hoursto" value="<?php echo $config_hoursto; ?>" size="1" />
                </td>
                
              
            </tr>
             <tr>
              <td><?php echo $entry_print_notice; ?></td>
              <td><?php if ($config_sms_notice) { ?>
                <input type="radio" name="config_print_notice" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_print_notice" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_print_notice" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_print_notice" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_sms_notice; ?></td>
              <td><?php if ($config_sms_notice) { ?>
                <input type="radio" name="config_sms_notice" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_sms_notice" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_sms_notice" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_sms_notice" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            
             <tr>
              <td> <?php echo $entry_sms_notice_mobile; ?></td>
              <td><input type="text" name="config_sms_notice_mobile" value="<?php echo $config_sms_notice_mobile; ?>" size="3" />
                </td>
            </tr>
             <tr>
              <td><?php echo $entry_distribution; ?></td>
              <td><?php if ($config_distribution) { ?>
                <input type="radio" name="config_distribution" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_distribution" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_distribution" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_distribution" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            
            
            <tr>
              <td><?php echo $entry_tax; ?></td>
              <td><?php if ($config_tax) { ?>
                <input type="radio" name="config_tax" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_tax" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_tax" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_tax" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
          
            <tr>
              <td><?php echo $entry_customer_group; ?></td>
              <td><select name="config_customer_group_id">
                  <?php foreach ($customer_groups as $customer_group) { ?>
                  <?php if ($customer_group['customer_group_id'] == $config_customer_group_id) { ?>
                  <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            
            <tr>
              <td><?php echo $entry_customer_approval; ?></td>
              <td><?php if ($config_customer_approval) { ?>
                <input type="radio" name="config_customer_approval" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_customer_approval" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_customer_approval" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_customer_approval" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
          
            <tr>
              <td><?php echo $entry_account; ?></td>
              <td><select name="config_account_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($informations as $information) { ?>
                  <?php if ($information['information_id'] == $config_account_id) { ?>
                  <option value="<?php echo $information['information_id']; ?>" selected="selected"><?php echo $information['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $information['information_id']; ?>"><?php echo $information['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_checkout; ?></td>
              <td><select name="config_checkout_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($informations as $information) { ?>
                  <?php if ($information['information_id'] == $config_checkout_id) { ?>
                  <option value="<?php echo $information['information_id']; ?>" selected="selected"><?php echo $information['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $information['information_id']; ?>"><?php echo $information['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_stock_display; ?></td>
              <td><?php if ($config_stock_display) { ?>
                <input type="radio" name="config_stock_display" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_stock_display" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_stock_display" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_stock_display" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_stock_checkout; ?></td>
              <td><?php if ($config_stock_checkout) { ?>
                <input type="radio" name="config_stock_checkout" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_stock_checkout" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_stock_checkout" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_stock_checkout" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_order_status; ?></td>
              <td><select name="config_order_status_id">
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $config_order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
           
            
          </table>
           <input type="hidden" name="config_guest_checkout" value="0"/>
        </div>
         <div id="tab-home">
            <table class="form">
         <tr>
              <td>显示BANNER：</td>
              <td><?php if ($config_home_banner==1) { ?>
                <input type="radio" name="config_home_banner" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_banner" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_home_banner" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_banner" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            
              <tr>
              <td>显示搜索框：</td>
              <td><?php if ($config_home_search==1) { ?>
                <input type="radio" name="config_home_search" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_search" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_home_search" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_search" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
             <tr>
              <td>主题颜色：</td>
              <td> 
              
     <select id="seat" name="config_color_css">
     <option value="">请选择颜色</option>
    <?php foreach ($color_css as $i) { ?>
       <?php if ($i == $config_color_css) { ?>
        <option value="<?php echo $i; ?>" selected="selected"> <?php echo $i; ?></option>
         <?php }else { ?>
    <option value="<?php echo $i; ?>"> <?php echo $i; ?></option>
     <?php } ?>
    <?php } ?>
    </select>
    
    </td>
            </tr>
              <tr>
              <td>显示首页全屏图：</td>
              <td><?php if ($config_home_image_s==1) { ?>
                <input type="radio" name="config_home_image_s" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_image_s" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_home_image_s" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_home_image_s" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
                <style>
			 a{text-decoration:none;}
.btn_addPic{
display: inline-block;
position: relative;
overflow: hidden;
padding: 0 0px;
border: 1px solid #EBEBEB;
background: none repeat scroll 0 0 #F3F3F3;
color: #999999;
font: 14px/39px 'MicroSoft Yahei','Simhei';
cursor: pointer;
text-align: center;
}
.btn_addPic em {
background:url(add.png) 0 0;
display: inline-block;
width: 18px;
height: 18px;
overflow: hidden;
margin: 10px 5px 10px 0;
line-height: 20em;
vertical-align: middle;
}
.btn_addPic:hover em{
background-position:-19px 0;}
.filePrew {
display: block;
position: absolute;
top: 0;
left: 0;
width: 320px;
height: 550px;
cursor: pointer;
opacity: 0;
filter:alpha(opacity: 0);
}
</style>
	            <tr>
	              <td>首页全屏图:</td>
                    <div id="loading" style="display:none;"></div>
	              <td><input type="hidden" name="config_home_image" value="<?php echo $home_image; ?>" id="image" />
	                  <a href="javascript:void(0);" class="btn_addPic"><div id="imageBox"><img src="<?php echo $home_image; ?>" id="preview" class="image" /></div><input type="file" id="fileToUpload" tabindex="3" title="支持jpg、jpeg、gif、png格式，文件小于1M" size="3" name="fileToUpload" class="filePrew"></a><br />
            <div id="cupload"></div>

              <!-- <div id="cupload">
                <a href="javascript:void(0);" class="btn_addPic"><span><em>+</em>添加图片</span><input type="file" tabindex="3" title="支持jpg、jpeg、gif、png格式，文件小于5M" size="3" name="pic" class="filePrew"></a>
                <input id="fileToUpload" type="file" size="45" name="fileToUpload" value="<?php echo $image; ?>" class="input">
                 
               </div>-->
               
                    </td>
	            </tr>
                    </table>
         </div>
 </div>
</div>
<script type="text/javascript"><!--
$('#template').load('index.php?route=setting/setting/template&token=<?php echo $token; ?>&template=' + encodeURIComponent($('select[name=\'config_template\']').attr('value')));

$('select[name=\'config_zone_id\']').load('index.php?route=common/localisation/zone&token=<?php echo $token; ?>&country_id=<?php echo $config_country_id; ?>&zone_id=<?php echo $config_zone_id; ?>');
//--></script> 
<script type="text/javascript"><!--
function image_upload(field, preview) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&token=<?php echo $token; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?php echo $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&token=<?php echo $token; ?>',
					type: 'POST',
					data: 'image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + preview).replaceWith('<img src="' + data + '" alt="" id="' + preview + '" class="image" onclick="image_upload(\'' + field + '\', \'' + preview + '\');" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script> 
<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
