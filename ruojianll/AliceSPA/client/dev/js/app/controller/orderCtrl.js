/**
 * Created by kunono on 2015/2/9.
 */

app.controller('orderCtrl',['order','data','address','$state','user',function(order,data,address,$state,user){
    user.makeLogin();
    var vm = this;
    vm.order = data.order;
    vm.remove = remove;
    vm.fixAddress = address.fix;
    vm.setReceived = setReceived;
    order.refresh();
    function refreshAddress(order){
        order.address = address.getAddress(order.address_id);
        console.log('asdasd');
        console.log(order);
    }
    function remove(corder){
        order.remove(corder.id);
    }
    function setReceived(ordert){
        order.setReceived(ordert.id).then(function(res){
            order.refresh();
        });
    }

}]);
