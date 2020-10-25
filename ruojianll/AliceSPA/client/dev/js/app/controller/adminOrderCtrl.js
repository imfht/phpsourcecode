/**
 * Created by kunono on 2015/2/26.
 */
app.controller('adminOrderCtrl',['log','adminOrder','adminAddress','data','address',function(log,adminOrder,adminAddress,data,address){
    var vm = this;
    vm.orderFilter = orderFilter;

    vm.hasPermissionOrder = false;
    vm.filter = -1;
    vm.showDeleted = false;
    vm.refresh = refresh;
    vm.setSend = setSend;
    vm.getAddress = getAddress;
    vm.states = data.order.states;
    adminOrder.hasPermission().then(function(res){
        vm.hasPermissionOrder = res.hasPermission;
    });
    vm.refresh();
    function refresh(){
        adminOrder.refresh().then(function(res){
            vm.orders_o = res.orders;
            vm.orderFilter(0);
        });
    }
    function orderFilter(filter) {
        if(filter != 0){
            vm.filter = filter;
        }
        vm.orders = _.filter(vm.orders_o,function(order){
           return (order.state == vm.filter || vm.filter == -1) && (vm.showDeleted?true:order.public == 1);
        });
    }
    function setSend(order){
        if(_.isEmpty(order.config.post_id)|| _.isEmpty(order.config.post_company)){
            log.log('post_id or post_company is empty');
            return;
        }
        adminOrder.setSend(order.id,order.config.post_id,order.config.post_company).then(function(res){
            if(res.success){
                vm.refresh();
            }
        });
    }
    function getAddress(order){
        adminAddress.get(order.address_id).then(function(res){
            order.address = res.address;
            address.fix(order.address);

        })
    }
}]);