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
                loading: false,
                rules: {},
                list: [
                    {
                        children: [
                            {
                                label: '故宫',
                                value: 'gugong',
                            },
                            {
                                label: '天坛',
                                value: 'tiantan',
                            },
                            {
                                label: '王府井',
                                value: 'wangfujing',
                            },
                        ],
                        label: '北京',
                        value: 'beijing',
                    },
                    {
                        children: [
                            {
                                children: [
                                    {
                                        label: '拙政园',
                                        value: 'zhuozhengyuan',
                                    },
                                    {
                                        label: '狮子林',
                                        value: 'shizilin',
                                    },
                                ],
                                label: '苏州',
                                value: 'suzhou',
                            },
                            {
                                children: [
                                    {
                                        label: '拙政园1',
                                        value: 'zhuozhengyuan',
                                    },
                                    {
                                        label: '狮子林1',
                                        value: 'shizilin',
                                    },
                                ],
                                label: '拙政园1',
                                value: 'zhuozhengyuan',
                            },
                        ],
                        label: '江苏',
                        value: 'jiangsu',
                    },
                ],
                form: {
                    address: '',
                    cardPicture: '',
                    deliveryName: '',
                    idNum: '',
                    name: '',
                    phone: '',
                    province: '',
                    realName: '',
                    seatNumber: '',
                    status: true,
                },
            };
        },
        methods: {
            goBack() {
                const self = this;
                self.$router.go(-1);
            },
            removeLogo() {
                this.form.cardPicture = '';
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
                self.form.cardPicture = data.data.path;
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="operation-delivery-add">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>管理—新增自提点</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <row>
                        <i-col span="12">
                            <form-item label="自提点用户名">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="真实姓名">
                                <i-input v-model="form.realName"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="手机号">
                                <i-input v-model="form.phone"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="座机号">
                                <i-input v-model="form.seatNumber"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="自提点名称">
                                <i-input v-model="form.deliveryName"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="所在地区">
                                <cascader :data="list" trigger="hover" v-model="form.province"></cascader>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="18">
                            <form-item label="详细地址">
                                <i-input v-model="form.address" type="textarea"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="身份证号码">
                                <i-input v-model="form.idNum"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="身份证正面" prop="cardPicture">
                                <div class="image-preview" v-if="form.cardPicture">
                                    <img :src="form.cardPicture">
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
                                        v-if="form.cardPicture === '' || form.cardPicture === null">
                                </upload>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="状态" >
                                <i-switch size="large" v-model="form.status">
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