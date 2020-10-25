/**
 * Created by kunono on 2015/3/22.
 */
app.controller('adminUserCtrl',['adminUser',function(adminUser){
    var vm = this;
    vm.hasPermissionUser = false;
    vm.count = 0;
    adminUser.hasPermission().then(function(bool){
        vm.hasPermissionUser = bool;
    });
    adminUser.getUserCount().then(function(c){
       vm.count = c;
    });
}]);
