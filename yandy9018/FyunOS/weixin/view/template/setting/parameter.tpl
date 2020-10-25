<?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="content">
     <div class="row">
	 <div class="span3 ">
      <h3>常规设置</h3>
      <p>设置分页单页显示的个数,邀请赠送积分的额度,开始审核评论的选项,注册邮件验证的开关选项,以及设置发货单的单号规则等.</p>
     </div>
	  <div class="span3">
     <h3>服务器设置</h3>
      <p>设置是否开启伪静态重写功能,是否显示错误信息,记录错误日志,网站统计代码,服务器SSL等.</p>
     </div>
      <div class="span3">
        <h3>邮件设置</h3>
        <p>设置发送邮件的参数,可以设置使用SMTP发送系统邮件,也可以使用部分系统自带的send_mail方法发送邮件,前提是系统支持.</p>
     </div>
      <div class="span3">
       <h3>在线客服</h3>
       <p>设置网站需要用到的在线IM,如QQ,旺旺等,设置后需要在页面扩展->模块里开启 在线客服模块 后才可以使用.</p>
      </div>
      <div class="span3">
       	<h3>货币与语言设置</h3>
       <p>设置网站使用到的语言版本以及货币,系统默认语言为简体中文,默认货币为人民币.</p>
      </div>
      <div class="span3">
       	<h3>库存以及订单状态,物流公司</h3>
       <p>系统预设了常用的一些状态值,可根据自身的需求修改状态表述;可以根据具体情况配送物流公司信息.</p>
      </div>
       <div class="span3">
       <h3>商品退换</h3>
       <p>设置退货管理中用到的原因,退换处理结果,退换的处理状态等.</p>
      </div>
      <div class="span3">
       	<h3>国家地区设置</h3>
       <p>国家设置预设为中国地区,包括中国省市数据,区域群组主要用来设置一些在物流配送等有共性的区域,比如江浙沪,等.</p>
      </div>
      <div class="span3">
       	<h3>商品规格以及税率设置</h3>
        <p>商业规格主要用来统一产品用到的度量单位,预设为厘米,千克,克等单位,税率主要用于设置产品的稅点,用于自动计算稅费.</p>
      </div>
      <div class="span3">
       	<h3>更多关于系统设置的问题</h3>
        <p>如果您有其他关于系统设置的疑问或者更好的建议,欢迎访问shopilex.com</p>
      </div>
	</div>
    </div>
  </div>


