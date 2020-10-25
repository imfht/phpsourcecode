/**
 * Created by kunono on 2015/1/29.
 */

app.controller('homeCtrl',['$scope','base','cart','product','component_modalImage','cnzz','marketing','$state',function($scope,base,cart,product,component_modalImage,cnzz,marketing,$state){
    cnzz.active();
    var vm = this;

    vm.goProduct = goProduct;
    marketing.home().then(function(data){
        vm.data = data;
    });
    function goProduct(id){
        console.log('asd');
        $state.go('product',{'id':id});
    }
    vm.goCategory = function(idt){
        $state.go('category',{id:idt})
    }
}]);