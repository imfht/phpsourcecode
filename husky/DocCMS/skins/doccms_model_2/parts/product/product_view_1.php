<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style>
.probox { padding-left:400px; position:relative; height:332px;}
.pro_img { width:400px; display:inline-table; text-align:center; position:absolute; left:0; top:0; }
.pro_txt { padding-right:30px; }
.pro_txt p { height:30px; line-height:25px; }
.pro_txt h2 { font-weight:bold; line-height:30px; }
.pro_txt .details { line-height:20px; border:1px solid #ddd; padding:6px; background:#fff; height:137px; overflow:hidden;}
.main1box { margin-bottom:10px; }
#main1 ul { display:none; }
#main1 ul li { display:inline-block; _display:inline; position:relative; margin:0 auto; }
#main1 ul.block { display:block; }
.menu1box { }
#menu1 li { display:inline; cursor:pointer; }
#menu1 li img { border:1px solid #ccc; width:50px; height:50px;}
#menu1 li.hover img { border:1px solid #669900; }
.jqzoom { border:1px solid black; float:left; position:relative; padding:0px; cursor:pointer; }
.jqzoom img { float:left; }
div.zoomdiv { z-index:100; position:absolute; top:0px; left:355px; width:200px; height:200px; background:#ffffff; border:1px solid #CCCCCC; display:none; text-align:center; overflow:hidden; }
div.jqZoomPup { z-index:10; visibility:hidden; position:absolute; top:0px; left:0px; width:50px; height:50px; border:1px solid #aaa; background:#ffffff url(<?php echo $tag['path.skin'];?>res/images/zoom.gif) 50% top no-repeat; opacity:0.5; -moz-opacity:0.5; -khtml-opacity:0.5; filter:alpha(Opacity=50); }
/*分页*/
.endPageNum { clear:both; font-size:12px; text-align:center; font-family:"宋体"; }
.endPageNum table { margin:auto; }
.endPageNum .s1 { width:52px; }
.endPageNum .s2 { background:#1f3a87; border:1px solid #ccc; color:#fff; font-weight:bold; }
.endPageNum a.s2:visited { color:#fff; }
.endPageNum a { padding:2px 5px; margin:5px 4px 0 0; color:#1F3A87; background:#fff; display:inline-table; border:1px solid #ccc; float:left; }
.endPageNum a:visited { color:#1f3a87; }
.endPageNum a:hover { color:#fff; background:#1f3a87; border:1px solid #1f3a87; float:left; text-decoration:underline; }
.endPageNum .s3 { cursor:default; padding:2px 5px; margin:5px 4px 0 0; color:#ccc; background:#fff; display:inline-table; border:1px solid #ccc; float:left; }
.prointr{ border:1px solid #ddd; margin:20px 0;}
.prointr h2{ height:40px; background:url(<?php echo $tag['path.skin'];?>res/images/hbg.jpg) repeat-x; font-size:20px; font-family:"微软雅黑", "黑体"; font-weight:normal; padding:3px 0 0 15px; text-shadow: 0 1px 0 #FFFFFF;}
.prointr p{ line-height:22px; text-indent:28px; color:#666; padding:10px;}
.details h2{ font-size:12px; font-weight:normal;}
.xgprlist{ width:100%; float:left; padding-top:15px;}
.xgprlist h3{ font-size:16px; font-weight:normal; padding:10px 0 0 15px; height:30px; background:#f0f0f0; margin-bottom:10px;}
.xgprlist ul li{ width:146px; height:160px; float:left; margin:0 12px; display:inline;}
.xgprlist ul li a{ display:block; width:145px; height:160px; z-index:10;}
.xgprlist ul li img{ width:140px; height:105px; float:left; padding:2px; border:1px solid #ccc;}
.xgprlist ul li span{ width:146px; text-align:center; float:left; padding-top:10px; font-size:12px;}
</style>
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $tag['path.skin'];?>res/js/jquery.jqzoom.js"></script>
<script>
$(document).ready(function(){
$(".main li").jqueryzoom({
	xzoom: 300,
	yzoom: 300,
	offset: 10,
	position: "right",
	preload:1,
	lens:1
});
});
function setTab(m,n){
var tli=document.getElementById("menu"+m).getElementsByTagName("li");
var mli=document.getElementById("main"+m).getElementsByTagName("ul");
for(i=0;i<tli.length;i++){
   tli[i].className=i==n?"hover":"";
   mli[i].style.display=i==n?"block":"none";
}
}
</script>
<?php 
//2011-09-10
$data=$tag['data.row'];
?>

<div class="probox">
  <div class="pro_img">
    <div class="main1box">
      <div class="main" id="main1">
        <?php 
			$originalPic = explode('|',$data['originalPic']);
			$middlePic   = explode('|',$data['middlePic']);
			$smallPic    = explode('|',$data['smallPic']);
			for($i=0;$i<count($originalPic);$i++)
			{
		  ?>
        <ul<?php echo !$i?' class="block"':''?>>
          <li><img src="<?php echo ispic($middlePic[$i])?>" jqimg="<?php echo ispic($originalPic[$i])?>" width="360" height="270"/></li>
        </ul>
        <?php
		    }?>
      </div>
    </div>
    <div class="menu1box">
      <ul id="menu1">
        <?php 
		for($i=0;$i<count($smallPic);$i++)
		{
		?>
        <li onmouseover="setTab(1,<?php echo $i;?>)"><img src="<?php echo  ispic($smallPic[$i])?>" /></li>
        <?php
		}?>
      </ul>
    </div>
  </div>
  <div class="pro_txt">
    <h2><?php echo $data['title']; ?></h2>
    <?php sys_push($data['spec'],'<p>{name}:{value}</p>',0)?> 
    <div class="details"><h2>产品简介: <?php echo $data['description']; ?></h2></div>
  </div>
</div>
	<?php sys_push($data['content'],'<div class="prointr"><h2>{name}</h2><p>{value}</p></div>',1)?>
  <div class="endPageNum">
    <table align="center">
      <tr>
        <td><?php echo $data['navbar'];?>
          </td>
      </tr>
    </table>
  </div>
  <div class="xgprlist">
  <h3>相关产品：</h3>
	<ul>
    	<?php sys_about(4,0); ?>
    </ul>
  </div>
  <p class="prodetails">点击数：<?php echo $data['counts']; ?> 录入时间：<?php echo $data['dtTime']; ?>【<a href="javascript:printing('productshow')">打印此页</a>】【<a href="javascript:history.back(1)">返回</a>】</p>
</div>
<?php unset($data);?>
