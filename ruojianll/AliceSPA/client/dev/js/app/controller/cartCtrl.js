/**
 * Created by kunono on 2015/2/7.
 */

app.controller('cartCtrl',[
    'cart','data','log','order','$state','component_modalMessage','user','$rootScope',
    function(cart,data,log,order,$state,component_modalMessage,user,$rootScope){
        user.makeLogin();
        var vm = this;
        vm.cart = data.cart;
        vm.address = data.address;
        vm.increaseNumber = increaseNumber;
        vm.reduceNumber = reduceNumber;
        vm.remove = remove;
        vm.makeOrder = makeOrder;
        vm.setAddressId = setAddressId;
        $rootScope.$watch(function(){
            return vm.address.data.length;
        },function(newValue,oldValue){
            if(oldValue == 0 && newValue > 0){
                setAddressId(vm.address.data[0].id);
            }
        });
        if(vm.address.data.length >0){
            setAddressId(vm.address.data[0].id);
        }
        vm.removeAll = function(){
            cart.remove_all().then(function(success){
                data.cart.data.items = [];
            })
        };
        function setAddressId(addid){
            vm.address_id = addid;
        }
        function setNumber(pid,number){
            return cart.setNumber(pid,number);
        }
        function reduceNumber(item){
            if(item.number > 1){
                setNumber(item.product_id,item.number - 1).then();
            }
        }
        function increaseNumber(item){
            item.number = parseInt(item.number);
            if(item.number == NaN){
                return;
            }
            setNumber(item.product_id,item.number + 1).then(function(success){},function(error) {
                if (error.errors[0] == 11) {
                    component_modalMessage.show('商品库存不足');
                }
            });
        }
        function remove(item){
            var pid = item.product_id;
            cart.remove(pid).then(function(res){
                if(res.success){
                    data.cart.data.items = _.filter(data.cart.data.items,function(item){
                        return item.product_id != pid;
                    });
                }
            });
        }
        function makeOrder(){
            if(vm.address == undefined){
                log.log('cart makeOrder fail , address is empty');
                return;
            }
            if(vm.address_id == undefined){
                log.log('no address selected');
                return;
            }
            order.add(vm.address_id,vm.cart.data.items).then(function(res){
                if(res.success){
                    cart.remove_all().then(function(res){
                        if(res.success){
                            data.cart.data.items = [];
                        }
                    });
                    $state.go('pay',{id:res.order_id,price:vm.getTotalPrice()});
                }
            });
        }
        vm.getTotalPrice = function(){
            var sum = 0;
            _.each(vm.cart.data.items,function(item){
                sum += item.price * item.number;
            });
            return sum;
        }
    }]);
