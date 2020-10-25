/**
 * Created by kunono on 2015/2/9.
 */

app.service('order',['base','log','data','$q','component_modalMessage',function(base,log,data,$q,component_modalMessage){
    return{
        refresh:refresh,
        add:add,
        remove:remove,
        setReceived:setReceived,
        pay_t:pay_t,
        getCustomActiveNumber:getCustomActiveNumber
    };
    function refresh(){
        if(!data.user.data.isLoggedIn){
            log.log('order user is not loggedin');
            return;
        }
        base.get('/order/all','order all').then(function(orders){
            data.order.data = orders;
            log.log('order refresh success');
        });
    }
    function add(address_id,items){
        var json = {};
        json.address_id = address_id;
        json.items = items;
        var defer = $q.defer();
        base.post('/order/add',json,'order add').then(function(res){
           refresh();
           defer.resolve(res);
           log.log('order add success');
        },function(err){
            defer.reject(err);
            if(err.errors[0] == 11){
                component_modalMessage.show(err.productName+' 商品数量不足');
            }
        });
        return defer.promise;
    }
    function remove(orderId){
        var json = {};
        json.id = orderId;

        var defer = $q.defer();
        base.post('/order/delete',json,'order delete').then(function(res){
            data.order.data.orders = _.filter(data.order.data.orders,function(order){
               return order.id != orderId;
            });
            defer.resolve(res);
            log.log('order remove success');
        },function(err){
            defer.reject(err);
        });
    }
    function setReceived(order_id){
        var json = {};
        json.id = order_id;
        return base.post('/order/setReceived',json,'order setReceived');
    }
    function pay_t(id,trad_id){
        var json  = {};
        json.id = id;
        json.trad_id = trad_id;
        base.post('/order/pay_t',json,'order pay').then(function(success){
            refresh();
        });
    }
    function getCustomActiveNumber(){
        var count = 0;
        _.each(data.order.data.orders,function(order){
            if(order.state == 1 || order.state == 3){
                count++;
            }
        })
        return count;
    }
}]);