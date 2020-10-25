<div id="side_right">
<h2><strong>会员管理</strong></h2>
<h3><span class="title">会员<strong><?php echo $model->mname;?></strong>信息</span></h3>

<div class="create">

<form>
<table width="900" border="0"  class="table_b">
 <tr>
    <th>会员ID：</th>
    <td><?php echo $model->id;?></td>
  </tr>
  <tr>
    <th>昵称：</th>
    <td><?php echo $model->mname;?></td>
  </tr>
  <tr>
    <th>头像：</th>
    <td>
    <p>
    <img src="<?php echo Yii::app()->baseUrl;?>/system/images/self.jpg" />
    </p>
    
    
    </td>
  </tr>
  <tr>
    <th>邮箱：</th>
    <td><?php echo $model->email;?></td>
  </tr>
  <tr>
    <th>邮箱验证：</th>
    <td><?php echo $model->email_validate==0?'未验证':'已验证';?></td>
  </tr>
    <tr>
    <th>QQ：</th>
    <td><?php echo $model->qq;?></td>
  </tr>
  <tr>
    <th>电话：</th>
    <td><?php echo $model->telephone;?>

    </td>
  </tr>
   <tr>
    <th>地址：</th>
    <td>
    <?php echo $model->address;?>
    
    </td>
 
  </tr>

   <tr>
    <th>真实姓名：</th>
    <td><?php echo $model->real_name;?></td>
  </tr>

     <tr>
    <th>身份证号：</th>
    <td><?php echo $model->id_card;?></td>
  </tr>
  <tr>
    <th>编辑：</th>
    <td><div class="update"><img src="<?php echo Yii::app()->baseUrl;?>/system/images/update.png" /><span><?php echo CHtml::link('修改信息',array('update','id'=>$model->id))?></span><img src="<?php echo Yii::app()->baseUrl;?>/system/images/delete.png" /><span><?php echo CHtml::link('删除会员',array('delete','id'=>$model->id),array('confirm'=>'确认删除此用户？'))?></span></div></td>
  </tr>
</table>


</form>

</div>






</div>
<!---------------side_right end---------------->

