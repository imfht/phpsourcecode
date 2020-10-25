 <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
     <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="$('form').submit();" class="btn btn-primary"><?php echo $button_delete; ?></button></div>
    </div>
    <div class="content">
      <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?php if ($sort == 'author') { ?>
                <a href="<?php echo $sort_title; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_author; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_title; ?>"><?php echo $column_author; ?></a>
                <?php } ?></td>
              <td class="left"><?php if ($sort == 'i.sort_order') { ?>
                <a href="<?php echo $sort_sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_mail; ?></a>
                <?php } else { ?>
                <a href="<?php echo $sort_sort_order; ?>"><?php echo $column_mail; ?></a>
                <?php } ?></td>
               <td class="left"><?php echo $column_message; ?></td>
              <td class="left"><?php echo $column_add_date; ?></td>
              <td class="right"><?php echo $column_status; ?></td>
              <td class="right"><?php echo $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <?php if ($messages) { ?>
            <?php foreach ($messages as $message) { ?>
            <tr>
              <td style="text-align: center;"><?php if ($message['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $message['message_id']; ?>" checked="checked" />
                <?php } else { ?>
                <input type="checkbox" name="selected[]" value="<?php echo $message['message_id']; ?>" />
                <?php } ?></td>
              <td class="left"><?php echo $message['author']; ?></td>
              <td class="left"><?php echo $message['email']; ?></td>
              <td class="left"><?php echo $message['message']; ?></td>
              <td class="left"><?php echo $message['date_added']; ?></td>
              <td class="left"><?php echo $message['status']; ?></td>
              <td class="right"><?php foreach ($message['action'] as $action) { ?>
                [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                <?php } ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
              <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?php echo $pagination; ?></div>
    </div>
  </div>
