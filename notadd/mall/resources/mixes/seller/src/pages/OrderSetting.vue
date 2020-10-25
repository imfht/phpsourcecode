<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                addAddress: false,
                addForm: {
                    address: '',
                    name: '',
                    phone: '',
                    province: [],
                },
                address: [
                    {
                        address: '陕西省西安市高新区详细地址',
                        name: '本初1',
                        phone: '1234544444',
                    },
                    {
                        address: '陕西省西安市高新区详细地址1',
                        name: '本初2',
                        phone: '1234544441',
                    },
                    {
                        address: '陕西省西安市高新区详细地址2',
                        name: '本初3',
                        phone: '1234544443',
                    },
                ],
                addValidate: {
                    address: [
                        {
                            message: '地址不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '联系人不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    province: [
                        {
                            message: '地区不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                defaultAddress: {
                    address: '陕西省西安市高新区详细地址',
                    name: '本初1',
                    phone: '1234544444',
                },
                editAddress: false,
                editForm: {
                    address: '',
                    name: '',
                    phone: '',
                    province: '北京市',
                },
                editValidate: {
                    address: [
                        {
                            message: '地址不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    name: [
                        {
                            message: '联系人不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                    province: [
                        {
                            message: '地区不能为空',
                            required: true,
                            trigger: 'blur',
                        },
                    ],
                },
                loading: false,
                logistics: [
                    {
                        name: '顺丰快递',
                    },
                    {
                        name: '顺丰快递1',
                    },
                    {
                        name: '顺丰快递2',
                    },
                    {
                        name: '顺丰快递3',
                    },
                    {
                        name: '顺丰快递4',
                    },
                    {
                        name: '顺丰快递5',
                    },
                    {
                        name: '顺丰快递6',
                    },
                    {
                        name: '顺丰快递7',
                    },
                ],
                logisticsForm: {
                    money: '',
                },
                logisticsSelect: [],
                printForm: {
                    message: '',
                    picture: '',
                },
                provinceData: [
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '故宫',
                                        value: '故宫',
                                    },
                                    {
                                        label: '天坛',
                                        value: '天坛',
                                    },
                                    {
                                        label: '王府井',
                                        value: '王府井',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅',
                                        value: '安全座椅',
                                    },
                                ],
                                label: '童车童床',
                                value: '北京1',
                            },
                            {
                                label: '营养辅食',
                                value: '营养辅食',
                            },
                            {
                                label: '尿裤湿巾',
                                value: '尿裤湿巾',
                            },
                        ],
                        label: '北京',
                        value: '北京',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '婴儿推车1',
                                        value: '婴儿推车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                    {
                                        label: '婴儿推车3',
                                        value: '婴儿推车3',
                                    },
                                    {
                                        label: '电动车',
                                        value: '电动车',
                                    },
                                    {
                                        label: '安全座椅4',
                                        value: '安全座椅4',
                                    },
                                ],
                                label: '狮子林',
                                value: '狮子林',
                            },
                            {
                                children: [
                                    {
                                        label: '夫子庙',
                                        value: '夫子庙',
                                    },
                                    {
                                        label: '夫子庙1',
                                        value: '夫子庙1',
                                    },
                                ],
                                label: '南京',
                                value: '南京',
                            },
                            {
                                children: [
                                    {
                                        label: '车1',
                                        value: '车1',
                                    },
                                    {
                                        label: '自行车2',
                                        value: '自行车2',
                                    },
                                ],
                                label: '拙政园',
                                value: '拙政园',
                            },
                        ],
                        label: '江苏',
                        value: '江苏',
                    },
                ],
                self: this,
            };
        },
        methods: {
            edit() {
                this.editAddress = true;
            },
            handleChange(value, selectedData) {
                this.editForm.province = selectedData.map(o => o.label).join('>');
            },
            newAddAddress() {
                this.addAddress = true;
            },
            remove(index) {
                this.address.splice(index, 1);
            },
            removeLogo() {
                this.printForm.picture = '';
            },
            submitAddAddress() {
                const self = this;
                self.loading = true;
                self.$refs.addForm.validate(valid => {
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
            submitEditAddress() {
                const self = this;
                self.loading = true;
                self.$refs.editForm.validate(valid => {
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
            submitLogistics() {
                const self = this;
                self.loading = true;
                self.$refs.logisticsForm.validate(valid => {
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
            submitPrint() {
                const self = this;
                self.loading = true;
                self.$refs.printForm.validate(valid => {
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
                self.printForm.picture = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-setting">
            <tabs value="name1">
                <tab-pane label="发货地址" name="name1">
                    <card :bordered="false">
                        <i-button class="first-btn" type="ghost" @click.native="newAddAddress">新增地址</i-button>
                        <div class="table-content">
                            <div class="table-title">
                                <span>选择默认</span>
                                <span>联系人</span>
                                <span>发货地址</span>
                                <span>电话</span>
                                <span>操作</span>
                            </div>
                            <radio-group v-model="defaultAddress" vertical>
                                <radio :label="item" v-for="(item, index) in address">
                                    <span>{{ item.name }}</span>
                                    <span>{{ item.address }}</span>
                                    <span>{{ item.phone }}</span>
                                    <span>
                                        <i-button type="ghost" size="small" @click.native="edit(index)">编辑</i-button>
                                        <i-button class="delete-ad" type="ghost" size="small"
                                                  @click.native="remove(index)">删除</i-button>
                                    </span>
                                </radio>
                            </radio-group>
                        </div>
                    </card>
                    <modal
                            v-model="addAddress"
                            title="新增地址" class="upload-picture-modal">
                        <div>
                            <i-form ref="addForm" :model="addForm" :rules="addValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="联系人" prop="name">
                                            <i-input v-model="addForm.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="所在地区" prop="province">
                                            <cascader :data="provinceData" trigger="hover"
                                                      v-model="addForm.province"></cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="详细地址" prop="address">
                                            <i-input v-model="addForm.address"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="电话" prop="phone">
                                            <i-input v-model="addForm.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitAddAddress">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                    <modal
                            v-model="editAddress"
                            title="编辑地址" class="upload-picture-modal edit-address-modal">
                        <div>
                            <i-form ref="editForm" :model="editForm" :rules="editValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="联系人" prop="name">
                                            <i-input v-model="editForm.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row class="edit-address-flex">
                                    <i-col span="18">
                                        <form-item label="所在地区" prop="province">
                                            <span>{{ editForm.province }}</span>
                                            <cascader :data="provinceData" trigger="hover" @on-change="handleChange"
                                                      v-model="editForm.province"></cascader>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="详细地址" prop="address">
                                            <i-input v-model="editForm.address"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="电话" prop="phone">
                                            <i-input v-model="editForm.phone"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitEditAddress">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                </tab-pane>
                <tab-pane label="默认物流公司" name="name2">
                    <card :bordered="false">
                        <i-form ref="logistics" :model="logistics" :rules="ruleValidate">
                            <checkbox-group v-model="logisticsSelect">
                                <checkbox :label="item.name" v-for="item in logistics">
                                    <span>{{ item.name }}</span>
                                </checkbox>
                            </checkbox-group>
                            <row>
                                <i-col span="12">
                                    <form-item>
                                        <i-button :loading="loading" type="primary">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </card>
                </tab-pane>
                <tab-pane label="免运费额度" name="name3">
                    <card :bordered="false">
                        <i-form ref="logisticsForm" :model="logisticsForm" :rules="ruleValidate" label-width="180">
                            <form-item label="免运费额度">
                                <row class="input-margin">
                                    <i-col span="2">
                                        <i-input v-model="logisticsForm.money"></i-input>
                                    </i-col>
                                    <i-col span="2">元</i-col>
                                </row>
                                <p class="tip">默认为0，标识不设置免运费额度，大于0标识购买金额超出该值后将免运费</p>
                            </form-item>
                            <row>
                                <i-col span="18">
                                    <form-item>
                                        <i-button :loading="loading" type="primary" @click.native="submitLogistics">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </card>
                </tab-pane>
                <tab-pane label="发货单打印" name="name4">
                    <card :bordered="false">
                        <i-form ref="printForm" :model="printForm" :rules="editValidate" :label-width="180">
                            <row>
                                <i-col span="14">
                                    <form-item label="备注信息" prop="message">
                                        <i-input type="textarea" :autosize="{minRows: 3,maxRows: 5}"
                                                 v-model="printForm.message"></i-input>
                                        <p class="tip">打印备注信息将会出现在打印订单的下方位置，用于注明店铺简介或发货、
                                            退换货相关规则等；内容不得超过100字</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="14">
                                    <form-item label="印章图片" prop="picture">
                                        <div class="image-preview" v-if="printForm.picture">
                                            <img :src="printForm.picture">
                                            <icon type="close" @click.native="removeLogo"></icon>
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
                                                v-if="printForm.picture === '' || printForm.picture === null">
                                        </upload>
                                        <p class="tip">印章图片将出现在打印订单的右下角位置，请选在120像素x120像素大小透明
                                            GIF/PNG格式图片上传作为您店铺的电子印章使用</p>
                                    </form-item>
                                </i-col>
                            </row>
                            <row>
                                <i-col span="20">
                                    <form-item>
                                        <i-button :loading="loading" type="primary" @click.native="submitPrint">
                                            <span v-if="!loading">确认提交</span>
                                            <span v-else>正在提交…</span>
                                        </i-button>
                                    </form-item>
                                </i-col>
                            </row>
                        </i-form>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>