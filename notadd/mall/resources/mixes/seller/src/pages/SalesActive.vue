<script>
    import image1 from '../../../seller/src/assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                columns: [
                    {
                        key: 'name',
                        render(h, data) {
                            return h('div', {
                                class: {
                                    'goods-name-row': true,
                                },
                            }, [
                                h('div', {
                                    class: {
                                        img: true,
                                    },
                                }, [
                                    h('img', {
                                        domProps: {
                                            src: data.row.image,
                                        },
                                    }),
                                ]),
                                h('div', {
                                    class: {
                                        'right-text': true,
                                    },
                                }, [
                                    h('p', data.row.name),
                                ]),
                            ]);
                        },
                        title: '商品名称',
                    },
                    {
                        align: 'center',
                        key: 'startTime',
                        title: '发货时间',
                    },
                    {
                        align: 'center',
                        key: 'price',
                        title: '价格',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
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
                                    style: {
                                        marginLeft: '10px',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                        width: 180,
                    },
                ],
                list: [
                    {
                        image: image1,
                        name: 'Apple/苹果 配备 Retina 5K 显示屏的 iMac I7 16G 512G固态 29寸',
                        price: '￥99.00',
                        startTime: '2018-04-23',
                    },
                    {
                        image: image1,
                        name: 'Apple/苹果 配备 Retina 5K 显示屏的 iMac I7 16G 512G固态 29寸',
                        price: '￥99.00',
                        startTime: '2018-04-23',
                    },
                    {
                        image: image1,
                        name: 'Apple/苹果 配备 Retina 5K 显示屏的 iMac I7 16G 512G固态 29寸',
                        price: '￥99.00',
                        startTime: '2018-04-23',
                    },
                    {
                        image: image1,
                        name: 'Apple/苹果 配备 Retina 5K 显示屏的 iMac I7 16G 512G固态 29寸',
                        price: '￥99.00',
                        startTime: '2018-04-23',
                    },
                ],
            };
        },
        methods: {
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="sales-active">
            <tabs value="name1">
                <tab-pane label="商品列表" name="name1">
                    <card :bordered="false">
                        <div class="spikes-content">
                            <div class="goods-body-header">
                                <router-link to="/seller/sales/active/create" class="first-btn">
                                    <i-button type="ghost">+添加商品</i-button>
                                </router-link>
                            </div>
                            <i-table class="goods-table"
                                     :columns="columns"
                                     :context="self"
                                     :data="list">
                            </i-table>
                            <div class="page">
                                <page :total="100" show-elevator></page>
                            </div>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>
