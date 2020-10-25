 <!-- shopping cart wrapper start -->
    <form id="shoppingCartWrapper" class="shoppingCartWrapper" action="#" method="post">
      <fieldset>
      
       <?php foreach ($products as $product) { ?>
        <!-- shopping cart product starts -->
        <div class="shoppingCartProductWrapper"> 
        <a  href="" class="shoppingCartProductImageWrapper">
        <img src="catalog/view/theme/diancan/images/content/shoppingCartProductImage-1.jpg" class="shoppingCartProductImage" alt=""></a>
          <div class="shoppingCartProductInfoWrapper"> 
           <a class="shoppingCartProductTitle" href="<?php echo $product['href']; ?>"> <?php echo $product['name']; ?></a>

            <div class="shoppingCartProductButtonsWrapper">
             <input type="hidden" value="<?php echo $product['product_id']; ?>" class="product_id"> 
             <input type="text" readonly="readonly" id="shoppingCartProductNumber-1" class="shoppingCartProductNumber" name="t1" value="<?php echo $product['quantity1']; ?>">
              <span class="shoppingCartProductPrice"><?php echo $product['price']; ?>元/<?php echo $product['unit']; ?>  <?php if ($product['reward']>0) { ?>
                    <font>送<?php echo $product['reward']; ?>积分</font>    
                     <?php } ?>  </span><a href="" class="shoppingCartRemoveProductButton"></a> </div>
          </div>
        </div>
        <!-- shopping cart product ends --> 
        
 <?php } ?>
        
        <!-- shopping cart info wrapper starts -->
        <div class="shoppingCartInfoWrapper"> <span class="shoppingCartProductsNumber">数量: <?php echo $count; ?></span> <span class="shoppingCartProductsTotal">总计: <?php echo $carAllCash; ?>元</span> </div>
        <!-- shopping cart info wrapper ends -->
        
        <div class="shoppingCartButtonsWrapper"><a href="" class="shoppingCartEmptyButton">进入餐车</a>
          <input type="submit" value="确认下单" id="shoppingCartCheckoutButton" class="shoppingCartCheckoutButton">
        </div>
      </fieldset>
    </form>
    <!-- shopping cart wrapper ends --> 