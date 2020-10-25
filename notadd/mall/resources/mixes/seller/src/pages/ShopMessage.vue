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
                action: `${window.api}/mall/admin/upload`,
                applyColumns: [
                    {
                        align: 'center',
                        key: 'applyTime',
                        title: '申请时间',
                    },
                    {
                        align: 'center',
                        key: 'charges',
                        title: '收费标准(元/年)',
                    },
                    {
                        align: 'center',
                        key: 'renewalTime',
                        title: '续签时长(年)',
                    },
                    {
                        align: 'center',
                        key: 'payMoney',
                        title: '付款金额(年)',
                    },
                    {
                        align: 'center',
                        key: 'validPeriod',
                        title: '续签起止有效期',
                    },
                    {
                        align: 'center',
                        key: 'voucher',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.voucher,
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
                        title: '付款凭证',
                    },
                    {
                        align: 'center',
                        key: 'applyStatus',
                        title: '状态',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.remove(data.index);
                                    },
                                },
                                props: {
                                    class: 'delete-ad',
                                    type: 'ghost',
                                },
                            }, '删除');
                        },
                        title: '操作',
                    },
                ],
                applyData: [
                    {
                        applyStatus: '续签成功',
                        applyTime: '2017-04-01',
                        charges: 300,
                        payMoney: '900.00',
                        renewalTime: 3,
                        validPeriod: '2017/4/1-2020/4/1',
                        voucher: image,
                    },
                ],
                isShow: false,
                levelList: [
                    {
                        label: '钻石店铺  100.00元/年',
                        value: '1',
                    },
                    {
                        label: '钻石店铺  200.00元/年',
                        value: '2',
                    },
                ],
                loading: false,
                self: this,
                shopApply: {
                    level: '',
                    renewalTime: '',
                },
                shopMessage: {
                    businessScope: '服饰',
                    companyAddress: '高新二路220号国土资源大厦公寓楼',
                    companyEmail: '336645564@qq.com',
                    companyLogo: image,
                    companyName: '本初网络',
                    companyPhone: '029-2222365',
                    companyPlace: '陕西省西安市雁塔区',
                    contactName: 'ddd',
                    contactPhone: '13626468989',
                    employeesNum: '50',
                    licenseNo: '3326568946464',
                    licensePicture: image,
                    licensePlace: '陕西省西安市雁塔区',
                    licenseTime: '2010/10/0-2020/12/20',
                    registered: '10w',
                },
                shopRenewal: {
                    image: '',
                    money: '100.00',
                    remarks: '',
                },
                timeList: [
                    {
                        label: '1年',
                        value: '1',
                    },
                    {
                        label: '2年',
                        value: '2',
                    },
                ],
            };
        },
        methods: {
            remove(index) {
                this.applyData.splice(index, 1);
            },
            removeImage() {
                this.shopRenewal.image = '';
            },
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.shopApply.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
                        this.$refs.shopApply.style.display = 'none';
                        this.isShow = true;
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            submitRenewal() {
                const self = this;
                self.loading = true;
                self.$refs.shopRenewal.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
                        this.isShow = false;
                    } else {
                        self.loading = false;
                        self.$notice.error({
                            title: '请正确填写设置信息！',
                        });
                    }
                });
            },
            uploadBefore() {
                this.$loading.start();
            },
            uploadError(error, data) {
                const self = this;
                self.$loading.error();
                if (typeof data.message === 'object') {
                    for (const p in data.message) {
                        self.$notice.error({
                            title: data.message[p],
                        });
                    }
                } else {
                    self.$notice.error({
                        title: data.message,
                    });
                }
            },
            uploadFormatError(file) {
                this.$notice.warning({
                    title: '文件格式不正确',
                    desc: `文件 ${file.name} 格式不正确`,
                });
            },
            uploadSuccess(data) {
                const self = this;
                self.$loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.shopRenewal.image = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-message">
            <tabs value="name1">
                <tab-pane label="店铺信息" name="name1">
                    <card :bordered="false">
                        <i-form :label-width="200">
                            <div class="library-application">
                                <h5>商品基本信息</h5>
                                <div>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="公司名称">
                                                {{ shopMessage.companyName }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="所在地">
                                                {{ shopMessage.companyPlace }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="详细地址">
                                                {{ shopMessage.companyAddress }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="公司电话">
                                                {{ shopMessage.companyPhone }}
                                            </form-item>
                                        </i-col>
                                        <i-col  span="12">
                                            <form-item label="电子邮箱">
                                                {{ shopMessage.companyEmail }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="员工总数">
                                                {{ shopMessage.employeesNum }}人
                                            </form-item>
                                        </i-col>
                                        <i-col  span="12">
                                            <form-item label="注册资金">
                                                {{ shopMessage.registered }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="联系人姓名">
                                                {{ shopMessage.contactName }}
                                            </form-item>
                                        </i-col>
                                        <i-col  span="12">
                                            <form-item label="联系人电话">
                                                {{ shopMessage.contactPhone }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                            <div class="library-application">
                                <h5>营业执照信息（副本）</h5>
                                <div>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="营业执照号">
                                                {{ shopMessage.companyName }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="营业执照所在地">
                                                {{ shopMessage.companyPlace }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="营业执照有效期">
                                                {{ shopMessage.companyAddress }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="法定经营范围">
                                                {{ shopMessage.companyPhone }}
                                            </form-item>
                                        </i-col>
                                    </row>
                                    <row>
                                        <i-col  span="12">
                                            <form-item label="营业执照电子版">
                                                <img :src="shopMessage.companyLogo" alt="">
                                            </form-item>
                                        </i-col>
                                    </row>
                                </div>
                            </div>
                        </i-form>
                    </card>
                </tab-pane>
                <tab-pane label="申请续签" name="name2">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>1.店铺到期前30天可以申请店铺续签</p>
                            <p>2.您的店铺已签约至2017-05-31，自2017-05-01起30天内可以申请续签</p>
                        </div>
                        <i-table :columns="applyColumns"
                                 :context="self"
                                 :data="applyData"
                                 highlight-row>
                        </i-table>
                        <i-form ref="shopApply" :model="shopApply" :rules="ruleValidate"
                                :label-width="200" v-show="this.applyData.length === 0">
                            <div class="apply-renewal">
                                <row>
                                    <i-col  span="12">
                                        <form-item label="申请店铺等级">
                                            <i-select v-model="shopApply.level">
                                                <i-option v-for="item in levelList" :value="item.value"
                                                          :key="item">{{ item.label }}</i-option>
                                            </i-select>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col  span="12">
                                        <form-item label="申请续签时长">
                                            <i-select v-model="shopApply.renewalTime">
                                                <i-option v-for="item in timeList" :value="item.value"
                                                          :key="item">{{ item.label }}</i-option>
                                            </i-select>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="">
                                            <i-button @click.native="submit" type="primary">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </div>
                        </i-form>
                        <i-form ref="shopRenewal" :model="shopRenewal" :rules="ruleValidate"
                                :label-width="200" v-if="isShow">
                            <div class="apply-renewal">
                                <row>
                                    <i-col  span="12">
                                        <form-item label="缴费金额">
                                            {{ shopRenewal.money }}元
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col  span="12">
                                        <form-item label="上传付款凭证">
                                            <div class="image-preview" v-if="shopRenewal.image">
                                                <img :src="shopRenewal.image">
                                                <icon type="close" @click.native="removeImage"></icon>
                                            </div>
                                            <upload :action="action"
                                                    :before-upload="uploadBefore"
                                                    :format="['jpg','jpeg','png']"
                                                    :headers="{
                                                        Authorization: `Bearer ${$store.state.token.access_token}`
                                                    }"
                                                    :max-size="2048"
                                                    :on-error="uploadError"
                                                    :on-format-error="uploadFormatError"
                                                    :on-success="uploadSuccess"
                                                    ref="upload"
                                                    :show-upload-list="false"
                                                    v-if="shopRenewal.image === '' || shopRenewal.image === null">
                                            </upload>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col  span="12">
                                        <form-item label="备注">
                                            <i-input type="textarea" v-model="shopRenewal.remarks"
                                                     :autosize="{minRows: 3,maxRows: 5}"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="">
                                            <i-button :loading="loading" @click.native="submitRenewal" type="primary">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </div>
                        </i-form>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>
