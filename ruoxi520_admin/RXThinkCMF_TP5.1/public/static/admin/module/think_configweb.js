/**
 * 网站配置
 * @auth 牧羊人
 * @date 2020/01/03
 */
layui.use(['function', 'form'], function () {

    //【声明变量】
    var func = layui.function
        , form = layui.form
        , $ = layui.$;

    // 【日期选择】
    func.initDate(['date_select|date'], function (value, date) {
        console.log("当前选择日期:" + value);
        console.log("日期详细信息：" + JSON.stringify(date));
    });

    // 【时间选择】
    func.initDate(['datetime_select|datetime'], function (value, date) {
        console.log("当前选择日期:" + value);
        console.log("日期详细信息：" + JSON.stringify(date));
    });
});
