/**
 * Created by kunono on 2015/2/1.
 */
app.service('component_ICODE',['$rootScope','ICODE','config',function($rootScope,ICODE,config){
    if($rootScope.component  == undefined){
        $rootScope.component = {};
    }
    $rootScope.component.ICODE = {url:"",code:"",correct:undefined,generate:{},check:{},refresh:{}};
    var icode = $rootScope.component.ICODE;
    icode.generate = function(){
        icode.code="";
        icode.url = ICODE.generateUrl();
    };
    icode.check = function(){
        if(icode.code.length==config.ICODE_length){
            ICODE.check(icode.code).then(function(res){
                icode.correct = res.correct;
            })
        }else{
            icode.correct = undefined;
        }

    };
    icode.setRequestICODE = function(obj){
        obj.ICODE = icode.code;
    };
    icode.clear = function(){
        icode.url="";
        icode.code="";
    };
    return $rootScope.component.ICODE;
}]);
