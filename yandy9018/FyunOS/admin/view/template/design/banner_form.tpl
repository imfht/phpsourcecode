<script type="text/javascript" src="view/javascript/upload/ajaxfileupload.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $(".filePrew").live('change',function(e){
	 var a = $(this).parent().parent().find('.imagerow').val();
    ajaxFileUpload(a);
  });
});
  function ajaxFileUpload(imagerow)
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
        fileElementId:'fileToUpload'+ imagerow,
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
            $('#imageBox'+imagerow).html("<img src="+ data.msg+"!list>");
		    $('input[name=\'banner_image['+ imagerow +'][image]\']').val(data.msg);
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

  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="$('#form').submit();" class="btn btn-primary"><?php echo $button_save; ?></button> <button onclick="location = '<?php echo $cancel; ?>';" class="btn"><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td><input type="text" name="name" value="<?php echo $name; ?>" size="100" />
              <?php if ($error_name) { ?>
              <span class="error"><?php echo $error_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="status">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        </table>
        <table id="images" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_title; ?></td>
              <td class="left"><?php echo $entry_link; ?></td>
              <td class="left"><?php echo $entry_image; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $image_row = 0; ?>
          <?php foreach ($banner_images as $banner_image) { ?>
          <tbody id="image-row<?php echo $image_row; ?>">
            <tr>
              <td class="left"><?php foreach ($languages as $language) { ?>
                <input type="text" name="banner_image[<?php echo $image_row; ?>][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($banner_image['banner_image_description'][$language['language_id']]) ? $banner_image['banner_image_description'][$language['language_id']]['title'] : ''; ?>" />
                <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />
                <?php if (isset($error_banner_image[$image_row][$language['language_id']])) { ?>
                <span class="error"><?php echo $error_banner_image[$image_row][$language['language_id']]; ?></span>
                <?php } ?>
                <?php } ?></td>
              <td class="left"><input type="text" name="banner_image[<?php echo $image_row; ?>][link]" value="<?php echo $banner_image['link']; ?>" /></td>
              
            
                
                 <td><input type="hidden" name="banner_image[<?php echo $image_row; ?>][image]" value="<?php echo $banner_image['image']; ?>" id="image<?php echo $image_row; ?>" />
                 
<div id="loading" style="display:none;"></div>

 <input type="hidden" value="<?php echo $image_row; ?>" class="imagerow" />
 <a href="javascript:void(0);" class="btn_addPic"><div id="imageBox<?php echo $image_row; ?>"><img src="<?php echo $banner_image['preview']; ?>" alt="" id="preview" class="image" /></div><input type="file"  id="fileToUpload<?php echo $image_row; ?>" tabindex="3" title="支持jpg、jpeg、gif、png格式，文件小于1M" size="3" name="fileToUpload" class="filePrew"></a><br />
</td>
                
              <td class="left"><a onclick="$('#image-row<?php echo $image_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
            </tr>
          </tbody>
          <?php $image_row++; ?>
          <?php } ?>
          <tfoot>
              <tr>
              <td class="left"></td>
                <td class="left"></td>
                  <td class="left"><div id="cupload"></div></td>
                    <td class="left"></td>
             
            </tr>
            <tr>
              <td colspan="3"></td>
              <td class="left"><a onclick="addImage();" class="button"><span><?php echo $button_add_banner; ?></span></a></td>
            </tr>
          </tfoot>
        </table>
       
      </form>
    </div>
  </div>
                         <style>
			 a{text-decoration:none;}
.btn_addPic{
display: inline-block;
position: relative;
height: 100px;
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
width: 100px;
height: 100px;
cursor: pointer;
opacity: 0;
filter:alpha(opacity: 0);
}
</style>

<script type="text/javascript"><!--
var image_row = <?php echo $image_row; ?>;

function addImage() {
    html  = '<tbody id="image-row' + image_row + '">';
	html += '<tr>';
    html += '<td class="left">';
	<?php foreach ($languages as $language) { ?>
	html += '<input type="text" name="banner_image[' + image_row + '][banner_image_description][<?php echo $language['language_id']; ?>][title]" value="" /> <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /><br />';
    <?php } ?>
	html += '</td>';	
	html += '<td class="left"><input type="text" name="banner_image[' + image_row + '][link]" value="" /></td>';
html += '	              <td><input type=\"hidden\" name="banner_image[' + image_row + '][image]" value="" id="image' + image_row + '" />';
html += ' <div id=\"loading\" style=\"display:none;\"></div>';
html += '	              <input type=\"hidden\" value="'+ image_row +'" class="imagerow" />';
html += '	                  <a href=\"javascript:void(0);\" class=\"btn_addPic\"><div id="imageBox' + image_row + '"><img src=\"<?php echo $no_image; ?>\" alt=\"\" id=\"preview\" class=\"image\" /></div><input type=\"file\"  id="fileToUpload' + image_row + '" tabindex=\"3\" title=\"支持jpg、jpeg、gif、png格式，文件小于1M\" size=\"3\" name=\"fileToUpload\" class=\"filePrew\"></a><br />';

html += '                    </td>';
					
					
	html += '<td class="left"><a onclick="$(\'#image-row' + image_row  + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
	html += '</tr>';
	html += '            <div id=\"cupload\"></div>';
	html += '</tbody>'; 
	
	$('#images tfoot').before(html);
	
	image_row++;
}
//--></script>