/**
 * Created by m on 17-5-2.
 */
layui.use(['tree', 'layer'], function () {
    //同步组织架构
    var organizationBtn = $('#organization_put');
    organizationBtn.on('click', '', function () {
        http.put('/member/api/organizations', '', function (data) {
            if (data.code === 0) {
                layer.alert('任务已提交，请耐心等待执行结果。', {icon: 6});
            } else {
                layer.msg(data.msg || '任务提交失败!');
            }
        })
    });
    layui.tree({
        elem: '#index_tree'
        , nodes: [{ //节点数据
            name: '部门1'
            , children: [{
                name: '部门2'
            }]
        }, {
            name: '部门3'
            , children: [{
                name: '部门4'
                , alias: 'bb' //可选
                , id: '123' //可选
            }, {
                name: '部门5'
            }]
        }]
        , click: function (node) {
            console.log(node) //node即为当前点击的节点数据
        }
    });
});