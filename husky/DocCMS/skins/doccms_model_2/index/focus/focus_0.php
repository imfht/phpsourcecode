<script type="text/javascript" src="<?php echo $myfocus_js;?>"></script><!--引入myFocus库-->
<script type="text/javascript">
myFocus.set({id:'<?php echo $data['boxId'];?>',pattern:'<?php echo $data['pattern'];?>', time:<?php echo $data['times'];?>,trigger:'<?php echo $data['adTrigger'];?>',width:<?php echo $data['width'];?>,height:<?php echo $data['height'];?>,txtHeight:'<?php echo $data['txtHeight'];?>'});
</script>

<div id="<?php echo $data['boxId'];?>" ><!--焦点图盒子-->
  <div class="__loading"><img src="<?php echo $tag['path.skin']?>res/plug-in/myfocus/pattern/img/loading.gif" alt="请稍候..." /></div>
  <!--载入画面-->
  <div  class="__pic">
    <ul>
    <!--内容列表-->
    <?php
    foreach($flash['results'] as $data)
    {
   ?>
      <li><a href="<?php echo $data['url'];?>" target="_blank"><img src="<?php echo $data['picpath'];?>" thumb="" alt="<?php echo $data['title'];?>" text="<?php echo $data['summary'];?>" /></a></li>
      <?php 
    }
    ?>
    </ul>
  </div>
</div>
