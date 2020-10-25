//数组的笛卡尔积
function descartes(args){
    if(args == undefined) return null;
    var len = args.length;
    if(len == 1){
        return args[0];
    }else{
        var tem = new Array();
        tem = group(args[0],args[1]);
        for(var i=2;i<len;i++){
            tem = group(tem,args[i]);
        }
        var result  = new Array();
        var tem_len = tem.length;
        num = 0;
        for(var i = 0;i < tem_len;i++){
            result[num++] = tem[i].split('*#*');
        }
        return result;
    }
}
function group (m,n){
    var tem = new Array();
    var num = 0;
    for(var i=0;i<m.length;i++){
        for(var j=0;j<n.length;j++){
            tem[num++] =m[i]+'*#*'+n[j];
        }
    }
    return tem;
}