/**
 * Created by kunono on 2015/2/1.
 */
app.controller('userCtrl',['$scope','base','user','data','$state','log','component_MCODE','component_ICODE','component_modalMessage',function($scope,base,user,data,$state,log,component_MCODE,component_ICODE,component_modalMessage){
    var vm = this;
    vm.login = login;
    vm.clearRegisterInput = clearRegisterInput;
    vm.register = register;
    vm.user = data.user;
    vm.checkPasswordConfirm = checkPasswordConfirm;
    vm.checkNameExist = function(){
      user.isExist('name',vm.register_name).then(function(b){
          vm.isNameExist = b;
      });
    };
    vm.checkMobilephoneExist = function(){
        if(component_MCODE.mobilephone.length==11){
            user.isExist('mobilephone',component_MCODE.mobilephone).then(function(b){
                vm.isMobilephoneExist = b;
            });
        }

    };
    function login(){
        if(_.isEmpty(vm.login_name)){
            vm.login_name = undefined;
        }
        if(_.isEmpty(vm.login_mobilephone)){
            vm.login_mobilephone = undefined;
        }
        user.login(vm.login_name,vm.login_mobilephone,vm.login_password).then(function(res){
            $state.go('main');
        },function(err){
            component_ICODE.generate();
            component_ICODE.code="";
        })
    }
    function clearRegisterInput(){
        vm.register_name = "";
        vm.register_password = "";
        vm.register_password_confirm = "";
        vm.register_mobilephone = "";
        vm.register_e_mail = "";
    }
    function checkPasswordConfirm(){
        vm.isPasswordConfirmTrue = vm.register_password == vm.register_password_confirm
    }
    function register(){
        if(vm.register_password != vm.register_password_confirm) {
            log.log('user register passwords are different');
            return;
        }
        if(vm.user.data.isLoggedIn == true){
            log.log('user register user has loggedin');
            return;
        }
        user.register(vm.register_name,vm.register_password,component_MCODE.mobilephone,vm.register_e_mail).then(function(res){
            log.log('user register success');
            $state.go('address.edit');
        },function(res){
            log.log('user register fail');
        });
    }
}]);