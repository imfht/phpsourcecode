<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                goods: {
                    preForm: [
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                    ],
                },
                orders: {
                    preForm: [
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                        {
                            endPrice: '',
                            startPrice: '',
                        },
                    ],
                },
            };
        },
        methods: {
            addpreArea() {
                this.goods.preForm.push(
                    {
                        endPrice: '',
                        startPrice: '',
                    },
                );
            },
            addOrderArea() {
                this.orders.preForm.push(
                    {
                        endPrice: '',
                        startPrice: '',
                    },
                );
            },
            deleteArea(index) {
                this.goods.preForm.splice(index, 1);
            },
            deleteOrderArea(index) {
                this.orders.preForm.splice(index, 1);
            },
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submitPrice() {
                const self = this;
                self.loading = true;
                self.$refs.goods.validate(valid => {
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
            submitOrder() {
                const self = this;
                self.loading = true;
                self.$refs.orders.validate(valid => {
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
        <div class="statistics-goods-setting">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>价格销量-设置价格区间</span>
                </div>
                <card :bordered="false">
                    <div class="prompt-box">
                        <p>提示</p>
                        <p>设置商品价格区间，当对商品价格进行相关统计时按照以下设置的价格区间进行统计和显示</p>
                        <p>设置价格区间的几点建议：一、建议设置的第一个价格区间起始额为0：二、价格区间应该设置完整，
                            不要缺少任何一个起始额和结束额；三、价格区间数值应连贯，例如0~100、101~200</p>
                    </div>
                    <tabs type="card">
                        <tab-pane label="商品价格区间">
                            <div class="goods-price-area">
                                <i-form ref="goods" :model="goods" :rules="ruleValidate" :label-width="180">
                                    <form-item v-for="(item, index) in goods.preForm">
                                        <row>
                                            <i-col span="2" class="price-width">起始额</i-col>
                                            <i-col span="2" class="input-width">
                                                <i-input v-model="item.startPrice"></i-input>
                                            </i-col>
                                            <i-col span="1">元</i-col>
                                            <i-col span="2" class="price-width">结束额</i-col>
                                            <i-col span="2" class="input-width">
                                                <i-input v-model="item.endPrice"></i-input>
                                            </i-col>
                                            <i-col span="1">元</i-col>
                                            <i-col span="14">
                                                <i-button @click.native="deleteArea(index)" v-if="index !== 0"
                                                          class="delete-color" type="ghost">刪除</i-button>
                                            </i-col>
                                        </row>
                                    </form-item>
                                    <form-item>
                                        <i-button @click.native="addpreArea"  class="button-style"
                                                  type="ghost">+添加区间</i-button>
                                    </form-item>
                                    <form-item>
                                        <i-button class="button-style" @click.native="priceSubmit"
                                                  :loading="loading" type="primary">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-form>
                            </div>
                        </tab-pane>
                        <tab-pane label="订单金额区间">
                            <div class="goods-price-area">
                                <i-form ref="orders" :model="orders" :rules="ruleValidate" :label-width="180">
                                    <form-item v-for="(item, index) in orders.preForm">
                                        <row>
                                            <i-col span="2" class="price-width">起始额</i-col>
                                            <i-col span="2" class="input-width">
                                                <i-input v-model="item.startPrice"></i-input>
                                            </i-col>
                                            <i-col span="1">元</i-col>
                                            <i-col span="2" class="price-width">结束额</i-col>
                                            <i-col span="2" class="input-width">
                                                <i-input v-model="item.endPrice"></i-input>
                                            </i-col>
                                            <i-col span="1">元</i-col>
                                            <i-col span="14">
                                                <i-button class="delete-color" @click.native="deleteOrderArea(index)"
                                                          type="ghost" v-if="index !== 0">刪除</i-button>
                                            </i-col>
                                        </row>
                                    </form-item>
                                    <form-item>
                                        <i-button class="button-style" @click.native="addOrderArea"
                                                  type="ghost" >+添加区间</i-button>
                                    </form-item>
                                    <form-item>
                                        <i-button class="button-style"  @click.native="orderSubmit"
                                                  :loading="loading" type="primary">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-form>
                            </div>
                        </tab-pane>
                    </tabs>
                </card>
            </div>
        </div>
    </div>
</template>
