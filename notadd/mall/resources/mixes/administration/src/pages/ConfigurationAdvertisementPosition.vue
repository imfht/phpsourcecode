<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                form: {
                    heightNum: '',
                    logo: '',
                    name: '',
                    province: '',
                    showStyle: 'style1',
                    switchStatus: true,
                    widthNum: '',
                },
                loading: false,
                province: [
                    {
                        label: '图片',
                        value: '1',
                    },
                    {
                        label: '图片1',
                        value: '2',
                    },
                ],
                rules: {
                    heightNum: [
                        {
                            message: '高度不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    logo: [
                        {
                            message: '广告位默认图片不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    name: [
                        {
                            message: '名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    widthNum: [
                        {
                            message: '宽度不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
            };
        },
        methods: {
            removeLogo() {
                this.form.logo = '';
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
            uploadBefore() {
                injection.loading.start();
            },
            uploadError(error, data) {
                const self = this;
                injection.loading.error();
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
                injection.loading.finish();
                self.$notice.open({
                    title: data.message,
                });
                self.form.logo = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="configuration-advertisement-add-position">
            <div class="edit-link-title">
                <i-button type="text">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>广告管理—新增广告位</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <row>
                        <i-col span="12">
                            <form-item label="名称" prop="name">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="类别">
                                <i-select v-model="form.province" placeholder="请选择">
                                    <i-option :key="item"
                                              :value="item.value"
                                              v-for="item in province">
                                        {{ item.label }}
                                    </i-option>
                                </i-select>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="18">
                            <form-item label="展示方式">
                                <radio-group v-model="form.showStyle">
                                    <radio label="style1">
                                        <span>可以发布多条广告并随机展示</span>
                                    </radio>
                                    <radio label="style2">
                                        <span>只允许发布并展示一条广告</span>
                                    </radio>
                                </radio-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="宽度" prop="widthNum">
                                <i-input v-model="form.widthNum"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="高度" prop="heightNum">
                                <i-input v-model="form.heightNum"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="高度" prop="heightNum">
                                <i-input v-model="form.heightNum"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="广告位默认图片上传" prop="logo">
                                <div class="image-preview" v-if="form.logo">
                                    <img :src="form.logo">
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
                                        v-if="form.logo === '' || form.logo === null">
                                </upload>
                                <p>系统支持的图片格式为：gif、jpg、jpeg、png</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="状态">
                                <i-switch size="large" v-model="form.switchStatus">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
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
                </i-form>
            </card>
        </div>
    </div>
</template>