/**
* Created by Happy on 2016/7/15 0015.
*/
define(['common/js/config','core/util/util'],function(require) {
var Config = require('common/js/config');
var Util = require('core/util/util');

return {
index:function(){
alert('this is demo index');
alert('ajax url method'+Config.get_url('act','op'));
}
}
});