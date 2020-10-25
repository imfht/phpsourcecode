<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                addForm: {
                    company: '',
                    height: '',
                    isUse: '',
                    leftWidth: '',
                    name: '',
                    picture: '',
                    topWidth: '',
                    width: '',
                },
                companyList: [
                    {
                        label: '申通物流',
                        value: '申通物流',
                    },
                    {
                        label: '申通物流',
                        value: '申通物流',
                    },
                ],
                loading: false,
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeLogo() {
                this.addForm.picture = '';
            },
            submit() {
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
                self.addForm.picture = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="order-waybill-edit">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>自建模版-编辑模版</span>
            </div>
            <card :bordered="false">
                <i-form ref="addForm" :model="addForm" :rules="ruleValidate" :label-width="180">
                    <row>
                        <i-col span="10">
                            <form-item label="模板名称">
                                <i-input v-model="addForm.name"></i-input>
                                <p class="tip">运单模版名称，最多10个字</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
                            <form-item label="物流公司">
                                <i-select v-model="addForm.company">
                                    <i-option v-for="item in companyList" :value="item.value"
                                              :key="item">{{ item.label }}</i-option>
                                </i-select>
                                <p class="tip">模板对应的物流公司</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
                            <form-item label="宽度">
                                <i-input v-model="addForm.width"></i-input>
                                <p class="tip">运单宽度，单位为毫米mm</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
                            <form-item label="高度">
                                <i-input v-model="addForm.height"></i-input>
                                <p class="tip">运单高度，单位为毫米mm</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
                            <form-item label="上偏移量">
                                <i-input v-model="addForm.topWidth"></i-input>
                                <p class="tip">运单模版上偏移量，单位为毫米mm</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
                            <form-item label="左偏移量">
                                <i-input v-model="addForm.leftWidth"></i-input>
                                <p class="tip">运单模版左偏移量，单位为毫米mm</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="18">
                            <form-item label="选择图片" prop="picture">
                                <div class="image-preview" v-if="addForm.picture">
                                    <img :src="addForm.picture">
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
                                        v-if="addForm.picture === '' || addForm.picture === null">
                                </upload>
                                <p class="tip">请上传扫描好的运单图片，图片尺寸必须与快递单实际尺寸相符</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="18">
                            <form-item label="启用">
                                <radio-group v-model="addForm.isUse">
                                    <radio label="是"></radio>
                                    <radio label="否"></radio>
                                </radio-group>
                                <p class="tip">请首先设计并测试模板然后再启用，启用后商家可以使用</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="10">
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