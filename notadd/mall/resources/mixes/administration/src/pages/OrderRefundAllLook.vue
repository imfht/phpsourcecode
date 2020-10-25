<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                loading: false,
                form: {
                    applyTime: '2016-12-21  10:31:59',
                    goodsname: '****',
                    handelResult: '同意',
                    handelText: 'jahwuiha',
                    handelTime: '2016-12-21  10:31:59',
                    linePay: '99.00',
                    orderCounts: '99.00',
                    payStyle: '在线支付',
                    refundDescription: 'mm',
                    refundImg: '',
                    refundMoney: '99.00',
                    refundReason: '不要',
                    remarks: '',
                    showAccount: '0.00',
                    showMoney: '99.00',
                    showMount: '0.00',
                    showStyle: '在线支付',
                },
                rules: {
                    remarks: [
                        {
                            message: '信息不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        window.console.log(valid);
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
    <div class="mall-wrap">
        <div class="order-refund-all-look">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>所有记录—查看</span>
                </div>
                <div class="refund-process-content store-information">
                    <card :bordered="false">
                        <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                            <div class="refund-application">
                                <h5>买家退款申请</h5>
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
                                            <form-item label="退款原因">
                                                {{ form.refundReason }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="退款说明">
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
                                <h5>商家退款处理</h5>
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
                            <div class="order-information">
                                <h5>平台退款审核</h5>
                                <div class="order-pay-content refund-module">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="平台确认">
                                                {{ form.payStyle }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="处理备注">
                                                ￥{{ form.orderCounts }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="处理时间">
                                                ￥{{ form.linePay }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="order-information">
                                <h5>退款详细</h5>
                                <div class="order-pay-content refund-module">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="支付方式">
                                                {{ form.showStyle }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="在线退款金额">
                                                ￥{{ form.showMoney }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="预存款金额">
                                                ￥{{ form.showAccount }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="18">
                                            <form-item label="充值卡金额">
                                                ￥{{ form.showMount }}
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