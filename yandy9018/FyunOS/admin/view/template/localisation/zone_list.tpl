 <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><a onclick="location = '<?php echo $insert; ?>'" class="btn btn-primary"><span><?php echo $button_insert; ?></span></a> <a onclick="$('form').submit();" class="btn"><span><?php echo $button_delete; ?></span></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?php if ($sort == 'c.name') { ?>
                <a href="<?php echo $sort_country; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_country; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_country; ?>"><?php echo $column_country; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'z.name') { ?>
                <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                <?php } ?></td>
         
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($zones) { ?>
            <?php foreach ($zones as $zone) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($zone['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $zone['zone_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $zone['zone_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $zone['country']; ?></td>
              <td class="left"><?php echo $zone['name']; ?></td>
             
              <td class="right"><?php foreach ($zone['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
