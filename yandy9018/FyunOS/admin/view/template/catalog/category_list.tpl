<?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
   	<div class="btn-toolbar" >
	 	<div class="btn-group">
          <button class="btn btn-small dropdown-toggle" data-toggle="dropdown"><?php echo $button_batch;?> <span class="caret"></span></button>
          <ul class="dropdown-menu">
            <li><a onclick="$('#form').submit();"  ><?php echo $button_delete; ?></a></li>
            <li><a onclick="$('#form').attr('action', '<?php echo $enabled; ?>'); $('#form').submit();"><?php echo $button_enable;?></a></li>
            <li><a onclick="$('#form').attr('action', '<?php echo $disabled; ?>'); $('#form').submit();"><?php echo $button_disable;?></a></li>
          </ul>
        </div>
        <div class="buttons" >
	 		<input type="button" onclick="location = '<?php echo $insert; ?>'" class="btn btn-primary" value="<?php echo $button_insert; ?>">
		 </div>
	 </div>
	</div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
       <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?php echo $column_name; ?></td>
              <td class="left"><?php echo $column_status; ?></td>
              <td class="left"><?php echo $column_sort_order; ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($categories) { ?>
            <?php foreach ($categories as $category) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($category['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $category['name']; ?></td>
           
              <td class="left"><?php echo $category['status']; ?></td>
              <td class="left"><?php echo $category['sort_order']; ?></td>
              <td class="right"><?php foreach ($category['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="5"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
    </div>
  </div>
