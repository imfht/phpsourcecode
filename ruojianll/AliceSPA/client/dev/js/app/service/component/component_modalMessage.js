/**
 * Created by kunono on 2015/3/12.
 */
app.service('component_modalMessage',['log','$rootScope',function(log,$rootScope){
    if($rootScope.component  == undefined){
        $rootScope.component = {};
    }
    $rootScope.component.modalMessage = {};
    $rootScope.component.modalMessage.show = function(message){
        $rootScope.component.modalMessage.message = message;
        $('#modalMessage').modal('show');
     }
    return $rootScope.component.modalMessage;
}]);