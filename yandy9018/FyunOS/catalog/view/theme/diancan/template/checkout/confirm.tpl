<script>
$(document).ready(function () {
    var ckcart = '';
  
    $.cookie('cart', ckcart, {expires:7,path:'/'});
  });
</script>
   <h3 class="pageTitle">下单成功！</h3>
      <a href="" class="susorder"><img src="catalog/view/theme/diancan/image/checkmark.png" alt="" /></a> <br>
      
 <?php if ($total > 0) { ?>
        <p>稍后您需要付给<?php echo $text_server; ?> <font color="#990000""><?php echo $total; ?></font> 元</p>
        <?php }else { ?>
        
        <p>已从您的账户中扣款，不用在付钱了。</p>
        
        <?php } ?>
        
        
     <div class="columnWrapper oneHalf">
          <a id="jsurl" href="<?php echo $home_links; ?>" class="gohome">返回首页</a>
         </div>
         
      <div class="columnWrapper oneHalf lastColumn">
 <a id="jsurl" href="<?php echo $category_links; ?>" class="singleProductPurchaseButton">继续点餐</a>
   </div>
      <div class="pageBreak"></div>
        <table>
        <thead>
          <tr>
            <th>商品名称</th>
            <th>数量</th>
            <th>价格</th>
          </tr>
          
        </thead>
        <tbody>
            <?php foreach ($products as $product) { ?>
          <tr>
            <td><?php echo $product['name']; ?></td>
            <td><?php echo $product['quantity1']; ?></td>
            <td><?php echo $product['total']; ?>元</td>
          </tr>
               <?php } ?> 
     
        </tbody>
      </table>
