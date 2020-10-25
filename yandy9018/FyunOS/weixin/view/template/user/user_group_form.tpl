  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><a onclick="$('#form').submit();" class="btn btn-primary"><span><?php echo $button_save; ?></span></a> <a onclick="location = '<?php echo $cancel; ?>';" class="btn"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td><input type="text" name="name" value="<?php echo $name; ?>" />
              <?php if ($error_name) { ?>
              <span class="error"><?php echo $error_name; ?></span>
              <?php  } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_access; ?></td>
            <td><div class="scrollbox">
                <?php $class = 'odd'; ?>
                <?php foreach ($permissions as $permission) { ?>
                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div class="<?php echo $class; ?>">
                  <?php if (in_array($permission, $access)) { ?>
                  <input type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" checked="checked" />
                  <?php echo $permission; ?>
                  <?php } else { ?>
                  <input type="checkbox" name="permission[access][]" value="<?php echo $permission; ?>" />
                  <?php echo $permission; ?>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $entry_modify; ?></td>
            <td><div class="scrollbox">
                <?php $class = 'odd'; ?>
                <?php foreach ($permissions as $permission) { ?>
                <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div class="<?php echo $class; ?>">
                  <?php if (in_array($permission, $modify)) { ?>
                  <input type="checkbox" name="permission[modify][]" value="<?php echo $permission; ?>" checked="checked" />
                  <?php echo $permission; ?>
                  <?php } else { ?>
                  <input type="checkbox" name="permission[modify][]" value="<?php echo $permission; ?>" />
                  <?php echo $permission; ?>
                  <?php } ?>
                </div>
                <?php } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?php echo $text_unselect_all; ?></a></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
