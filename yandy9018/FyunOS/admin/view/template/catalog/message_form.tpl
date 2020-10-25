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
            <td><?php echo $entry_author; ?></td>
            <td><?php echo $message['author']; ?>
            <input type="hidden" name="author" value="<?php echo $message['author']; ?>" />
              </td>
          </tr>
         <tr>
            <td><?php echo $entry_mail; ?></td>
            <td><?php echo $message['email']; ?> <input type="hidden" name="email"  value="<?php echo $message['email']; ?>" />
              </td>
          </tr>
          <tr>
            <td> <?php echo $entry_message; ?></td>
            <td>
            	<textarea readonly="true"  name="message" cols="60" rows="8"><?php echo $message['message']; ?></textarea>
            </td>
          </tr>
            <tr>
            <td> <?php echo $entry_reply; ?></td>
            <td>
            	<textarea  name="reply" cols="60" rows="8"><?php echo $message['reply']; ?></textarea>
            </td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="status">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_replyed; ?></option>
                <option value="0"><?php echo $text_no_reply; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_replyed; ?></option>
                <option value="0" selected="selected"><?php echo $text_no_reply; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
