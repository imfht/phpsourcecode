<?php if ($error) { ?>
  <div class="alert alert-error"><?php echo $error; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2> <?php echo $heading_title; ?></h2>
    </div>
    <div class="content">
      <table class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $column_name; ?></td>
            <td class="left"><?php echo $column_status; ?></td>
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($extensions) { ?>
          <?php foreach ($extensions as $extension) { ?>
          <tr>
            <td class="left"><?php echo $extension['name']; ?></td>
            <td class="left"><?php echo $extension['status'] ?></td>
            <td class="right"><?php foreach ($extension['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
