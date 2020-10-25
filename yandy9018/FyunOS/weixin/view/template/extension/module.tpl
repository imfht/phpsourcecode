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
   </div>
    <div class="content">
      <table class="list">
        <thead>
          <tr>
           
            <td class="left"><?php echo $column_name; ?></td>
            <td class="left"><?php echo $column_description; ?></td>
            <td class="left"><?php echo $column_version; ?></td>
            <td class="left"><?php echo $column_author; ?></td>
            <td class="left"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($extensions) { ?>
          <?php foreach ($extensions as $extension) { ?>
          <?php if($extension['installed']) {?>
          <tr>
      		
            <td class="left"><span class="label label-success">已安装</span> <?php echo $extension['name']; ?></td>
            <td class="left"><?php echo $extension['description']; ?></td>
            <td class="left"><?php echo $extension['version']; ?></td>
            <td class="left"><?php echo $extension['author']; ?></td>
            <td class="left"><?php foreach ($extension['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
          </tr>
		 <?php }  ?>  
          <?php } ?>
          
          <?php foreach ($extensions as $extension) { ?>
                    <?php if(!$extension['installed']) {?>
                    <tr>
                    
                      <td class="left"><span class="label">未安装</span> <?php echo $extension['name']; ?></td>
                      <td class="left"><?php echo $extension['description']; ?></td>
			          <td class="left"><?php echo $extension['version']; ?></td>
			          <td class="left"><?php echo $extension['author']; ?></td>
                      <td class="left"><?php foreach ($extension['action'] as $action) { ?>
                        [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                        <?php } ?></td>
                    </tr>
          		 <?php }  ?>  
                    <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="5"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

