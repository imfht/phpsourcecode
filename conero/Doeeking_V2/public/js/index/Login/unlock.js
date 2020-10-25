$(function(){
    Cro.checkLockable();
});
var Cro = new Conero();
Cro.checkLockable = function(){
    var lockable = this.getJsVar('unlockable');
    if('Y' == lockable){
        $('#lock_update').attr('disabled','disabled');
         $('#lock_update').after('<a href="/conero/index/login/quit"><button type="button">注销</button></a>');
    }
}