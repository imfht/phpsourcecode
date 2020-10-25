<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');
admincp::head($navbar);
?>
<script type="text/javascript">

</script>
<style type="text/css">
.rowa{display: flex;
    flex-wrap: wrap;
    flex-grow: 1;
    flex-shrink: 0;
    justify-content: space-between;
}
.tmpl{width: 24%;cursor: pointer; position: relative; text-align: center;height: 250px;border:1px solid #ccc;margin-bottom: 10px; background-color: #fff;padding: 10px 0px;}
.tmpl .icon{display: none;height: 50px;width: 50px}
.tmpl.active{color: #3a87ad;
    background-color: #d9edf7;
    border-color: #bce8f1;}
.tmpl.selected{color: #468847;
    background-color: #dff0d8;
    border-color: #d6e9c6;}
.tmpl.selected .icon{display: block;font-size: 48px;position: absolute;top: 0px;right: 0px;}
.preview{max-width: 150px;height: 150px;overflow: hidden;border:1px solid #ccc;padding: 5px;}
.title{font-weight: 600;font-size: 16px;}
</style>
<script type="text/javascript">
  function modal_callback(obj){
    var state = window.parent.modal_<?php echo $this->callback;?>('<?php echo $this->target; ?>',obj);
    if(state=='off'){
      window.top.iCMS_MODAL.destroy();
    }
  }
  $(function(){
    $(".tmpl").hover(function(){
        $(this).addClass('active');
    },function(){
        $(this).removeClass('active');
    }).toggle(
    function(){
      $(".tmpl").removeClass('selected');
      $(this).addClass('selected');
      var me=this;
      window.setTimeout(function(){
        modal_callback($('input',me)[0]);
      },1000);
    },
    function(){$(this).removeClass('selected');},
  );
  })
</script>
<div class="widget-box widget-plain">
  <div class="widget-content nopadding">
    <div class="rowa">
    <?php
      foreach ($dirArray as $dir => $value) {
        $path = $dir;
        $title = $dir;
        $jpath = iPHP_TPL_DIR.'/'.$dir.'/package.json';
        $package = array();
        if(is_file($jpath)){
          $package = json_decode(file_get_contents($jpath),true);
          $title = $package['title'];
        }
        $preview = './template/'.$path.'/preview.jpg';
    ?>
      <?php foreach ($value as $tk => $tmpl) {
          if ($tmpl!='/') {
              $path = $dir.'/'.$tmpl;
              $_title = $package['template'][$tmpl]['title'];
              // $_title && $title.='-'.$_title;
              $_preview = './template/'.$path.'/preview.jpg';
              is_file($_preview) && $preview = $_preview;
          }
          is_file($preview) OR $preview ='./app/admincp/ui/nopic.jpg';
      ?>
          <div class="tmpl">
              <input class="hide" type="checkbox" value="<?php echo $path; ?>" checked/>
              <img class="preview" src="<?php echo $preview; ?>">
              <div class="clearfix"></div>
              <h2 class="title"><?php echo $title;?></h2>
              <p class="text-info"><?php echo $_title;?></p>
              <p class="muted"><?php echo 'template/'.$path;?></p>
              <span class="icon"><i class="fa fa-check-square-o"></i></span>
          </div>
      <?php }?>
    <?php }?>
    </div>
  </div>
</div>

<?php admincp::foot();?>
