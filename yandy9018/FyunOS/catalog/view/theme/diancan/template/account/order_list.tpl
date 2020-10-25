<?php echo $header; ?>

<body> 
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper loginPageWrapper"> 
   <?php echo $nav; ?>
    <!-- header outer wrapper ends --> 
    
    <!-- page content wrapper starts -->
    
    <div class="pageContentWrapper"> 
      
      <!-- page title starts -->
      <h3 class="pageTitle">我的订单（<?php echo $order_total; ?>）</h3>
      <!-- page title ends -->
<?php if ($orders) { ?>
 <?php foreach ($orders as $order) { ?>  
 
      <div class="postExcerptWrapper">
          <p class="smallPostQuote">订单号：#<?php echo $order['order_id']; ?>
          <br>
            <span class="smallPostQuoteAuthor"><?php echo $text_total; ?></font><?php echo $order['total']; ?>元</span><br>
   <?php if ($order['express']) { ?>
    配送员：<?php echo $order['express']; ?> &nbsp;&nbsp;电话：<?php echo $order['express_website']; ?>
    <?php } ?>
   </p>
        </div>
      <div class="smallPostInfoWrapper"><span class="singleIconWrapper singleIconText iconCalendarDark postInfo postDate postInfoNoMargin"><?php echo $text_date_added; ?></font><?php echo $order['date_added']; ?></span><a href="#" class="smallPostMoreButton"><?php echo $order['status']; ?></a></div>
      
        <table>
        <thead>
          <tr>
            <th>商品</th>
            <th>数量</th>
            <th>价格</th>
          </tr>
          
        </thead>
        <tbody>
          <?php foreach ($order['products'] as $value) { ?>
          <tr>
            <td><?php echo $value['name']; ?></td>
            <td><?php echo $value['quantity']; ?></td>
            <td><?php echo $value['total']; ?>元</td>
          </tr>
               <?php } ?> 
     
        </tbody>
      </table>
    	<?php } ?>    
	    <?php } else { ?>
        还没有订单哦！
         <?php } ?>
          <div class="textBreakBottom"></div>
         <?php echo $pagination; ?>
    </div>
    
    </div>
    
    </div>


	</body>
</html>