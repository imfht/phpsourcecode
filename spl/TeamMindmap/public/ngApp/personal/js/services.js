/**
 * Created by spatra on 14-12-6.
 */

define(['personalJS/module'], function(personalModule){

  personalModule.factory('PersonalInfoService', ['$http', function($http){
    var baseUrl = 'api/personal/';

    return{
      getInfo: function(){
        return $http.get(baseUrl + 'info');
      },
      updateInfo: function(infoData){
        return $http.put(baseUrl + 'info', infoData);
      },
      updatePassword: function(passwordData){
        return $http.put(baseUrl + 'password', passwordData);
      },
      checkUpdatePasswordInfo: function(passwordData, errorMessages){
        //清除原有的错误信息
        ['password', 'newPassword', 'newPassword_confirmation'].forEach(function(item){
          delete errorMessages[item];
        });

        if( passwordData.password
          && passwordData.password.replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == '' ){
          errorMessages['password'] = '请输入原密码';
          return false;
        }

        if( passwordData.newPassword
          && passwordData.newPassword.replace(/^(\s|\u00A0)+/,'').replace(/(\s|\u00A0)+$/,'') == '' ){
          errorMessages['newPassword'] = '新密码不能为空';
          return false;
        }

        if( passwordData.newPassword != passwordData.newPassword_confirmation ){
          errorMessages['newPassword'] = '两次输入密码不一致 ';
          return false;
        }

        return true;
      }
    };
  }]);

});