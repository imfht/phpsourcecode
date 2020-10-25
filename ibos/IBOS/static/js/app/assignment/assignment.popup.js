/**
 *  * Assignment
 *  指派任务全局关联模块弹窗 JS
 *  @version $Id$
 */

var AssignmentPopup = {

    // 开启列表弹窗
    openListDialog: function (param) {
        var _this = this;
        Ui.closeDialog("d_asp_list");
        data = {"associatedmodule": param.module, "associatednode": param.node, "associatedid": param.id};
        Ui.ajaxDialog(Ibos.app.url("assignment/unfinished/listpopup", data), {
            id: "d_asp_list",
            title: "关联的任务",
            width: 470,
            height: 540,
            padding: "0px",
            lock: false
        });
    }
};