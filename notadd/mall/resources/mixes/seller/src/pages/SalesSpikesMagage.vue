<script>
    import image1 from '../../../seller/src/assets/images/img_logo.png';
    import image from '../../../seller/src/assets/images/adv.jpg';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                addBtn: true,
                form: {
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
                            key: 'pre_price',
                            title: '原价',
                            width: 180,
                        },
                        {
                            align: 'center',
                            key: 'price',
                            render(h, data) {
                                if (self.searchResult) {
                                    return h('i-input', {
                                        props: {
                                            type: 'ghost',
                                            value: data.row.price,
                                        },
                                    });
                                }
                                if (!self.searchResult) {
                                    return h('div', data.row.price);
                                }
                                return '';
                            },
                            title: '优惠价格',
                            width: 180,
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
                                    }, '删除'),
                                ]);
                            },
                            title: '操作',
                            width: 180,
                        },
                    ],
                    endTime: '2018-02-22',
                    list: [
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                        {
                            image: image1,
                            name: '可用十年 创意椭圆不锈钢肥皂(带底托精装) 神奇除味去',
                            pre_price: '￥388.00',
                            price: '388.00',
                        },
                    ],
                    name: '秒杀促销',
                    pictureList: [
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: true,
                        },
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: true,
                        },
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: true,
                        },
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: false,
                        },
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: false,
                        },
                        {
                            img: image,
                            name: 'Sony/索尼SGP512CNWIFI 32GB 平板电脑 32G 官方标配',
                            price: '￥1999.00',
                            status: false,
                        },
                    ],
                    search: '',
                    startTime: '2017-07-20',
                },
                loading: false,
                rules: {},
                searchResult: false,
            };
        },
        methods: {
            addData() {
                this.addBtn = false;
                this.searchResult = true;
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            remove(index) {
                this.form.list.splice(index, 1);
            },
            removeProduct(index) {
                this.form.pictureList.splice(index, 1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="sales-spikes-manage">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>活动列表-管理活动</span>
            </div>
            <card :bordered="false">
                <div class="prompt-box">
                    <p>提示</p>
                    <p>1.限时秒杀商品的时间段不能重叠</p>
                    <p>2.点击添加商品按钮可以搜索并添加参加活动的商品，点击删除按钮可以删除该商品</p>
                </div>
                <i-form :label-width="180" :model="form" ref="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="活动名称">
                                {{ form.name }}
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="开始时间">
                                {{ form.startTime }}
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="结束时间">
                                {{ form.endTime }}
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="24">
                            <form-item label="选择商品" prop="goods">
                                <i-table :columns="form.columns"
                                         :context="self"
                                         :data="form.list">
                                </i-table>
                                <div class="goods-body-header-right" v-if="addBtn">
                                    <i-button type="ghost" @click.native="addData">+添加商品</i-button>
                                </div>
                                <div v-if="searchResult">
                                    <div class="goods-body-header-right">
                                        <i-input v-model="form.search" placeholder="请输入商品名称/spu">
                                            <i-button slot="append" type="primary">搜索</i-button>
                                        </i-input>
                                        <span class="search-tip">不输入名称直接搜索将显示店内所有商品</span>
                                    </div>
                                    <div class="search-result-content">
                                        <h5>搜索结果</h5>
                                        <div>
                                            <div v-for="(item, index) in form.pictureList" class="picture-check">
                                                <img :src="item.img" alt="" @click="lookPicture(item)">
                                                <p class="name">{{ item.name}}</p>
                                                <p class="price">价格：{{ item.price}}</p>
                                                <i-button type="error" v-if="item.status === true"
                                                          @click.native="removeProduct(index)">从秒杀活动中移除</i-button>
                                                <i-button type="ghost" v-if="item.status === false">添加至秒杀活动</i-button>
                                            </div>
                                            <div class="page">
                                                <page :total="100" show-elevator></page>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form-item>
                        </i-col>
                    </row>
                    <row v-if="searchResult">
                        <i-col span="12">
                            <form-item>
                                <i-button :loading="loading" type="primary" @click.native="submit">
                                    <span v-if="!loading">确认提交</span>
                                    <span v-else>正在提交…</span>
                                </i-button>
                            </form-item>
                        </i-col>
                    </row>
                </i-form>
            </card>
        </div>
    </div>
</template>
