/**
 * Created by kunono on 2015/2/28.
 */
app.controller('payCtrl',['log','order','$stateParams','$state','alipay',function(log,order,$stateParams,$state,alipay){
    var vm = this;

    alipay.getFormData($stateParams.id).then(function(data){
        vm.formData = data.formData;
    });

    vm.pay_t = pay_t;
    vm.id = $stateParams.id;
    vm.price = $stateParams.price;
    function pay_t(){
        if(_.isEmpty(vm.trad_id)){
            log.log('pay pay trad_id is empty');
            return;
        }
        order.pay_t(vm.id,vm.trad_id);
        $state.go('main');
    }
}]);