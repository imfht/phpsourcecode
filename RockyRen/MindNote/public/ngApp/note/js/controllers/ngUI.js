/**
 * Created by rockyren on 15/5/19.
 */
define(['noteJS/module'], function(noteModule){
  noteModule.controller('InputHandleModalController', ['$scope', '$modalInstance', 'inputInfo', function($scope, $modalInstance, inputInfo){

    $scope.modalTitle = inputInfo.modalTitle;

    $scope.inputName = inputInfo.inputName;


    $scope.ok = function() {
      //将新增笔记的名字传给mindnote控制器
      $modalInstance.close($scope.inputName);

    };

    $scope.cancel = function(){
      $modalInstance.dismiss('cancel');
    };
  }]);


  noteModule.controller('AlertHandleModalController', ['$scope', '$modalInstance', 'alertInfo', function($scope, $modalInstance, alertInfo){
    $scope.modalTitle = alertInfo.modalTitle;
    $scope.alertMessage = alertInfo.alertMessage;

    $scope.ok = function(){
      $modalInstance.close();
    };

    $scope.cancel = function(){
      $modalInstance.dismiss('cancel');
    };
  }]);
});