<script>
    import injection from '../helpers/injection';

    export default {
        beforeRouteEnter(to, from, next) {
            injection.loading.start();
            injection.http.post(`${window.api}/mall/admin/product/category/list`, {
                parent_id: to.query.parent,
            }).then(response => {
                const structures = response.data.structure;
                next(vm => {
                    vm.current = response.data.current;
                    vm.form.parent = response.data.current.path;
                    vm.level = response.data.level;
                    vm.parents = Object.keys(structures).map(index => {
                        const item = structures[index];
                        item.label = item.name;
                        item.value = item.id;
                        const children = item.children;
                        item.children = Object.keys(children).map(i => {
                            const sub = children[i];
                            sub.label = sub.name;
                            sub.value = sub.id;
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
                action: `${window.api}/mall/admin/upload`,
                current: {},
                form: {
                    deposit: 10,
                    logo: '',
                    name: '',
                    order: 0,
                    parent: [],
                    show: 'SPU',
                },
                level: 0,
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
                parents: [],
                ways: [
                    {
                        value: 'SPU',
                        label: 'SPU',
                    },
                    {
                        value: 'SKU',
                        label: 'SKU',
                    },
                ],
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
                        const form = self.form;
                        if (form.parent.length > 0) {
                            form.parent_id = form.parent[form.parent.length - 1];
                        }
                        window.console.log(form);
                        self.$http.post(`${window.api}/mall/admin/product/category/create`, self.form).then(() => {
                            self.$notice.open({
                                title: '创建商品分类信息成功！',
                            });
                            if (self.form.parent) {
                                self.$router.push({
                                    path: '/mall/product/category',
                                    query: {
                                        parent: form.parent_id,
                                    },
                                });
                            } else {
                                self.$router.push('/mall/product/category');
                            }
                        }).catch(() => {
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
        watch: {
            form: {
                deep: true,
                handler(val) {
                    window.console.log(val);
                },
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-category-add">
            <div class="edit-link-title">
                <router-link to="/mall/product/category">
                    <i-button type="text">
                        <icon type="chevron-left"></icon>
                    </i-button>
                </router-link>
                <span>分类管理—新增</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <div class="basic-information">
                        <row>
                            <i-col span="12">
                                <form-item label="分类名称" prop="name">
                                    <i-input number v-model="form.name"></i-input>
                                </form-item>
                            </i-col>
                        </row>
                        <template v-if="level === 3">
                            <row>
                                <i-col span="12">
                                    <form-item label="展示方式" prop="show">
                                        <i-select placeholder="请选择" v-model="form.show">
                                            <i-option :value="way.value"
                                                      :key="way"
                                                      v-for="way in ways">{{ way.label }}</i-option>
                                        </i-select>
                                    </form-item>
                                </i-col>
                            </row>
                        </template>
                        <row>
                            <i-col span="12">
                                <form-item label="分佣比例" prop="deposit">
                                    <i-input v-model="form.deposit"></i-input>
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
                                <form-item label="上级分类">
                                    <cascader change-on-select :data="parents" v-model="form.parent"
                                              @on-change="selectChange"></Cascader>
                                    <p class="tip">如果选择上级分类,那么新的分类则为被选择上级分类的子分类</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="排序">
                                    <i-input v-model="form.order"></i-input>
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