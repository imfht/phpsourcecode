<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                form: {
                    applyTime: '2016-12-21  10:31:59',
                    goodsname: '****',
                    handelResult: '同意',
                    handelText: 'jahwuiha',
                    handelTime: '2016-12-21  10:31:59',
                    refundDescription: 'mm',
                    refundImg: '',
                    refundMoney: '99.00',
                    refundNum: 2,
                    refundReason: '不要',
                },
                rules: {},
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="order-rejected-look-reject">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>退款管理—查看</span>
                </div>
                <div class="refund-process-content store-information">
                    <card :bordered="false">
                        <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                            <div class="refund-application">
                                <h5>买家退货退款申请</h5>
                                <div class="application-content refund-module">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="申请时间">
                                                {{ form.applyTime }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="商品名称">
                                                {{ form.goodsname }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="退款金额">
                                                ￥{{ form.refundMoney }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="退货原因">
                                                {{ form.refundReason }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="退货数量">
                                                {{ form.refundNum }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="退货说明">
                                                {{ form.refundDescription }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="凭证上传">
                                                {{ form.refundImg }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="refund-handel">
                                <h5>商家退款退货处理</h5>
                                <div class="handel-content refund-module">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="审核结果">
                                                {{ form.handelResult }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="处理备注">
                                                {{ form.handelText }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="处理时间">
                                                {{ form.handelTime }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                        </i-form>
                    </card>
                </div>
            </div>
        </div>
    </div>
</template>