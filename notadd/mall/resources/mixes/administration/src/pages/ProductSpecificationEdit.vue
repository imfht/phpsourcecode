<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.all([
                injection.http.post(`${window.api}/mall/admin/product/specification`, {
                    id: to.params.id,
                }),
                injection.http.post(`${window.api}/mall/admin/product/category/list`),
            ]).then(injection.http.spread((one, two) => {
                window.console.log(one, two);
                const form = one.data.data;
                const structures = two.data.structure;
                next(vm => {
                    form.placeholder = form.category.breadcrumb;
                    form.category = form.category.path;
                    vm.form = form;
                    vm.categories = Object.keys(structures).map(index => {
                        const item = structures[index];
                        item.label = item.name;
                        item.value = item.id;
                        const children = item.children;
                        item.children = Object.keys(children).map(i => {
                            const sub = children[i];
                            sub.label = sub.name;
                            sub.value = sub.id;
                            const down = sub.children;
                            sub.children = Object.keys(down).map(n => {
                                const son = down[n];
                                son.label = son.name;
                                son.value = son.id;
                                return son;
                            });
                            return sub;
                        });
                        return item;
                    });
                    injection.loading.finish();
                });
            })).catch(() => {
                injection.loading.fail();
            });
        },
        data() {
            return {
                categories: [],
                loading: false,
                rules: {
                    name: [
                        {
                            message: '规格不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
                form: {
                    category: [],
                    name: '',
                    order: '',
                    placeholder: '',
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
                        const form = self.form;
                        if (form.category.length) {
                            form.category_id = form.category[form.category.length - 1];
                        } else {
                            form.category_id = 0;
                        }
                        self.$http.post(`${window.api}/mall/admin/product/specification/edit`, form).then(() => {
                            self.$notice.open({
                                title: '更新规格信息成功！',
                            });
                            self.$router.push('/mall/product/specification');
                        }).catch(() => {
                            self.$notice.error({
                                title: '更新规格信息失败！',
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
        <div class="goods-standard-edit">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>规格管理—编辑</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <div class="basic-information">
                        <row>
                            <i-col span="12">
                                <form-item label="规格" prop="name">
                                    <i-input v-model="form.name"></i-input>
                                    <p class="tip">
                                        请填写常用的商品规格的名称；例如：颜色；尺寸等
                                    </p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="快捷定位">
                                    <div class="flex-module">
                                        <cascader :data="categories"
                                                  change-on-select
                                                  :placeholder="form.placeholder"
                                                  v-model="form.category">
                                        </cascader>
                                    </div>
                                    <p class="tip">选择分类，可关联到任意级分类 （只在后台快捷定位中起作用）</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="排序" prop="order">
                                    <i-input v-model="form.order"></i-input>
                                    <p class="tip">
                                        请填写自然数。类型列表将会根据排序进行由小到大排列显示
                                    </p>
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