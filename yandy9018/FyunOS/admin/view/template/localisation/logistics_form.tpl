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
       <div id="languages" class="htabs" >
            <?php foreach ($languages as $language) { ?>
           		 <a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
			<?php } ?>
          </div>
          <?php foreach ($languages as $language) { ?>
      <div id="language<?php echo $language['language_id']; ?>">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td>
              <input type="text" name="logistics[<?php echo $language['language_id']; ?>][logistics_name]" value="<?php echo isset($logistics[$language['language_id']]) ? $logistics[$language['language_id']]['logistics_name'] : ''; ?>" />
              <?php if (isset($error_name[$language['language_id']])) { ?>
              <span class="error"><?php echo $error_name[$language['language_id']]; ?></span><br />
              <?php } ?>
              </td>
          </tr>
          <tr>
            <td><?php echo $entry_link; ?></td>
            <td><input type="text" name="logistics[<?php echo $language['language_id']; ?>][logistics_link]" value="<?php echo isset($logistics[$language['language_id']]) ? $logistics[$language['language_id']]['logistics_link'] : ''; ?>" />
             </td>
          </tr>
        </table>
        </div>
        <?php } ?>
      </form>
    </div>
  </div>
