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
<script language="javascript" type="text/javascript" src="<?php echo $tag['path.root'];?>/inc/js/date/WdatePicker.js"></script>
<script language="javascript">
function validator()
{
 	if(document.getElementById('name').value=="")
	{alert("请填写您的姓名!"); document.getElementById('name').focus(); return false;}
	if(document.getElementById('educational').value=="")
	{alert("请填写您的最高学历!");document.getElementById('educational').focus();return false;}
	if(document.getElementById('finishSchool').value=="")
	{alert("请填写您的毕业院校!");document.getElementById('finishSchool').focus();return false;}
	if(document.getElementById('speciality').value=="")
	{alert("请填写您的主修专业!");document.getElementById('speciality').focus();return false;}
	if(document.getElementById('experience').value=="")
	{alert("请填写您的工龄!");document.getElementById('experience').focus();return false;}
	if(document.getElementById('email').value=="")
	{alert("请填写您的E-mail!");document.getElementById('email').focus();return false;}
	if(document.getElementById('mobile').value=="")
	{alert("请填写您的移动电话!");document.getElementById('mobile').focus();return false;}
	if(document.getElementById('resume').value=="")
	{alert("请填写您的个人简历!");document.getElementById('resume').focus();return false;}
}
</script>
<style>
<style type="text/css">
*{ padding:0; margin:0;}
img{ border:none;}
#jobsinfo{ width:98%; margin:0 15px; font-size:12px; color:#666;}
.jobinfott{ width:100%; line-height:26px; float:left; background:#EBEBEB; padding:8px 0 0 10px;}
.jobreminder{ width:100%;; height:24px; padding-top:7px; float:left; background:url(images/location_bg.gif) bottom repeat-x; text-align:center; margin-bottom:10px;}
.xx{border-collapse: collapse; border-top:1px solid #ebebeb; border-left:1px solid #ebebeb; margin:0 auto;}
.xx td{ border-right:1px solid #ebebeb; border-bottom:1px solid #ebebeb; padding:5px;}
.xx input { border: 1px solid #ccc; color: #6F6F6F; font-size: 12px; height:18px; padding:4px; line-height:18px;}
.xx textarea { border: 1px solid #CCCCCC; color: #6F6F6F; font-size: 12px; width:500px;}
.jobtable{ width:100%; float:left; margin-bottom:15px;}
.xx .buttons{ height:30px; background-color: #ECECEC; background-image: -moz-linear-gradient(#F4F4F4, #ECECEC); border: 1px solid #D4D4D4; border-radius: 0.2em 0.2em 0.2em 0.2em; color: #333333; cursor: pointer; display: inline-block; font:12px "微软雅黑"; margin: 0; outline: medium none; overflow: visible; padding: 0.3em 1em; position: relative; text-decoration: none; text-shadow: 1px 1px 0 #FFFFFF; white-space: nowrap;}
.xx .buttons:hover,.xx .buttons:focus,.xx .buttons:active { background-color: #3072B3; background-image: -moz-linear-gradient(#599BDC, #3072B3); border-color: #3072B3 #3072B3 #2A65A0; color: #FFFFFF; text-decoration: none; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3);}
</style>

<div id="jobsinfo">
      <div class="jobinfott">郑重声明：请如实填写相关内容，对填报虚假信息如学历、工作经历以及其他相关内容，损害公司利益获得任职资格者，将依法追究法律责任。</div>
      <div class="jobreminder">对自荐申请资料中任何有关个人的信息，我们将无条件履行保密义务，尽职尽责地保护您的隐私。</div>
      <div class="jobtable">
          <form name="form1" id="form1" method="post" action="<?php echo sys_href($request['p'],'job_send',$request['r'])?>"  onsubmit="return validator()">
            <table width="662" cellspacing="0" bordercolor="#D7D8DC" border="0" align="center" class="xx">
                  <tbody><tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;姓 名<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" maxlength="10" size="16" id="name" name="name"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;性 别<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><select id="sex" name="sex" style="width:80px;">
                        <option selected="selected" value="男">男</option>
                        <option value="女">女</option>
                      </select>
                      </td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;民 族<font color="#EC0000">*</font></td>
                    <td width="140" height="22"><select name="nation" id="nation">
                          <option selected="selected" value="汉族">汉族</option>
                            <option value="蒙古族">蒙古族</option>
                            <option value="回族">回族</option>
                            <option value="藏族">藏族</option>
                            <option value="维吾尔族">维吾尔族</option>
                            <option value="苗族">苗族</option>
                            <option value="彝族">彝族</option>
                            <option value="壮族">壮族</option>
                            <option value="布依族">布依族</option>
                            <option value="朝鲜族">朝鲜族</option>
                            <option value="满族">满族</option>
                            <option value="侗族">侗族</option>
                            <option value="瑶族">瑶族</option>
                            <option value="白族">白族</option>
                            <option value="土家族">土家族</option>
                            <option value="哈尼族">哈尼族</option>
                            <option value="哈萨克族">哈萨克族</option>
                            <option value="傣族">傣族</option>
                            <option value="黎族">黎族</option>
                            <option value="傈僳族">傈僳族</option>
                            <option value="佤族">佤族</option>
                            <option value="畲族">畲族</option>
                            <option value="高山族">高山族</option>
                            <option value="拉祜">拉祜</option>
                            <option value="水族">水族</option>
                            <option value="东乡族">东乡族</option>
                            <option value="纳西族">纳西族</option>
                            <option value="景颇族">景颇族</option>
                            <option value="柯尔克孜">柯尔克孜</option>
                            <option value="土族">土族</option>
                            <option value="达斡尔族">达斡尔族</option>
                            <option value="仫佬族">仫佬族</option>
                            <option value="羌族">羌族</option>
                            <option value="布朗族">布朗族</option>
                            <option value="撒拉族">撒拉族</option>
                            <option value="毛难族">毛难族</option>
                            <option value="仡佬族">仡佬族</option>
                            <option value="锡伯族">锡伯族</option>
                            <option value="阿昌族">阿昌族</option>
                            <option value="普米族">普米族</option>
                            <option value="塔吉克族">塔吉克族</option>
                            <option value="怒族">怒族</option>
                            <option value="乌孜别克">乌孜别克</option>
                            <option value="俄罗斯族">俄罗斯族</option>
                            <option value="鄂温克族">鄂温克族</option>
                            <option value="崩龙族">崩龙族</option>
                            <option value="保安族">保安族</option>
                            <option value="裕固族">裕固族</option>
                            <option value="京族">京族</option>
                            <option value="塔塔尔族">塔塔尔族</option>
                            <option value="独龙族">独龙族</option>
                            <option value="鄂伦春族">鄂伦春族</option>
                            <option value="赫哲族">赫哲族</option>
                            <option value="门巴族">门巴族</option>
                            <option value="珞巴族">珞巴族</option>
                            <option value="基诺族">基诺族</option>
                            <option value="外国血统">外国血统</option>
                            <option value="其他">其他</option>
                          </select>
                      </td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;出生日期</td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="birthday" name="birthday" onClick="WdatePicker()"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;身 高</td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="height" name="height"></td>
                    <td width="90" bgcolor="#F7F7F7">身份证号<font color="#EC0000">*</font></td>
                    <td width="140" height="22"><input type="text" maxlength="20" size="18" id="TxtCode" name="TxtCode"></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7"> &nbsp;婚姻状况</td>
                    <td width="120" height="22"><select id="isMarried" name="isMarried" style="width:80px;">
                        <option value="已婚">已婚</option>
                        <option selected="selected" value="未婚">未婚</option>
                      </select>
                      </td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;户 籍</td>
                    <td width="120" height="22"><input type="text" maxlength="200" size="16" id="residence" name="residence"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;现住地址</td>
                    <td width="140" height="22"><input type="text" maxlength="200" size="18" id="TxtAddress1" name="TxtAddress1"></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;学 历<font color="#EC0000">*</font></td>
                    <td width="120" height="22">
                          <select id="educational" name="educational" style="width:80px;">
                            <option value="博士">博士</option>
                            <option value="硕士">硕士</option>
                            <option value="本科">本科</option>
                            <option selected="selected" value="专科">专科</option>
                            <option value="中专">中专</option>
                            <option value="高中">高中</option>
                            <option value="初中">初中</option>
                            <option value="小学">小学</option>
                          </select>
                    </td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;毕业院校<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" size="16" id="finishSchool" name="finishSchool"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;所学专业<font color="#EC0000">*</font></td>
                    <td width="140" height="22"><input type="text" size="18" id="speciality" name="speciality"></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;毕业时间<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="finishTime" name="finishTime" onClick="WdatePicker()"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;外语与水平</td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="languageSkill" name="languageSkill"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;计算机水平</td>
                    <td width="140" height="22"><input type="text" maxlength="18" size="18" id="TxtComputer" name="TxtComputer"></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;专业职称</td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="TxtFunction" name="TxtFunction"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;职称级别</td>
                    <td width="120" height="22"><select id="SelFClass" name="SelFClass" style="width:80px;">
                        <option value="高级">高级</option>
                        <option value="中级">中级</option>
                        <option value="初级">初级</option>
                        <option selected="selected" value="无">无</option>
                      </select>
                      </td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;工 龄</td>
                    <td width="140" height="22"><input type="text" maxlength="10" size="10" id="experience" name="experience">年</td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;目前年薪</td>
                    <td width="120" height="22"><input type="text" maxlength="8" size="8" id="TxtNowWage" name="TxtNowWage">万元/年</td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;求职岗位<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="TxtWantWork" name="TxtWantWork"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;期望年薪</td>
                    <td width="140" height="22"><input type="text" maxlength="10" size="10" id="TxtWantWage" name="TxtWantWage">万元/年</td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;联系电话<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" size="16" id="telphone" name="telphone"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;手 机</td>
                    <td width="120" height="22"><input type="text" maxlength="18" size="16" id="mobile" name="mobile"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;电子邮件</td>
                    <td width="140" height="22"><input type="text" maxlength="25" size="18" id="email" name="email"></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;个人主页</td>
                    <td width="120" height="22"><input type="text" maxlength="25" size="16" id="TxtHomePage" name="TxtHomePage"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;联系地址<font color="#EC0000">*</font></td>
                    <td width="120" height="22"><input type="text" maxlength="50" size="16" id="address" name="address"></td>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;邮 编<font color="#EC0000">*</font></td>
                    <td width="140" height="22"><input type="text" maxlength="18" size="18" id="TxtZip" name="TxtZip"></td>
                  </tr>
                  <input type="hidden" value="" name="inc">
                  <input type="hidden" value="无" name="works">
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;教育培训</td>
                    <td colspan="5"><textarea id="TxtTeach" rows="5" cols="97" name="TxtTeach"></textarea></td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;工作经历</td>
                    <td colspan="5">
                      <p><textarea id="TxtTask" rows="8" cols="97" name="TxtTask"></textarea></p>
                    </td>
                  </tr>
                  <tr>
                    <td width="90" bgcolor="#F7F7F7">&nbsp;个人简历<font color="#EC0000">*</font></td>
                    <td colspan="5"><textarea id="resume" rows="8" cols="97" name="resume"></textarea></td>
                  </tr>
                  <tr>
                    <td height="60" colspan="6">
                        <p align="center">
                            <input type="Submit" onclick="return CheckForm()" value="提  交" class="buttons" name="Submit">
                          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <input type="reset" value="重  置" class="buttons" name="Submit2">
                        </p>
                    </td>
                  </tr>
                </tbody>
            </table>
          </form>
      </div>
</div>