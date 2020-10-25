<?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
<?php } ?>
<?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
<?php } ?>
<div class="box">
  <div class="heading">
    <h2 ><?php echo $heading_title; ?></h2>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
        <tr>
          <td><?php echo $text_all_seo;?><br/><span class="help"> <?php echo $text_warning_clear;?></span></td>
          <td>
         	<button type="submit" name="all_seo" value="all_seo" class="btn btn-primary"><?php echo $button_generate;?></button>
         </td>
        </tr>
      </table>
    </form>
</div>
