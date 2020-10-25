function showdialog(p){

    var param = {
        title: p.title,
        width: p.width,
        height: p.height,
        content: p.message
    };
    if (p.padding){
        param.padding = p.padding;
    }
    if (!p.oknull){
        param.okValue = !p.okValue ? '确定' : p.okValue;
        param.ok = function(){
            if(typeof p.okfunction!='undefined'){
                return p.okfunction();
            }
        }
    }else if(p.oknull!==true){
        param.okValue = p.oknull;
        param.ok = function(){
            if(typeof p.okfunction!='undefined'){
            
                return p.okfunction();
            }
        }
    }else{
        param.okValue = null;
    }
    
    if (!p.cancelnull){
        param.cancelValue = !p.cancelValue ? '取消' : p.cancelValue;
        param.cancel = function(){
            if(typeof p.cancelfunction!='undefined'){
                p.cancelfunction();
            }
        }
    }
    
    var d = dialog(param);
    d.show();
    if (!p.nolock){
        d.__lock();
    }

    return d;
}