/**
 * Created by kunono on 2015/3/9.
 */
app.controller('storyCtrl',['product','story','$stateParams','log','component_modalImage','cnzz',function(product,story,$stateParams,log,component_modalImage,cnzz){
    cnzz.active();
    var vm = this;
    vm.id = $stateParams.id;
    story.get(vm.id).then(function(data){
        vm.story = data.story;
    });
}]);
