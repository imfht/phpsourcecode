/**
 * Created by kunono on 2015/3/12.
 */
app.controller('categoryCtrl',['category','$stateParams','cart','data','$state','cnzz',function(category,$stateParams,cart,data,$state,cnzz){
    cnzz.active();
    var vm = this;
    category.get($stateParams.id).then(function(data){
        vm.category = data.category;
        category.getProductLimit($stateParams.id,100).then(function(data){
            vm.products = data.products;
        })
    });
    vm.addToCart = addToCart;
    function addToCart(product){
        if(!data.user.data.isLoggedIn){
            $state.go('user.login');
            return;
        }
        cart.add(product.id,1,product.price);
    }

}]);