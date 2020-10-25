<?php
if ($c_main == 0)
  echo '<section class="height-100 imagebg cover cover-1 parallax" data-overlay="3"><div class="background-image-holder">';
else
  echo '<section class="height-60 imagebg cover cover-1 parallax" data-overlay="3"><div class="background-image-holder" style="height:60vh;">';
?>
    <?php
    $g_pic = $db->getOne("SELECT s_picture FROM slideshow WHERE s_parent='global' ORDER BY s_order ASC,id DESC");
    $i_pic = $db->getOne("SELECT s_picture FROM slideshow WHERE s_parent='index' ORDER BY s_order ASC,id DESC");
    $c_pic = $db->getOne("SELECT s_picture FROM slideshow WHERE s_parent='".@$channel['id']."' ORDER BY s_order ASC,id DESC");
    if (empty($c_pic)&&empty($i_pic)&&empty($g_pic))
      $pic = $t_path.'img/hero23.jpg';
    elseif (!empty($c_pic))
      $pic = $c_pic;
    elseif (!empty($i_pic))
      $pic = $i_pic;
    else
      $pic = $g_pic;
    echo '<img alt="image" src="'.$pic.'" />';
    unset_str('$g_pic,$i_pic,$c_pic,$pic');
    ?>
  </div>
</section>
