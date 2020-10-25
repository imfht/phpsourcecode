<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = '联系我们';
// $this->params['breadcrumbs'][] = $this->title;

use yii\helpers\Url;
?>

<div class="main">
  <div class="box">
    <div class="letter">
      <div class="letter_top"><span>写信须知</span></div>
      <div class="letter_box"> 一、请您填写真实姓名、住址和联系方式，以便承办单位调查核实和反馈办理结果；<br>
        二、请您简明扼要填写信件内容，写清事情发生的时间、地点、简要经过、需要解决的问题和诉求；<br>
        三、请您客观真实反映情况和问题，对所反映内容的真实性负责，不得捏造歪曲事实，不得诬告、陷害他
        人。 </div>
    </div>
    <div class="letter">
      <div class="letter_top"><span>我要写信</span><font>（注意：带<b>*</b>的为必填项）</font></div>
      <div class="letter_content">
        <table cellpadding="0" cellpadding="0" border="0">
        <form class="">
            <tr>
              <td class="title">姓名：</td>
              <td><input type="text" class="txt" name="name" id="contact-name">
                <b>*</b></td>
            </tr>
            <tr>
              <td class="title">手机号码：</td>
              <td><input type="tel" class="txt" name="tel" id="contact-tel">
                <b>*</b></td>
            </tr>
            <tr>
              <td class="title">身份证号：</td>
              <td><input type="text" class="txt" name="idcode" id="contact-idcode"></td>
            </tr>
            <tr>
              <td class="title">电子邮箱：</td>
              <td><input type="email" class="txt" name="email" id="contact-email"></td>
            </tr>
            <tr>
              <td class="title">户口地址：</td>
              <td><input type="text" class="txt" name="address" id="contact-address">
                <b>*</b></td>
            </tr>
            <tr>
              <td class="title">信件标题：</td>
              <td><input type="text" class="txt2" name="subject" id="contact-subject">
                <b>*</b></td>
            </tr>
            <tr>
              <td class="title">信件内容：</td>
              <td><textarea name="body" id="contact-body"></textarea>
                <b class="b">*</b></td>
            </tr>
            <tr>
              <td class="title">附件上传：</td>
              <td><input class="update" type="file" name="enclosure">
                <font>上传文件格式为WOED格式，上传大小不能超过<span class="red">10M</span>)</font></td>
            </tr>
            <tr>
              <td class="title">公开意愿：</td>
              <td><input type="radio" class="radio" id="contact-open" name="open" value="1" checked>
                是
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" class="radio" id="contact-open" name="open" value="0">
                否 <span class="red"><strong>*</strong>&nbsp;&nbsp;&nbsp;&nbsp;如果您选择"是",我们可能将对您的写信内容及办理结果进行公示!</span></td>
            </tr>
            <tr>
            	<td colspan="2" align="center">
                <button type="button" class="sub" id="subBtn">提交</button>
                </td>
            </tr>
        </form>
        </table>
      </div>
    </div>
  </div>
</div>

 <script type="text/javascript">
    var ajaxUrl = "<?= Url::to('site/ajax-broker', true); ?>";

     $(function(){
        $('#hold_bottom').hide();

       function clearForm(){
          $("#contact-name").val("");
          $("#contact-tel").val("");
          $("#contact-idcode").val("");
          $("#contact-email").val("");
          $("#contact-address").val("");
          $("#contact-subject").val("");
          $("#contact-body").val("");
        }

        $('#subBtn').click(function(){
              //alert('subBtn');

              var name = $("#contact-name").val();
              var tel = $("#contact-tel").val();
              var idcode = $("#contact-idcode").val();
              var email = $("#contact-email").val();
              var address = $("#contact-address").val();
              var subject = $("#contact-subject").val();
              var body = $("#contact-body").val();
              //var open = $("#contact-open").val();
              var open = $( "input:checked" ).val(); 

              var args = {
                  'classname': '\\frontend\\models\\ContactForm',
                  'funcname': 'contactformAjax',
                  'params': {
                      'name': name,
                      'tel': tel,
                      'idcode': idcode,
                      'email': email,
                      'address': address,
                      'subject': subject,
                      'body': body,
                      'open': open,
                  }
              };

              $.ajax({
              url: ajaxUrl,
              type: 'GET',
              cache: false,
              dataType: 'json',
              data: 'args=' + JSON.stringify(args),
              success: function (ret) {
                  if(0 == ret['code'])
                  {
                      //location.reload();
                      alert('您已成功提交，谢谢。');
                      clearForm();
                  }
                  else
                  {
                      alert(ret['msg']);
                  }
                  
              },
              error: function () {
              }
              });
        });


});




 </script>
