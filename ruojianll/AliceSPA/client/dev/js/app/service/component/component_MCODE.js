/**
 * Created by kunono on 2015/3/4.
 */
app.service('component_MCODE',['log','$rootScope','MCODE','component_ICODE','config','component_modalMessage',function(log,$rootScope,MCODE,component_ICODE,config,component_modalMessage){
    if($rootScope.component  == undefined){
        $rootScope.component = {};
    }
    $rootScope.component.MCODE = {mobilephone:"",code:"",correct:undefined,send:{},check:{}};
    var mcode = $rootScope.component.MCODE;
    mcode.check = check;
    mcode.send = send;
    mcode.setRequestMCODE = setRequestMCODE;

    function check(){
        if(mcode.code.length==config.MCODE_length){
            MCODE.check(mcode.code).then(function(res){
                mcode.correct = res;
            });
        }
        else{
            mcode.correct=undefined;
        }

    }
    function send(){
        if(!component_ICODE.correct){
            component_modalMessage.show('请先输入正确的图片验证码')
            return;
        }
        MCODE.send(component_ICODE.code,mcode.mobilephone).then(function(success){
            log.log('component_MCODE send success');
            component_modalMessage.show('短信验证码已成功发送')
        },function(err){
            component_ICODE.generate();
            component_modalMessage.show('短信验证码发送失败，请重新输入图片验证码获取')
            log.log('component_MCODE send success false');
            log.log(err);
        });
    }
    function setRequestMCODE(obj){
        obj.MCODE = mcode.code;
    };
    return $rootScope.component.MCODE;
}]);
