<script>
    import image1 from '../assets/images/img_logo.png';

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
                        align: 'center',
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'evaluationPeople',
                        title: '评价人',
                        width: 190,
                    },
                    {
                        align: 'center',
                        render(h) {
                            return h('div', [
                                h('icon', {
                                    props: {
                                        type: 'star',
                                    },
                                    style: {
                                        color: '#f7d55f',
                                    },
                                }),
                                h('icon', {
                                    props: {
                                        type: 'star',
                                    },
                                    style: {
                                        color: '#f7d55f',
                                    },
                                }),
                                h('icon', {
                                    props: {
                                        type: 'star',
                                    },
                                    style: {
                                        color: '#f7d55f',
                                    },
                                }),
                                h('icon', {
                                    props: {
                                        type: 'star',
                                    },
                                    style: {
                                        color: '#f7d55f',
                                    },
                                }),
                                h('icon', {
                                    props: {
                                        type: 'star',
                                    },
                                    style: {
                                        color: '#f7d55f',
                                    },
                                }),
                            ]);
                        },
                        title: '评分',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'evaluationContent',
                        title: '评价内容',
                        width: 400,
                    },
                    {
                        align: 'center',
                        key: 'baskImage',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.baskImage,
                                            },
                                        });
                                    },
                                    default() {
                                        return h('icon', {
                                            props: {
                                                type: 'image',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '晒单图片',
                        width: 300,
                    },
                    {
                        align: 'center',
                        key: 'evaluationTime',
                        title: '评价时间',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'evaluationProduct',
                        title: '被评商品',
                        width: 300,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.remove(data.index);
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '删除');
                        },
                        title: '操作',
                        width: 150,
                    },
                ],
                list: [
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                    {
                        baskImage: image1,
                        evaluationContent: '时尚但不易过时，高上大，还配有眼睛盒，发货速度',
                        evaluationPeople: 'pingjia',
                        evaluationProduct: '黎明眼镜GUCCI古驰太阳',
                        evaluationTime: '2016-12-23',
                    },
                ],
                searchCategory: '',
                searchList: [
                    {
                        label: '评价人',
                        value: '1',
                    },
                    {
                        label: '被评商品',
                        value: '2',
                    },
                ],
                searchWord: '',
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
    <div class="mall-wrap">
        <div class="order-evaluation">
            <tabs value="name1">
                <tab-pane label="评价列表" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>买家可以在订单完成后对订单商品进行评价操作，评价信息将显示在对应的商品页面</p>
                        </div>
                        <div class="store-body">
                            <div class="store-body-header">
                                <i-button class="export-btn" type="ghost" @click="deleteData">批量删除</i-button>
                                <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                                <div class="store-body-header-right">
                                    <i-input v-model="searchWord">
                                        <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                            <i-option v-for="item in searchList"
                                                      :key="item"
                                                      :value="item.value" >{{ item.label }}</i-option>
                                        </i-select>
                                        <i-button slot="append" type="primary">搜索</i-button>
                                    </i-input>
                                </div>
                            </div>
                            <i-table class="shop-table"
                                     :context="self"
                                     :data="list"
                                     :columns="columns"
                                     highlight-row ref="evaluationListTable" ></i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>