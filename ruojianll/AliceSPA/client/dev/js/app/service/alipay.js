/**
 * Created by kunono on 2015/3/29.
 */

app.service('alipay',['base',function(base){
    return {
        getFormData:getFormData
    };
    function getFormData(orderId){
        var json = {};
        json.order_id = orderId;
        return base.post('/alipay/formData',json,'alipay formData');
    }
}]);