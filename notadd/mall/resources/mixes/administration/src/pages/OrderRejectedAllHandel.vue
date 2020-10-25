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
                    refundNum: 2,
                    refundReason: '不要',
                    remarks: '',
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
        <div class="order-rejected-all-handel">
            <div class="store-refund-process">
                <div class="edit-link-title">
                    <i-button type="text" @click.native="goBack">
                        <icon type="chevron-left"></icon>
                    </i-button>
                    <span>所有记录—处理</span>
                </div>
                <div class="refund-process-content store-information">
                    <card :bordered="false">
                        <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                            <div class="refund-application">
                                <h5>买家退货退款申请</h5>
                                <div class="application-content refund-module">
                                    <row>
                                        <i-col span="12">
                                            <form-item label="申请时间">
                                                {{ form.applyTime }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="商品名称">
                                                {{ form.goodsname }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="退款金额">
                                                ￥{{ form.refundMoney }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="退货原因">
                                                {{ form.refundReason }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="退货数量">
                                                {{ form.refundNum }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="退货说明">
                                                {{ form.refundDescription }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
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
                                        <i-col span="12">
                                            <form-item label="审核结果">
                                                {{ form.handelResult }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="处理备注">
                                                {{ form.handelText }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="处理时间">
                                                {{ form.handelTime }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="order-information">
                                <h5>订单支付信息</h5>
                                <div class="order-pay-content refund-module">
                                    <row>
                                        <i-col span="12">
                                            <form-item label="支付方式">
                                                {{ form.payStyle }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="订单总额">
                                                ￥{{ form.orderCounts }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item label="在线支付金额">
                                                ￥{{ form.linePay }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="refund-review">
                                <h5>平台退款审核</h5>
                                <div class="review-content refund-module">
                                    <row>
                                        <i-col span="18">
                                            <form-item label="备注信息" prop="remarks" class="remark-input">
                                                <i-input v-model="form.remarks" type="textarea"
                                                         :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                                <p>系统默认退款到“站内余额”，如果“在线退款”到原支付账号，建议在备注里说明，
                                                    方便核对。</p>
                                            </form-item>
                                            <p></p>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col span="12">
                                            <form-item>
                                                <i-button :loading="loading" type="primary" @click.native="submit">
                                                    <span v-if="!loading">确认提交</span>
                                                    <span v-else>正在提交…</span>
                                                </i-button>
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