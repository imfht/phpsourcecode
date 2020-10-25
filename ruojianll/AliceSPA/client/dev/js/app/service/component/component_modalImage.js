/**
 * Created by kunono on 2015/3/12.
 */
app.service('component_modalImage',['log','$rootScope',function(log,$rootScope){
    if($rootScope.component  == undefined){
        $rootScope.component = {};
    }
    $rootScope.component.modalImage = {show:{}};
    $rootScope.component.modalImage.show = function(url){
        $rootScope.component.modalImage.url = url;
        $('#modalImage').modal('show');
    };
    $rootScope.component.modalImage.close = function(){
        $('#modalImage').modal('hide');
    };
    return $rootScope.component.modalImage;
}]);
