/**
 * Created by kunono on 2015/2/7.
 */
app.service('cart',[
    'base','data','log','$q','component_modalMessage',
    function(base,data,log,$q,component_modalMessage) {
        return{
            refresh:refresh,
            setNumber:setNumber,
            remove:remove,
            remove_all:remove_all,
            add:add
        };
        function refresh(){
            if(!data.user.data.isLoggedIn){
                log.log('cart user is not loggedin');
                return;
            }
            var defer = $q.defer();
            base.get('/cart/all','cart all').then(function(res){
                _.each(res.cart,function(item){
                    item.number = parseInt(item.number);
                });
                data.cart.data.items = res.cart;
                defer.resolve(res);
                log.log('cart refresh success');
            },function(err){
                defer.reject(err);
            });
            return defer.promise;
        }
        function setNumber(productId,number){
            var json = {};
            json.product_id = productId;
            json.number = number;
            var defer = $q.defer();
            base.post('/cart/setNumber',json,'cart setNumber').then(function(res){
                defer.resolve(res);
                var t = _.find(data.cart.data.items,function(i){
                    return i.product_id == productId;
                });
                if(t != undefined){
                    t.number = number;
                }
                log.log('cart setNumber success');
            },function(err){
                defer.reject(err);
            });
            return defer.promise;
        }
        function remove(pid){
            var json = {};
            json.product_id = pid;
            return base.post('/cart/delete',json,'cart delete');
        }
        function add(pid,number,price){
            var json = {};
            json.product_id = pid;
            json.number = number;
            json.price = price;
            var defer = $q.defer();
            base.post('/cart/add',json,'cart add').then(function(res){
                component_modalMessage.show('商品已加入购物车,您可以在购物车中修改商品数量');
                refresh().then(function(refreshSuccess){
                    defer.resolve(res);
                });
            },function(err){
                if(err.errors[0] == 9){
                    component_modalMessage.show('购物车中已存在该商品，您可以在购物车中修改商品数量');
                }
                defer.reject(err);
            });
            return defer.promise;
        }
        function remove_all(){
            return base.get('/cart/delete_all','cart delete_all');
        }
    }]);