
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
<div class="box">
  <div class="heading">
    <h2><?php echo $heading_title; ?></h2>
    <div class="buttons"><a onclick="$('#form').submit();" class="btn btn-primary"><span><?php echo $button_save; ?></span></a> <a onclick="location = '<?php echo $cancel; ?>';" class="btn"><span><?php echo $button_cancel; ?></span></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table id="module" class="list">
        <thead>
          <tr>
            <td class="left">IM类型</td>
            <td class="left">号码</td>
            <td class="left">文字</td>
            <td class="right">排序</td>
            <td></td>
          </tr>
        </thead>
        <?php $module_row = 0; ?>
     <?php foreach ($ims as $im) { ?>
        <tbody id="module-row<?php echo $module_row; ?>">
          <tr>
            <td class="left"><select name="ims[<?php echo $module_row; ?>][type]">
               <?php if ($im['type']=='msn') { ?>
               	 <option value="msn" selected="selected">MSN</option>
                <?php } else { ?>
                	 <option value="msn" >MSN</option>
                <?php } ?>
                <?php if ($im['type']=='qq') { ?>
                	<option value="qq" selected="selected">QQ</option>
                <?php } else { ?>
                	 <option value="qq">QQ</option>
                <?php } ?><?php if ($im['type']=='wangwang') { ?>
                	<option value="wangwang" selected="selected">阿里旺旺</option>
                <?php } else { ?>
                	 <option value="wangwang">阿里旺旺</option>
                <?php } ?>
                   <?php if ($im['type']=='skype') { ?>
                	<option value="skype" selected="selected">Skype</option>
                <?php } else { ?>
                	 <option value="skype">Skype</option>
                <?php } ?>
              
              </select></td>
            <td class="left">
            	<input type="text" name="ims[<?php echo $module_row; ?>][account]" value="<?php echo $im['account']; ?>" size="50" />
            </td>
            <td class="left">
            	<input type="text" name="ims[<?php echo $module_row; ?>][text]" value="<?php echo $im['text']; ?>" size="50" />
            </td>
            <td class="right"><input type="text" name="ims[<?php echo $module_row; ?>][sort_order]" value="<?php echo $im['sort_order']; ?>" size="3" /></td>
            <td class="left"><a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
          </tr>
        </tbody>
           <?php $module_row++; ?>
        <?php } ?>
     	 <tfoot>
          <tr>
            <td colspan="4"></td>
            <td class="left"><a onclick="addModule();" class="button"><span><?php echo $button_add_module; ?></span></a></td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</div>
<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="ims[' + module_row + '][type]">';
	html += '  		 <option value="msn">MSN</option>';
	html += '  		 <option value="qq">QQ</option>';
	html += '  		 <option value="wangwang">阿里旺旺</option>';
	html += '  		 <option value="skype">Skype</option>';
	html += '    </select></td>';
	html += '    <td class="left"><input type="text" name="ims[' + module_row + '][account]" value="" size="50" /></td>';
	html += '    <td class="left"><input type="text" name="ims[' + module_row + '][text]" value="" size="50" /></td>';
	html += '    <td class="right"><input type="text" name="ims[' + module_row + '][sort_order]" value="" size="3" /></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}
//--></script>