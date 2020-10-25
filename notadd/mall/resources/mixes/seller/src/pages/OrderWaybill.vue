<script>
    import image from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                self: this,
                selfTemplateColumns: [
                    {
                        align: 'center',
                        key: 'company',
                        title: '物流公司',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'template',
                        title: '运单模版',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'example',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'example-module': true,
                                },
                            }, [
                                h('img', {
                                    domProps: {
                                        src: data.row.img,
                                    },
                                }),
                                h('div', [
                                    h('p', `宽度：${data.row.width}`),
                                    h('p', `宽度：${data.row.height}`),
                                ]),
                            ]);
                        },
                        title: '运单图例',
                    },
                    {
                        align: 'center',
                        key: 'topWidth',
                        title: '上偏移量',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'leftWidth',
                        title: '左偏移量',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'default',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.status,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '启用',
                        width: 120,
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    class: {
                                        'action-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.design(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '设计'),
                                h('i-button', {
                                    class: {
                                        'action-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.design(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '测试'),
                                h('i-button', {
                                    class: {
                                        'action-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.edit(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 330,
                    },
                ],
                selfTemplateData: [
                    {
                        company: '顺丰速运',
                        height: '139mm',
                        img: image,
                        isTemplate: true,
                        leftWidth: 2,
                        status: true,
                        template: '常用模版1',
                        topWidth: 2,
                        width: '216mm',
                    },
                    {
                        company: '顺丰速运',
                        height: '139mm',
                        img: image,
                        isTemplate: true,
                        leftWidth: 2,
                        status: true,
                        template: '常用模版1',
                        topWidth: 2,
                        width: '216mm',
                    },
                ],
                templateColumns: [
                    {
                        align: 'center',
                        key: 'company',
                        title: '物流公司',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'template',
                        title: '运单模版',
                        width: 160,
                    },
                    {
                        align: 'center',
                        key: 'example',
                        render(h, data) {
                            if (data.row.isTemplate) {
                                return h('div', {
                                    class: {
                                        'example-module': true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.img,
                                        },
                                    }),
                                    h('div', [
                                        h('p', `宽度：${data.row.width}`),
                                        h('p', `宽度：${data.row.height}`),
                                    ]),
                                ]);
                            }
                            return '';
                        },
                        title: '运单图例',
                    },
                    {
                        key: 'default',
                        render(h, data) {
                            if (data.row.isTemplate) {
                                return h('i-switch', {
                                    props: {
                                        size: 'large',
                                        value: data.row.status,
                                    },
                                    scopedSlots: {
                                        close() {
                                            return h('span', '关闭');
                                        },
                                        open() {
                                            return h('span', '开启');
                                        },
                                    },
                                });
                            }
                            return '';
                        },
                        title: '默认',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            const items = [];
                            if (data.row.isTemplate) {
                                items.push(h('div', [
                                    h('i-button', {
                                        class: {
                                            'edit-btn': true,
                                        },
                                        on: {
                                            click() {},
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '编辑'),
                                    h('i-button', {
                                        on: {
                                            click() {},
                                        },
                                        props: {
                                            size: 'small',
                                            type: 'ghost',
                                        },
                                    }, '解绑'),
                                ]));
                            }
                            if (!data.row.isTemplate) {
                                items.push(h('i-button', {
                                    on: {
                                        click() {
                                            self.selectTemplate(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '选择模板'));
                            }
                            return h('div', items);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                templateData: [
                    {
                        company: '顺丰速运',
                        height: '139mm',
                        img: image,
                        isTemplate: true,
                        status: true,
                        template: '常用模版1',
                        width: '216mm',
                    },
                    {
                        company: '顺丰速运',
                        template: '未绑定',
                        isTemplate: false,
                    },
                    {
                        company: '顺丰速运',
                        template: '未绑定',
                        isTemplate: false,
                    },
                ],
            };
        },
        methods: {
            addTemplate() {
                const self = this;
                self.$router.push(
                    {
                        path: 'waybill/add',
                    },
                );
            },
            design() {
                const self = this;
                self.$router.push(
                    {
                        path: 'waybill/design',
                    },
                );
            },
            edit() {
                const self = this;
                self.$router.push(
                    {
                        path: 'waybill/edit',
                    },
                );
            },
            remove(index) {
                this.selfTemplateData.splice(index, 1);
            },
            selectTemplate() {
                const self = this;
                self.$router.push(
                    {
                        path: 'waybill/select',
                    },
                );
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-waybill">
            <tabs value="name1">
                <tab-pane label="模版绑定" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.未绑定的物流公司后边会出现“选择模板”按钮，在选择模板页面可以绑定可用的打印模板</p>
                            <p>2.点击“设置”按钮可以设置自定义的内容，包括偏移量和需要显示的项目</p>
                            <p>3.点击“默认”按钮可以设置当前模板为默认打印模板</p>
                            <p>4.点击“解绑”按钮可以解除当前绑定，重新选择其他模板</p>
                        </div>
                        <i-table :columns="templateColumns"
                                 :context="self"
                                 :data="templateData"></i-table>
                    </card>
                </tab-pane>
                <tab-pane label="自建模版" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.未绑定的物流公司后边会出现“选择模板”按钮，在选择模板页面可以绑定可用的打印模板</p>
                            <p>2.点击“设置”按钮可以设置自定义的内容，包括偏移量和需要显示的项目</p>
                            <p>3.点击“默认”按钮可以设置当前模板为默认打印模板</p>
                            <p>4.点击“解绑”按钮可以解除当前绑定，重新选择其他模板</p>
                        </div>
                        <i-button class="first-btn" type="ghost" @click.native="addTemplate">+添加模板</i-button>
                        <i-table :columns="selfTemplateColumns"
                                 :context="self"
                                 :data="selfTemplateData"></i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>