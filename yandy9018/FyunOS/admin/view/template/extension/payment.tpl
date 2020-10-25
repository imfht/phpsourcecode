<?php if ($error) { ?>
  <div class="alert alert-error"><?php echo $error; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2> <?php echo $heading_title; ?></h2>
       <div class="buttons">
      	<button onclick="$('#form').submit();" class="btn btn-primary"><?php echo $button_set_default; ?></button>
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $default; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="list">
        <thead>
          <tr>
            <td class="left" width="30px" style="text-align: left;" ><?php echo $column_default; ?></td>
            <td class="left"><?php echo $column_name; ?></td>
            <td></td>
            <td class="left"><?php echo $column_status; ?></td>
            <td class="right"><?php echo $column_sort_order; ?></td>
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($extensions) { ?>
          <?php foreach ($extensions as $extension) { ?>
            <?php if($extension['installed']) {?>
	          <tr>
	             <td class="left"> 
	            <?php if($default_payment==$extension['code']){ ?>
	           		 <input type="radio" checked="checked"  name="config_default_payment" value="<?php echo $extension['code']; ?>" />
	            <?php }else { ?>
	            	 <input type="radio" name="config_default_payment" value="<?php echo $extension['code']; ?>" />
	            <?php } ?>
	            </td>
	            <td class="left"><span class="label label-success"><?php echo $text_installed;?></span> <?php echo $extension['name']; ?></td>
	            <td class="center"><?php echo $extension['link'] ?></td>
	            <td class="left"><?php echo $extension['status'] ?></td>
	            <td class="right"><?php echo $extension['sort_order']; ?></td>
	            <td class="right"><?php foreach ($extension['action'] as $action) { ?>
	              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
	              <?php } ?></td>
	          </tr>
			<?php } ?>
          <?php } ?>
          
          <?php foreach ($extensions as $extension) { ?>
               <?php if(!$extension['installed']) {?>
          	          <tr>
          	            <td class="left">&nbsp;</td>
          	            <td class="left"><span class="label"><?php echo $text_uninstalled;?></span> <?php echo $extension['name']; ?></td>
          	            <td class="center"><?php echo $extension['link'] ?></td>
          	            <td class="left"><?php echo $extension['status'] ?></td>
          	            <td class="right"><?php echo $extension['sort_order']; ?></td>
          	            <td class="right"><?php foreach ($extension['action'] as $action) { ?>
          	              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
          	              <?php } ?></td>
          	          </tr>
          			<?php } ?>
               <?php } ?>
          
          <?php } else { ?>
          <tr>
            <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
     </form>
    </div>
  </div>
