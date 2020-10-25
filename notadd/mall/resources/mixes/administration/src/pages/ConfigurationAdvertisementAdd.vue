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
                    adname: '',
                    endtime: '',
                    linkAddress: '',
                    logo: '',
                    position: '',
                    starttime: '',
                },
                loading: false,
                position: [
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
                    adname: [
                        {
                            message: '广告名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    logo: [
                        {
                            message: '图片上传不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    position: [
                        {
                            message: '广告位不能为空',
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
        <div class="configuration-advertisement-add">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>广告管理—新增广告</span>
            </div>
            <card :bordered="false">
                <i-form :label-width="200" ref="form" :model="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="广告名称" prop="adname">
                                <i-input v-model="form.adname"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="选择广告位">
                                <i-select placeholder="请选择" v-model="form.position">
                                    <i-option :key="item"
                                              :value="item.value"
                                              v-for="item in position">
                                        {{ item.label }}
                                    </i-option>
                                </i-select>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="开始时间" prop="starttime">
                                <time-picker placeholder="选择时间" type="time"
                                             v-model="form.starttime"></time-picker>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="结束时间" prop="endtime">
                                <time-picker type="time" placeholder="选择时间"
                                             v-model="form.endtime"></time-picker>
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
                            <form-item label="链接地址" prop="linkAddress">
                                <i-input v-model="form.linkAddress"></i-input>
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