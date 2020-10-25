   <!-- main menu outer wrapper starts -->
   <div class="mainMenuOuterWrapper"> 
      <!-- main menu wrapper starts -->
      <ul class="mainMenuWrapper">
       <?php foreach ($navs as $nav) { ?>
        <li <?php if ($menu==$nav['tid']){ ?> class="currentPage" <?php } ?>><a class="jsurl" rel="<?php echo $nav['url']; ?>"><?php echo $nav['title']; ?></a></li>
      
 <?php } ?>
   <li><a class="jsurl" rel="<?php echo $map; ?>">地图导航</a></li>
      </ul>
      
      <!-- main menu wrapper ends -->
      <div class="mainMenuBottomDecoration"></div>
    </div>
    <!-- main menu outer wrapper ends --> 
    
    <!-- shopping cart wrapper start -->
    <form id="shoppingCartWrapper" class="shoppingCartWrapper">


  <!-- page wrapper starts -->
      <!-- page title starts -->
      <!-- page title ends -->
      
      <!-- checkout form starts -->
       <?php if ($products) { ?>
      <?php foreach ($products as $product) { ?>
        <!-- shopping cart product starts -->
        <div class="shoppingCartProductWrapper"> 
        
        <a  href="" class="shoppingCartProductImageWrapper"><img src="<?php echo $product['thumb']; ?>" class="shoppingCartProductImage" alt=""></a>
          <div class="shoppingCartProductInfoWrapper"> <a href="" class="shoppingCartProductTitle"><?php echo $product['name']; ?></a>
            <div class="checkoutProductButtonsWrapper">
           
              <span class="shoppingCartProductPrice"><?php echo $product['price']; ?>元/<?php echo $product['unit']; ?> × <?php echo $product['quantity1']; ?></span>
              
           
              
            </div>
          </div>
        </div>
       <?php } ?>
        <!-- shopping cart product ends --> 
         <!-- shopping cart info wrapper starts -->
        <div class="shoppingCartInfoWrapper"> <span class="shoppingCartProductsNumber"></span> <span class="shoppingCartProductsTotal">总计:<?php echo $moToFixed; ?></span> </div>
        <!-- shopping cart info wrapper ends -->
        
        <div class="shoppingCartButtonsWrapper">
         <div class="columnWrapper oneHalf">
        <a rel="<?php echo $category; ?>" class="shoppingCartEmptyButton jsurl">进入菜单</a>
    </div>
     <div class="columnWrapper oneHalf lastColumn">
      <?php if($this->customer->isLogged()){ ?>
            <a id="topcart" href="#" class="singleProductPurchaseButton">下单</a>
             <?php }else{ ?>
             <a id="" rel="<?php echo $login; ?>" href="#" class="singleProductPurchaseButton jsurl">下单</a>
              <?php } ?>
           </div> 
        </div>
        <?php }else{ ?>
         <div class="shoppingCartInfoWrapper"> <span class="shoppingCartProductsNumber">没有数据！</span> </div>
        <!-- shopping cart info wrapper ends -->
        
        <div class="shoppingCartButtonsWrapper"><a rel="<?php echo $category; ?>" class="shoppingCartEmptyButton jsurl">进入电子菜单</a></div>
           <?php } ?>

 <!-- checkout form ends -->

    <!-- page content wrapper ends -->
    

    
 
    </form>
   
    <!-- shopping cart wrapper ends --> 
    
    <!-- header outer wrapper starts -->
    <div class="headerOuterWrapper">
      <div class="headerWrapper"> <a rel="<?php echo $home; ?>" class="homeButton jsurl"></a>
       <a rel="<?php echo $account; ?>" class="accountButton jsurl"></a>
    <?php if ($storeType == 1){ ?>
      <?php if ($menu=='category'){ ?>
      <div class="shoppingCartButton" id="CartButtonNoid"></div>
      <?php }else{ ?>
       <div class="shoppingCartButton" id="shoppingCartButton"></div>
        <?php } ?>
       <?php } ?>
       
       <div class="mainMenuButton">
      </div>
      
      </div>

      
      <!-- main logo starts --> 
      <!-- main logo ends --> 
    </div>
    <!-- header outer wrapper ends --> 
    