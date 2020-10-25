<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>应聘者信息：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <table width="100%" border="0">
  		<tr>
  		<td><strong>应聘者名称</strong>：<?php echo $resume->name ?></td>
  		<td><strong>性别</strong>：<?php echo $resume->sex ?></td>
  		<td><strong>出生日期</strong>：<?php echo $resume->birthday ?></td>
  		<td><strong>民族</strong>：<?php echo $resume->nation ?></td>
  		<td><strong>婚姻状况</strong>：<?php echo $resume->isMarried ?></td>
  		<td><strong>现有职称</strong>：<?php echo $resume->nowJob ?></td>
  		</tr>
  		<tr>
  		<td colspan="6"><strong>现在居住地</strong>：<?php echo $resume->nowAddress ?></td>
  		</tr>
  		<tr>
  		<td colspan="6"><strong>户口所在地</strong>：<?php echo $resume->residence ?></td>
   		</tr>
  		<tr>
  		<td><strong>最高学历</strong>：<?php echo $resume->educational ?></td>
  		<td><strong>身高</strong>：<?php echo $resume->height ?>CM（厘米）</td>
  		<td><strong>毕业时间</strong>：<?php echo $resume->finishTime ?></td>
  		<td><strong>毕业院校</strong>：<?php echo $resume->finishSchool ?></td>
  		<td colspan="2"><strong>主修专业</strong>：<?php echo $resume->speciality ?></td>
  		<td><strong>工作经验</strong>：<?php echo $resume->experience ?></td>
  		</tr>
  		<tr>
  		<td colspan="6"><strong>自我评价</strong>：<?php echo $resume->selfAppreciation ?></td>
  		</tr>
  		<tr>
  		<td><strong>所会外语</strong>：<?php echo $resume->languageSkill ?></td>  
  		<td><strong>主修专业</strong>：<?php echo $resume->speciality ?></td>
  		<td><strong>E-mail</strong>：<?php echo $resume->email ?></td>
  		<td><strong>住宅电话</strong>：<?php echo $resume->telphone ?></td>
  		<td><strong>移动电话</strong>：<?php echo $resume->mobile ?></td>
  		</tr>
  		<tr>
  		<td colspan="6"><strong>通讯地址</strong>：<?php echo $resume->address ?></td>  
  		</tr>
  		<tr>
  		<td colspan="6"><strong>个人简历</strong>：<?php echo $resume->resume ?></td>
  		</tr>
  		<tr>
  		<td colspan="6" align="right"><font color="Gray">添加时间：<?php echo $resume->dtTime ?></font></td>
  		</tr>
		</table>
	</td>
  </tr>
</table>

