<?php

//DEBUG 加载二维码类 开始
require_once(dirname(__FILE__).'/tcpdf_min/tcpdf_barcodes_2d.php');

//DEBUG 设置二维码内容
$barcodeobj = new TCPDF2DBarcode('http://www.dzmvc.com', 'QRCODE,H');
//DEBUG 以 PNG 图片形式输出二维码
//$barcodeobj->getBarcodePNG(6, 6, array(0,0,0));
//DEBUG 以 SVG 图片形式输出二维码
// output the barcode as SVG image
//$barcodeobj->getBarcodeSVG(6, 6, 'black');
//DEBUG 以 HTML 形式输出二维码
//$barcodeobj_html = $barcodeobj->getBarcodeHTML(6, 6, 'black');
//echo $barcodeobj_html;
//DEBUG 二维码 结束

//DEBUG 加载tcpdf 开始
require_once(dirname(__FILE__).'/tcpdf_min/tcpdf.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('土豆工作组');
$pdf->SetTitle('PDF TEST');
$pdf->SetSubject('DEMO 001');
$pdf->SetKeywords('定制开发/运维推广/维护托管');

//DEBUG 设置头部数据
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' PDF', PDF_HEADER_STRING);

//DEBUG 设置页头页脚
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

//DEBUG 默认设置字符等宽
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//DEBUG 设置间距
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//DEBUG 自动分页
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//DEBUG 设置字体
$pdf->SetFont('stsongstdlight', '', 12);
// ---------------------------------------------------------

//DEBUG 增加一页
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

//DEBUG 创建第一页内容
$html = '<h1>土豆工作组</h1>
DZ Framework<br />
专注互联网开发<br /><br />
<table>
<tr>
	<td width="400px">业务服务</td>
	<td width="100px">QQ</td>
	<td width="100px">微信</td>
</tr>
<tr>
	<td>
		面向中小型企业的互联网软件开发服务与解决方案<br />
		网站建设解决方案 网站推广解决方案 企业品牌全案策划<br />
		域名注册 企业邮箱 代理备案 云服务器 代理托管 安全审查<br />
		<b>+86 18913622808</b> &nbsp;&nbsp; info@dzmvc.com &nbsp;&nbsp; 中国江苏苏州
	</td>
	<td><img src="http://www.dzmvc.com/images/im.jpg" alt="QQ" width="" height="" border="0" /></td>
	<td><img src="http://www.dzmvc.com/images/weixin.jpg" alt="微信" width="" height="" border="0" /></td>
</tr>
</table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// test some inline CSS
$html = '<div class="content">
		<div class="row-fluid">
			<div class="span4">
				<p><strong>定制开发.</strong></p>
				<p>目前互联网不再是一个新事物，我们在地铁、公交车、宾馆里或餐厅里随时随地都能感受到网络带来的好处，如果不重视互联网信息化建设，意味着少了很大一部分隐形客户。我们为客户提供全方位个性化的互联网解决方案。</p>
			</div>
			<div class="span4">
				<p><strong>运维推广</strong></p>
				<p>团队凭借多年来的设计、开发以及运维、营销经验，整合了一套设计、开发以及运维、营销方案。
				我们深刻地理解互联网的发展已经客户对新时期互联网的特殊需求，量身打造全方位的互联网解决方案</p>
			</div>
			<div class="span4">
				<p><strong>维护托管</strong></p>
				<p>网络的增长速度比以往任何时候都迅猛，互联网安全形式也越来越严峻。系统服务器漏洞、软件攻击、恶意代码注入即病毒感染。我们为客户提供可靠的服务器代理运维、压力测试及软件安全审查服务。</p>
			</div>
		</div>
	</div>';

$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table

// add a page
$pdf->AddPage();

$html = '<div class="services-info" id="serv-pop1">
	<div class="row-fluid">
		<h2>设计</h2>
		<div class="span9">
			<p>包括标识（商标和品牌）、出版物（杂志，报纸和书籍）、平面广告，海报，广告牌，网站图形元素、标志和产品包装。例如，产品包装可能包括的商标或其他的艺术作品、编排文本和纯粹的设计元素，如风格统一的图像，形状和颜色。组合是平面设计的最重要的特性之一，尤其是当产品使用预先存在的材料或多种元素。</p>
		</div>
	</div>
</div>
<div class="services-info" id="serv-pop2">
	<div class="row-fluid">
		<h2>开发</h2>
		<div class="span9">
			<p>根据用户要求开发出软件系统或者系统中的部分软件。包括需求捕捉、需求分析、设计、实现和测试。一般分为系统软件和应用软件。 设计思路和方法的一般过程，包括设计软件的功能和实现的算法和方法、软件的总体结构设计和模块设计、编程和调试、程序联调和测试以及编写、提交、产品化打包等。</p>
		</div>
	</div>
</div>
<div class="services-info" id="serv-pop3">
	<div class="row-fluid">
		<h2>运维</h2>
		<div class="span9">
			<p>一般是指对大型组织已经建立好的网络软硬件的维护，其中传统的运维是指信息技术运维(IT运维),所谓IT运维管理，是指采用相关的方法、手段、技术、制度、流程和文档 等，对运行环境（如软硬件环境、网络环境等）、业务系统和运维人员进行的综合管理服务</p>
		</div>
	</div>
</div>
<div class="services-info" id="serv-pop4">
	<div class="row-fluid">
		<h2>方案</h2>
		<div class="span9">
			<p>方案设计包括设计要求分析、系统功能分析、原理方案设计几个过程，是一个极富有创造性的设计阶段，它涉及到知识水平、经验、灵感和想象力等。主要是从分析需求出发，确定实现产品功能和性能所需要的总体对象，决定技术系统，实现产品的功能与性能到技术系统的映像，并对技术系统进行初步的评价和优化。根据设计任务书的要求，选择合理的技术系统，构思满足要求的原理方案。</p>
		</div>
	</div>
</div>
<div class="services-info" id="serv-pop5">
	<div class="row-fluid">
		<h2>推广</h2>
		<div class="span9">
			<p>就是通过研究推广的方法，制定出一套适合宣传和推广商品、服务甚至人的方案，而其中的媒介就是通过网络。被推广对象可以是企业、产品、政府以及个人等等。通常我们所指的网络推广是指通过互联网的种种手段，进行的宣传推广等活动，确切的说这也是一种互联网营销的一部分，即是通过互联网这类的推广最终达到提高转化率的目的。</p>
		</div>
	</div>
</div>
<div class="services-info" id="serv-pop6">
	<div class="row-fluid">
		<h2>摄影</h2>
		<div class="span9">
			<p>一般来讲，人们使用可见光照相，最常用到的是照相机。因场景和用途的不同，照相机有着非常多的分类。综合来讲，照相机都要有几个基本的部分以保证暴光过程，这包括：感光介质、成像透镜、曝光时间控制机构、存储介质、电子感光器、电子存储介质。</p>
		</div>
	</div>
</div>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('www.dzmfv.com.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+