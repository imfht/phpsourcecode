<?php
# batchly generate xianjinquan 
# wadelau@ufqi.com on Sun Jan 31 10:22:15 CST 2016
# Mon May 13 13:07:32 HKT 2019
#

require("../comm/header.inc.php");

$gtbl = new GTbl($tbl, array(), $elementsep);

include("../comm/tblconf.php");

# main actions
$out = "";
if($act == 'dobatch1'){
    #$out .= "act:[$act] reqt:".serialize($_REQUEST);
    $out .= "<fieldset><legend> 批量生成促销现金券: 步骤2 </legend>
        <form id='addstepform' name='addstepform' action='extra/xianjinquan.php?sid=$sid&act=dobatch2&tbl=$tbl&db=$db' method='post'>";
    $out .= "<p> 主题：".($sname=Wht::get($_REQUEST, 'sname'))." <input name='sname' type='hidden' value='".$sname."'/>  </p>"; 
    $out .= "<p> 面值：".($facevalue=intval(Wht::get($_REQUEST, 'facevalue')))." 元 <input name='facevalue' type='hidden' value='".$facevalue."'/>  </p>"; 
    $out .= "<p> 数量：".($icount=intval(Wht::get($_REQUEST, 'icount')))." 张 <input name='icount' type='hidden' value='".$icount."'/>  </p>"; 
    $out .= "<p> 绑定到商品Id：".($bind2productid=intval(Wht::get($_REQUEST, 'bind2productid')))."  <input name='bind2productid' type='hidden' value='".$bind2productid."'/>  </p>"; 
    $out .= "<p> 绑定到商户Id：".($bind2storeid=intval(Wht::get($_REQUEST, 'bind2storeid')))."  <input name='bind2storeid' type='hidden' value='".$bind2storeid."'/>  </p>"; 
    $out .= "<p> 有效截止日期：".($dendtime=Wht::get($_REQUEST, 'dendtime'))." <input name='dendtime' type='hidden' value='".$dendtime."'/>  </p>"; 
    $out .= " <p> <input type='button' name='rtnbtn' value='返回修改' onclick=\"javascript:GTAj.backGTAjax('contentarea','1');\"/>
        <input type='submit' value='确认无误, 批量生成' id='addmultistepsubmit' onclick=\"javascript:doActionEx(this.form.name,'contentarea');\"/></p>
        </form>
        </fieldset>";
}
else if($act == 'dobatch2'){

    $sname=Wht::get($_REQUEST, 'sname');
    $facevalue=intval(Wht::get($_REQUEST, 'facevalue'));
    $icount=intval(Wht::get($_REQUEST, 'icount'));
    $bind2productid=intval(Wht::get($_REQUEST, 'bind2productid'));
    $bind2storeid=intval(Wht::get($_REQUEST, 'bind2storeid'));
    $dendtime=Wht::get($_REQUEST, 'dendtime');

    $succi = 0;
    for($i=0; $i<$icount; $i++){
        $scode = date('mdHis', time()).$i;
        $tmpsql = "insert into ".$tbl." set sname='$sname', scode='$scode', facevalue=$facevalue, dendtime='$dendtime', dinserttime=NOW(),bind2productid='".$bind2productid."',bind2storeid='".$bind2storeid."'";
        $result = $gtbl->execBy($tmpsql);
        if($result[0]){
            $succi++;
        }
        debug("extra/xinjinquan: ".$tmpsql." result:".serialize($result));
    }

    $out .= "成功批量生成 $succi 张 $facevalue 元现金券! 请 <a href='javascript:window.location.reload();'>刷新浏览</a>.";

}
else{
    $out .= "<fieldset><legend> 批量生成促销现金券: 步骤1 </legend>
        <form id='addstepform' name='addstepform' action='extra/xianjinquan.php?sid=$sid&act=dobatch1&tbl=$tbl&db=$db' method='post'>";
    $out .= "<p> 主题：<input name='sname' style=\"width:260px\"/>  </p>"; 
    $out .= "<p> 面值：<input name='facevalue'/> <br/> 单位为元, 填写整数 如, 100, 50, 20, 10等  </p>";
    $out .= "<p> 数量：<input name='icount'/> <br/> 共 ? 张, 填写整数 如, 100, 50, 20, 10等  </p>";
    $out .= "<p> 绑定到商品Id：<input name='bind2productid'/> <br/> 只在浏览该商品时可领用,不填为所有 </p>";
    $out .= "<p> 绑定到商户Id：<input name='bind2storeid'/> <br/> 只在浏览该商户的商品时可领用,不填为所有  </p>";
    $out .= "<p> 有效截止日期：<input name='dendtime'/><br/>日期格式: YYYY-mm-dd , 如 2019-12-31  </p>"; 
    $out .= "
        <p><input type='submit' value='确认, 下一步' id='addmultistepsubmit' onclick=\"javascript:doActionEx(this.form.name,'contentarea');\"/></p>
        </form>
        </fieldset>";

}

# or

$data['respobj'] = array('output'=>'content');

# module path
$module_path = '';
include_once($appdir."/comm/modulepath.inc.php");

# without html header and/or html footer
$isoput = false;

require("../comm/footer.inc.php");

?>