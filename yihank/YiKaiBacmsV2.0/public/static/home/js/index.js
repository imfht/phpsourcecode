/*
 * @Author: Paco
 * @Date:   2017-01-31
 * @lastModify 2017-03-19
 * +----------------------------------------------------------------------
 * | jqadmin [ jq酷打造的一款懒人后台模板 ]
 * | Copyright (c) 2017 http://jqadmin.jqcool.net All rights reserved.
 * | Licensed ( http://jqadmin.jqcool.net/licenses/ )
 * | Author: Paco <admin@jqcool.net>
 * +----------------------------------------------------------------------
 */

layui.define(['jquery', 'layer', 'ajax'], function(exports) {
    var $ = layui.jquery,
        layer = layui.layer;
    var ajax = layui.ajax;
    /**
     * 更换语言
     */
    ajax.chang= function(ret, options, that) {
        if (ret.status==200){//成功
            layer.msg(ret.msg, {
                icon: 1,
                time: 2000
            },function () {
                window.location.href=window.location;
            });
        }else{//失败
            layer.msg(ret.msg, {
                icon: 5
            });
        }
    }
    exports('index', {});
});