<?php if ($products) { ?>  

 <?php foreach ($products as $product) { ?>      
   <!-- portfolio item starts  -->
      <div class="portfolioOneItemWrapper chanpin">
      <input type="hidden"  value="<?php echo $product['product_id']; ?>"  name="product_id">
       <a href="<?php echo $product['ythumb']; ?>" class="cboxElement portfolioOneItemImageWrapper">
      <img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
       </a>
         <?php if ($product['tag']) { ?>
       <div id="i"><span><?php echo $product['tag']; ?></span></div>
         <?php } ?>
        <div class="portfolioOneItemInfoWrapper">
          <h4 class="portfolioOneItemTitle"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a> <span style="float:right; font-size:18px; font-weight:bold; color:#F60"><?php echo $product['price']; ?>/<?php echo $product['unit']; ?></span></h4>
       
               <?php if ($product['reward']>0) { ?> <p>
                   积分：送<?php echo $product['reward']; ?>分<br>  </p>
                 <?php } ?>
        </div>
          
       <?php if ($storeType == 1){ ?>
        <div class="portfolioOneItemButtonsWrapper goodssbox"> 
         <span  class="inventory fr"  style="display: none;">库存<font  class="invenfont"><?php echo $product['quantity']; ?></font> </span>
        <span class="lgadd fr">
         <button type="button" class="lgminus jia">+</button> 
         <button type="button" class="lgplus jian yincang">-</button>  
          <input type="text"  value="0"  name="t1"  size="2"  class="addtext"  maxlength="3" id="tt"  datatype="Number"  readonly="readonly"  msg="必须为数字">
        </span>
        
        </div>
        <?php } ?>
      </div>
      <!-- portfolio item ends --> 
      <?php } ?>
<?php }else { ?>
1
<?php } ?>  
