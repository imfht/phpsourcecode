/**
 * Created by rockyren on 14/11/4.
 */
define(['jquery'],function($){
  /**
   * 实现导航栏高亮
   */
  var highlight = function(){
    var base_url = window.location.protocol + '//' + window.location.host;
    var current_url = window.location.href;
    var current_model = current_url.replace(base_url,'').split('/')[1];

    $('#nav-function li').each(function(){
      //alert($(this).text());
      var link_url = $(this).find('a').attr('href');
      var link_model = link_url.replace(base_url,'').split('/')[1];

      if(current_model.indexOf(link_model) >= 0) {
        $(this).addClass('active');
      }

    });
  };

  $('#password_confirmation').blur(function(){
    var pwc = $('#password_confirmation').val();
    var pw = $('#password').val();
    if(pwc != pw) {
      $('#pwcIM').text("与密码不同").addClass("my-alert");
    }
    else {
      $('#pwcIM').text("").removeClass("my-alert");
    }
  });

  var $loadingInfo = $('<p class="alert alert-info" style="margin-top: 0.3em; margin-bottom: 0">正在检查有效性...</p>');

  function checkRepeat(mixed, $errorInfo){
    var $self = $(mixed);
    var mixedValue = $self.val();
    var $selfParent = $self.parent();

    var infoCopied = $loadingInfo.clone();
    infoCopied.insertAfter($selfParent);
    $errorInfo.remove();

    $.ajax({
      'url': '/api/repeat/' + mixedValue,
      success: function(data){
        infoCopied.remove();
        if( data === 'repeated' ){
          $errorInfo.insertAfter($selfParent);
        }
      }
    })
  }

  /**
   * 注册页表单即时验证
   */
  var $repeatedEmail = $('<p class="alert alert-info alert-danger">该电子邮箱地址已经被注册</p>');
  $('#email').blur(function(){
    var emailFilter  = new RegExp("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])");
    var email = $('#email').val();
    if(!emailFilter.test(email)) {
      $('#emailIM').text("邮箱格式不正确").addClass("my-alert");
    } else {
      $('#emailIM').text("").removeClass("my-alert");

      checkRepeat('#email', $repeatedEmail);
    }
  });

  var $repeatedName = $('<p class="alert alert-info alert-danger">该用户名已经被注册</p>');
  $('#username').blur(function(){
    checkRepeat(this, $repeatedName);
  });


  return {
    highlight: highlight
  }
});