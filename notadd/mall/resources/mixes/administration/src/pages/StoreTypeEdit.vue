<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/store/type`, {
                id: to.params.id,
            }).then(response => {
                next(vm => {
                    vm.form = response.data.data;
                    injection.loading.finish();
                });
            }).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                form: {
                    amount_of_deposit: '',
                    name: '',
                    order: 0,
                },
                loading: false,
                rules: {
                    amount_of_deposit: [
                        {
                            message: '保证金额数不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
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
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$http.post(`${window.api}/mall/admin/store/type/edit`, self.form).then(() => {
                            self.$notice.open({
                                title: '编辑店铺类型信息成功！',
                            });
                            self.$router.push('/mall/store/type');
                        }).catch(() => {
                            self.$notice.error({
                                title: '编辑店铺类型信息失败！',
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
        <div class="store-category-set">
            <div class="edit-link-title">
                <router-link to="/mall/store/type">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>店铺分类—设置</span>
            </div>
            <card :bordered="false">
                <i-form :label-width="200" ref="form" :model="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="分类名称" prop="name">
                                <i-input v-model="form.name" placeholder="请输入店铺分类名称"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item  label="保证金额数" prop="amount_of_deposit">
                                <i-input v-model="form.amount_of_deposit" placeholder="请输入店铺保证金额数"></i-input>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="排序">
                                <i-input v-model="form.order" placeholder=""></i-input>
                                <p class="tip">数字范围为0~255，数字越小越靠前</p>
                            </form-item>
                        </i-col>
                    </row>
                    <form-item>
                        <i-button @click.native="submit" type="primary">
                            <span v-if="!loading">确认提交</span>
                            <span v-else>正在提交…</span>
                        </i-button>
                    </form-item>
                </i-form>
            </card>
        </div>
    </div>
</template>