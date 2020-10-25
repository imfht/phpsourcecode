<section class="bg--secondary partners-1 space--sm">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <?php
        $size = 10;
        $res = $db->getAll("SELECT * FROM link ORDER BY l_order ASC,id DESC LIMIT 0,".$size);
        ?>
        <div class="slider" data-items="<?php echo $size;?>" data-timing="3000">
          <ul class="slides">
            <?php
            if (!empty($res)) {
              foreach ($res as $val) {
                echo '<li><img alt="img" src="'.$val['l_picture'].'" /></li>';
              }
            }else{
            echo '<li><img alt="img" src="'.$t_path.'img/partner1.png" /></li><li><img alt="img" src="'.$t_path.'img/partner2.png" /></li><li><img alt="img" src="'.$t_path.'img/partner4.png" /></li><li><img alt="img" src="'.$t_path.'img/partner3.png" /></li><li><img alt="img" src="'.$t_path.'img/partner5.png" /></li><li><img alt="img" src="'.$t_path.'img/partner6.png" /></li><li><img alt="img" src="'.$t_path.'img/partner7.png" /></li>';
            }
            ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<?php unset_str($size);?>
