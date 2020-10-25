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
        <div class="tabs">
          <?php foreach ($languages as $language) { ?>
          <a href="#language<?php echo $language['language_id']; ?>"><img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
          <?php } ?>
        </div>
        <?php foreach ($languages as $language) { ?>
        <div id="language<?php echo $language['language_id']; ?>">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?php echo $entry_title; ?></td>
              <td><input type="text" name="measurement_class[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($measurement_class[$language['language_id']]) ? $measurement_class[$language['language_id']]['title'] : ''; ?>" />
                <?php if (isset($error_title[$language['language_id']])) { ?>
                <span class="error"><?php echo $error_title[$language['language_id']]; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_unit; ?></td>
              <td><input type="text" name="measurement_class[<?php echo $language['language_id']; ?>][unit]" value="<?php echo isset($measurement_class[$language['language_id']]) ? $measurement_class[$language['language_id']]['unit'] : ''; ?>" />
                <?php if (isset($error_unit[$language['language_id']])) { ?>
                <span class="error"><?php echo $error_unit[$language['language_id']]; ?></span>
                <?php } ?></td>
            </tr>
          </table>
        </div>
        <?php } ?>
        <table class="form">
          <?php foreach ($measurement_tos as $measurement_to) { ?>
          <tr>
            <td><?php echo $measurement_to['title']; ?>:</td>
            <td><input type="text" name="measurement_rule[<?php echo $measurement_to['measurement_class_id']; ?>]" value="<?php echo isset($measurement_rule[$measurement_to['measurement_class_id']]) ? $measurement_rule[$measurement_to['measurement_class_id']]['rule'] : ''; ?>" /></td>
          </tr>
          <?php } ?>
        </table>
      </form>
    </div>
  </div>

<script type="text/javascript"><!--
$('.tabs a').tabs(); 
//--></script> 
