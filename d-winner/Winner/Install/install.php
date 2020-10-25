<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
 
if(file_exists('lock.txt')){
    echo '系统已安装，请不要重复安装！如需安装，请删除install文件夹下的lock.txt文件。';
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="x-ua-compatible" content="ie=7" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Winner权限管理系统 - 安装向导</title>
<script language="javascript">
<!-- 
function onNext(){
	window.location = "check.php";
}

function onClose(){
	window.close();
}
-->
</script>
<link href="img/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div align="center">
<div class="main">
<div class="top">
  <img src="img/logo_about.png" height="45" />
  <span>Winner权限管理系统</span></div>
 <div class="content">
   <div>
     <br/>
     <h3 align="center">中文版授权协议 适用于中文用户</h3>
     <p>&nbsp;&nbsp;&nbsp; 版权所有 (c) 2010-2015，九五时代保留所有权利。</p>
     <p>&nbsp;&nbsp;&nbsp; 感谢您选择 Winner权限管理系统 产品。希望我们的努力能为您提供一个高效快速和强大的权限管理解决方案，节省开发时间与成本，免去重复开发，让您能立即进入关键项目功能的开发。</p>
     <p>&nbsp;&nbsp;&nbsp; 九五时代为 Winner权限管理系统 产品的开发商，依法独立拥有 Winner权限管理系统 产品著作权。九五时代网址为   http://www.95era.com，Winner权限管理系统官方网站网址为 http://www.95era.com，Winner权限管理系统官方讨论区网址为   http://bbs.95era.com。</p>
     <p>&nbsp;&nbsp;&nbsp; Winner权限管理系统   著作权已在中华人民共和国国家版权局注册，著作权受到法律和国际公约保护。使用者：无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用 Winner权限管理系统。</p>
     <p>&nbsp;&nbsp;&nbsp; 本授权协议适用且仅适用于 Winner权限管理系统 1.x 版本，九五时代拥有对本授权协议的最终解释权。</p>
     <h4>I. 协议许可的权利</h4>
     <ol>
       <li>您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用。 </li>
       <li>您可以在协议规定的约束和限制范围内修改 Winner权限管理系统 源代码(如果被提供的话)或界面风格以适应您的WEB应用要求。 </li>
       <li>您拥有使用本软件构建的WEB应用及相关信息的所有权，并独立承担与文章内容的相关法律义务。 </li>
       <li>获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持期限、技术支持方式和技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。 </li>
     </ol>
     <h4>II. 协议规定的约束和限制</h4>
     <ol>
       <li>未获商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目或实现盈利的网站）。购买商业授权请登陆http://www.95era.com参考相关说明。 </li>
       <li>不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。 </li>
       <li>无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用 Winner权限管理系统 的整体或任何部分，未经书面许可，页面页脚处的 Winner权限管理系统  名称和九五时代官方网站（http://www.95era.com） 的链接都必须保留，而不能清除或修改。 </li>
       <li>禁止在 Winner权限管理系统 的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。 </li>
       <li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。 </li>
     </ol>
     <h4>III. 有限担保和免责声明</h4>
     <ol>
       <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。 </li>
       <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。 </li>
       <li>九五时代不对使用本软件构建的网站中的文章或信息承担责任。 </li>
     </ol>
     <p>&nbsp;&nbsp;&nbsp; 有关 Winner权限管理系统 最终用户授权协议、商业授权与技术服务的详细内容，均由 Winner权限管理系统   官方网站独家提供。九五时代拥有在不事先通知的情况下，修改授权协议和服务价目表的权力，修改后的协议或价目表对自改变之日起的新授权用户生效。</p>
     <p>&nbsp;&nbsp;&nbsp; 电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装   95CMS，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</p>
   </div>
 </div> 
 <div class="act"><input onclick="onClose()" class="but" name="no" type="button" value="不同意" /> &nbsp; <input onclick="onNext()" class="but" name="yes" type="button" value="同意" />
 <div><img src="img/step.png" width="700" height="10" /></div>
 </div>
 <div class="foot">Copyright 2010-2015 <a href="http://www.95era.com/" target="_blank">九五时代</a> Inc.   All Rights Reserved</div>
</div>
</div>
</body>
</html>
