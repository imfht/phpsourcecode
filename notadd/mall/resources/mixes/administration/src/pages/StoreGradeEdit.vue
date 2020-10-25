<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/store/grade`, {
                id: to.params.id,
            }).then(response => {
                next(vm => {
                    const form = response.data.data;
                    form.price = parseInt(form.price, 12);
                    vm.form = form;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                loading: false,
                rules: {
                    application_instruction: [
                        {
                            message: '申请说明不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    level: [
                        {
                            message: '级别不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                        },
                    ],
                    name: [
                        {
                            message: '等级名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    price: [
                        {
                            message: '收费标准不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                        },
                    ],
                },
                form: {
                    application_instruction: '',
                    can_claim: true,
                    can_upload: true,
                    extensions: [],
                    id: 0,
                    level: 0,
                    name: '',
                    price: 0,
                    publish_limit: 0,
                    upload_limit: 0,
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
                        self.$http.post(`${window.api}/mall/admin/store/grade/edit`, self.form).then(() => {
                            self.$notice.open({
                                title: '编辑店铺等级信息成功！',
                            });
                            self.$router.push('/mall/store/grade');
                        }).catch(() => {
                            self.$notice.error({
                                title: '编辑店铺等级信息失败！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
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
        <div class="store-level-edit">
            <div class="edit-link-title">
                <router-link to="/mall/store/grade">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>店铺等级—编辑</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <row>
                        <i-col span="12">
                            <form-item label="等级名称" prop="name">
                                <i-input v-model="form.name"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="可发布商品数">
                                <i-input number v-model="form.publish_limit"></i-input>
                                <p class="tip">0表示没有限制</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="可上传商品数" prop="upload_limit">
                                <i-input number v-model="form.upload_limit"></i-input>
                                <p class="tip">0表示没有限制</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="可认领商品" prop="can_claim">
                                <i-switch size="large" v-model="form.can_claim">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="可自主发布商品" prop="can_upload">
                                <i-switch size="large" v-model="form.can_upload">
                                    <span slot="open">开启</span>
                                    <span slot="close">关闭</span>
                                </i-switch>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="收费标准" prop="price">
                                <i-input number v-model="form.price"></i-input>
                                <p class="tip">收费标准，单位：元/年，必须为数字，在会员开通或升级店铺时将显示在前台</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="可使用插件" prop="extensions">
                                <checkbox-group v-model="form.extensions">
                                    <checkbox label="秒杀活动"></checkbox>
                                    <checkbox label="预售活动"></checkbox>
                                    <checkbox label="满减活动"></checkbox>
                                    <checkbox label="包邮"></checkbox>
                                </checkbox-group>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="申请说明" prop="application_instruction">
                                <i-input v-model="form.application_instruction" type="textarea"></i-input>
                                <p class="tip">在会员开通或升级店铺时将显示在前台</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="级别" prop="level">
                                <i-input number v-model="form.level"></i-input>
                                <p class="tip">数值越大表明级别越高</p>
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