/**
 * Created by kunono on 2015/2/1.
 */
app.controller('appCtrl',['user','$scope','data','$state','marketing','component_banner','order',function(user,$scope,data,$state,marketing,component_banner,order){
    $scope.vm = this;
    var vm = $scope.vm;
    vm.user = data.user;
    vm.cart = data.cart;
    vm.order = data.order;
    vm.login = login;
    vm.logout = user.logout;
    vm.activeOrdersNumber = order.getCustomActiveNumber;

    function login(){
        $state.go('user.login',{lastState:'main'});
    }
}]);