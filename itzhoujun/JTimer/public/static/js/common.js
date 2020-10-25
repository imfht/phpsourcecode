
function appendParam(url,param,value){
    var sper = url.indexOf('?') > 0 ? '&' : '?';
    return url + sper + param + '=' + value;
}