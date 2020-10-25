/**
 * Created by kunono on 2015/3/12.
 */
app.service('utility',['$rootScope','component_modalImage',function($rootScope,component_modalImage){
    $rootScope.utility = $rootScope.utility||{};
    $rootScope.utility.generateUrl = function(state,value){
        if(state=='product'){
            return '#/product/'+value;
        }
        if(state=='story'){
            return '#/story/'+value;
        }
    };
    $rootScope.utility.showModalImage = function(url){
        component_modalImage.show(url);
    };
}]);