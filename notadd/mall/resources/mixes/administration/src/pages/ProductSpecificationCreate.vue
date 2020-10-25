<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/category/list`).then(response => {
                const structures = response.data.structure;
                next(vm => {
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
            }).catch(() => {
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
                },
            };
        },
        methods: {
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
                        self.$http.post(`${window.api}/mall/admin/product/specification/create`, form).then(() => {
                            self.$notice.open({
                                title: '创建规格信息成功！',
                            });
                            self.$router.push('/mall/product/specification');
                        }).catch(() => {
                            self.$notice.error({
                                title: '创建规格信息失败！',
                            });
                        }).finally(() => {
                            self.loading = false;
                        });
                        window.console.log(valid);
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
        <div class="goods-standard-add">
            <div class="edit-link-title">
                <router-link to="/mall/product/specification">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>规格管理—添加</span>
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
                                    <cascader :data="categories"
                                              trigger="click"
                                              v-model="form.category"></cascader>
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