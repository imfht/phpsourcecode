  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">   
   <div class="heading">
      <h2> <?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="$('#form').submit();" class="btn btn-primary"  ><?php echo $button_save; ?></button> <button onclick="location = '<?php echo $cancel; ?>';" class="btn"><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td><input type="text" name="name" value="<?php echo $name; ?>" />
              <?php if ($error_name) { ?>
              <span class="error"><?php echo $error_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_code; ?></td>
            <td><input type="text" name="code" value="<?php echo $code; ?>" />
              <?php if ($error_code) { ?>
              <span class="error"><?php echo $error_code; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_locale; ?></td>
            <td><input type="text" name="locale" value="<?php echo $locale; ?>" />
              <?php if ($error_locale) { ?>
              <span class="error"><?php echo $error_locale; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_image; ?></td>
            <td><input type="text" name="image" value="<?php echo $image; ?>" />
              <?php if ($error_image) { ?>
              <span class="error"><?php echo $error_image; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_directory; ?></td>
            <td><input type="text" name="directory" value="<?php echo $directory; ?>" />
              <?php if ($error_directory) { ?>
              <span class="error"><?php echo $error_directory; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_filename; ?></td>
            <td><input type="text" name="filename" value="<?php echo $filename; ?>" />
              <?php if ($error_filename) { ?>
              <span class="error"><?php echo $error_filename; ?></span>
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
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="sort_order" value="<?php echo $sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
