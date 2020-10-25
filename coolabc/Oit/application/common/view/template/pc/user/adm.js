$(document).ready(function () {
    console.log('-- common/view/template/pc/user/adm.js')

    // 初始化页面模块
    var index = new Page({
        Module: '{$Think.MODULE_NAME}',
        Controller: '{$Think.CONTROLLER_NAME}',
        Action: '{$Think.ACTION_NAME}'
    });

    var record = index.child_record = new Page({
        Module: '{$Think.MODULE_NAME}',
        Controller: '{$Think.CONTROLLER_NAME}',
        Action: 'vr_record',
    });

    // 传递的 js_json 数据
    index.data = JSON.parse('{$js_data}');

    // 当前链接
    index.url_curr = '__URL__';

    // 页面需要构建的组件
    for (var i = 0; i < index.data.Gui_Ele.length; i++) {
        if (!index.gui_build_state.hasOwnProperty(index.data.Gui_Ele[i])) {
            index.gui_build_state[index.data.Gui_Ele[i]] = false;
        }
    }
    console.log(index.gui_build_state);

    // 脚本代码对象初始
    var code_str = "{$data['Script']['adm']}";
    if (code_str != '') {
        (function () {
            // 初始化脚本中全局变量
            var that = index;
            index.Script = {
                adm: eval(code_str)
            };
        })();
    } else {
        index.Script = undefined;
    }
    index.gui_build_state.Script = true;

    index.auto_run().when_build_ele_finish();

    //console.log(index);
});
