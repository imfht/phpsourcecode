/**
 * Created by kunono on 2015/2/28.
 */
app.controller('productCtrl',['story','product','log','$stateParams','comment','component_uploadFile','component_modalImage','cart','cnzz','$state','data',function(story,product,log,$stateParams,comment,component_uploadFile,component_modalImage,cart,cnzz,$state,data){
    cnzz.active();


    var vm = this;
    vm.makeComment = makeComment;
    component_uploadFile.init();
    refresh();

    function refresh(){
        product.get($stateParams.id).then(function(data){
            vm.product = data.product;
            product.getStories(vm.product.id,false).then(function(success){
                vm.product.stories = success.stories;
            })

        });
    }
    vm.addToCart = function(){
        if(!data.user.data.isLoggedIn){
            $state.go('user.login');
            return;
        }
        cart.add(vm.product.id,1,vm.product.price);
    };
    function makeComment(){
        if(_.isEmpty(vm.comment.content) || vm.comment.rating == undefined || !(vm.comment.rating >=1 && vm.comment.rating <= 5)){
            log.log('productCtrl makeComment content or rating is empty');
            return;
        }
        component_uploadFile.upload(vm.product.upload_file_limit_id).then(function(success){
            var t = _.map(success.files,function(file){return file.upload_file_name});
            comment.make(vm.product.id,vm.comment.content,vm.comment.rating,t,vm.product.upload_file_limit_id).then(function(success){
                refresh();
            });
        });

    }
}]);