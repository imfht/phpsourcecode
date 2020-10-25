<div id="field-default">
  <?php if($base_fields)foreach ((array)$base_fields as $key => $value) {
        if(empty($value['name'])){
          continue;
        }
  ?>

  <div id="field_<?php echo $value['name']; ?>">
    <div class="input-prepend input-append">
      <span class="add-on"><?php echo $value['name']; ?></span>
      <span class="input-xlarge uneditable-input"><?php echo $value['field'].' ('.$value['len'].') '.($value['unsigned']?'UNSIGNED':''); ?> NOT NULL</span>
      <span class="add-on" style="width:auto"><?php echo $value['label']; ?> <?php echo $value['comment']; ?></span>
    </div>
  </div>
  <div class="clearfloat mb10"></div>
  <?php } ?>
</div>

