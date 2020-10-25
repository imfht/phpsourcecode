/**
 * Created by spatra on 14-12-2.
 */

define(['personalJS/module'], function(personalModule){

  /**
   * 用户信息与设定编辑的控制器
   */
  personalModule.controller('PersonalSettingController',['$scope', 'userInfo', 'PersonalInfoService', 'ClassHelperService',
    function($scope, userInfo, PersonalInfoService, ClassHelperService){
      $scope.personalInfo = userInfo;
      $scope.sending = false;
      $scope.head = {
        imgUrl: 'img/userHeadImage/' + userInfo.head_image
      };

      //用于localResize
      $scope.success = function(stop){
        $scope.$apply();
        setTimeout(function(){
          stop();
        }, 500);
      };

      /*
      用户提交信息更改请求
       */
      $scope.submitPersonalInfo = function(){
        //如果更改了头像则将其放入待提交数据集
        var data = ClassHelperService.clone($scope.personalInfo);
        if( $scope.head.base64Clean ){
          data.head_image = $scope.head.base64Clean;
        }
        else{
          delete data.head_image;
        }

        $scope.sending = true;

        var waitingOpts = {
          title: '请求正在处理...'
        };

        $scope.$emit('message:wait', waitingOpts);

        PersonalInfoService.updateInfo( data )
          .success(function(){
            waitingOpts.show({
              type: 'success',
              title: '保存成功'
            });
            $scope.personalInfo.head_image = '';
            $scope.$emit('personalInfo:update');
            $scope.sending = false;
          })
          .error(function(data){
            waitingOpts.show({
              type: 'error',
              title: '失败'
            });
            console.error(data);
            $scope.sending = false;
          });

      };
  }]);//End of --> PersonalSettingController

  /**
   * 用户修改密码的控制器
   */
  personalModule.controller('PasswordEditController', ['$scope', 'PersonalInfoService',
    function($scope, PersonalInfoService){

      //初始化相关变量
      $scope.passwordInfo = {};
      $scope.errorMessages = {};

      /*
      用户更改密码
       */
      $scope.submitPasswordInfo = function(){

        if( PersonalInfoService.checkUpdatePasswordInfo($scope.passwordInfo, $scope.errorMessages) ){

          PersonalInfoService.updatePassword($scope.passwordInfo)
            .success(function(){ alert('密码已经成功修改'); $scope.errorMessages = {}; })

            .error(function(data){ $scope.errorMessages = data.errorMessages; });
        }
      };
  }]);// End of --> PasswordEditController

});