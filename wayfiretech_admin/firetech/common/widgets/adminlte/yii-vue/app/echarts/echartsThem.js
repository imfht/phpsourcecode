let echartsThem = function(echartsObj,them){
    let data = '';
    if(them){
        data = require('./themes/'+them+'.js');
    }else{
        data = require('./themes/walden.js');
    }
    console.log('样式数据',data.default,echartsObj,Vue)
    echartsObj.registerTheme('walden',data.default);
}
Vue.prototype.echartsThem=echartsThem