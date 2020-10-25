<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/category`, {
                id: to.params.id,
            }).then(repsonse => {
                window.console.log(repsonse);
                next(vm => {
                    vm.form = repsonse.data.data;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                action: `${window.api}/mall/admin/upload`,
                form: {
                    logo: '',
                    order: 0,
                    deposit: 0,
                    name: '',
                    parent_id: 0,
                },
                loading: false,
                rules: {
                    deposit: [
                        {
                            message: '分佣比例不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'number',
                        },
                    ],
                    name: [
                        {
                            message: '分类名称不能为空',
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
                        self.$http.post(`${window.api}/mall/admin/product/category/edit`, self.form).then(() => {
                            self.$notice.open({
                                title: '编辑商品分类信息成功！',
                            });
                            if (self.form.parent_id) {
                                self.$router.push({
                                    path: '/mall/product/category',
                                    query: {
                                        parent: self.form.parent_id,
                                    },
                                });
                            } else {
                                self.$router.push('/mall/product/category');
                            }
                        }).catch(() => {
                            self.$notice.error({
                                title: '编辑商品分类信息失败！',
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
        <div class="goods-category-edit">
            <div class="edit-link-title">
                <router-link to="/mall/product/category">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>分类管理—编辑"珠宝手表"</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <div class="basic-information">
                        <row>
                            <i-col span="12">
                                <form-item label="分类名称" prop="name">
                                    <i-input v-model="form.name"></i-input>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="分佣比例" prop="deposit">
                                    <i-input number v-model="form.deposit"></i-input>
                                    <div class="tip">
                                        <p>分佣比例必须为0-100的整数,默认关联至子分类</p>
                                    </div>
                                </form-item>
                            </i-col>
                            <i-col span="1" class="inline-symbol">%</i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="分类图片" prop="image">
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
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="排序">
                                    <i-input number v-model="form.order"></i-input>
                                    <p class="tip">数字范围为0~255，数字越小越靠前</p>
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
                    </div>
                </i-form>
            </card>
        </div>
    </div>
</template>