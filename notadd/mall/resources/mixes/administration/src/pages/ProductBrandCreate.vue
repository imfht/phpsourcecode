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
                action: `${window.api}/mall/admin/upload`,
                categories: [],
                defaultList: [],
                form: {
                    categories: [
                        [],
                    ],
                    initial: '',
                    logo: '',
                    name: '',
                    recommend: true,
                    show: 'image',
                    sort: '',
                },
                initials: [],
                loading: false,
                rules: {
                    name: [
                        {
                            message: '名称不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    initial: [
                        {
                            message: '名称首字母不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
            };
        },
        methods: {
            addContactType() {
                this.form.categories.push([]);
            },
            deleteType(index) {
                this.form.categories.splice(index, 1);
            },
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
                        self.$http.post(`${window.api}/mall/admin/product/brand/create`, self.form).then(() => {
                            self.$notice.open({
                                title: '创建品牌信息成功！',
                            });
                            self.$router.push('/mall/product/brand');
                        }).catch(() => {
                            self.$notice.error({
                                title: '创建品牌信息失败！',
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
        mounted() {
            const self = this;
            for (let i = 0; i < 25; i += 1) {
                self.initials.push({
                    label: String.fromCharCode((65 + i)),
                    value: String.fromCharCode((65 + i)),
                });
            }
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
        <div class="good-brand-add">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>品牌管理—新增</span>
            </div>
            <card :bordered="false">
                <i-form ref="form" :model="form" :rules="rules" :label-width="200">
                    <div>
                        <row>
                            <i-col span="12">
                                <form-item label="品牌名称" prop="name">
                                    <i-input v-model="form.name"></i-input>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="名称首字母" prop="initial">
                                    <i-select placeholder="请选择" v-model="form.initial">
                                        <i-option v-for="item in initials"
                                                  :value="item.value"
                                                  :key="item">
                                            {{ item.label }}
                                        </i-option>
                                    </i-select>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="14">
                                <form-item label="所属分类">
                                    <row v-for="(item, index) in form.categories" class="contact-classification">
                                        <i-col span="18">
                                           <cascader :data="categories" change-on-select v-model="form.categories[index]"></cascader>
                                        </i-col>
                                        <i-col span="6">
                                           <i-button type="error"
                                                     v-if="index !== 0"
                                                     @click.native="deleteType(index)">删除</i-button>
                                        </i-col>
                                    </row>
                                    <p class="tip">请选择分类，可关联大分类或更具体的下级分类</p>
                                    <i-button class="add-contact-type"
                                              type="ghost"
                                              @click.native="addContactType">增加关联分类</i-button>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="20">
                                <form-item label="品牌LOGO" prop="logo">
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
                                    <p class="tip">品牌LOGO尺寸要求宽度为150像素，高度为50像素，比例为3:1的图片；
                                        支持格式gif、jpg、png</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="20">
                                <form-item label="展示方式">
                                    <radio-group v-model="form.show">
                                        <radio label="image">
                                            <span>图片</span>
                                        </radio>
                                        <radio label="text">
                                            <span>文字</span>
                                        </radio>
                                    </radio-group>
                                    <p class="tip">在"全部品牌"页面的展示方式，如果设置为"图片"则显示该品牌的"品牌图片标识"，
                                        如果设置为"文字"则显示该品牌的“品牌名”</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="20">
                                <form-item label="是否推荐">
                                    <i-switch size="large" v-model="form.recommend">
                                        <span slot="open">开启</span>
                                        <span slot="close">关闭</span>
                                    </i-switch>
                                    <p class="tip">选择被推荐的图片将在所有品牌列表页"推荐品牌"位置展现</p>
                                </form-item>
                            </i-col>
                        </row>
                        <row>
                            <i-col span="12">
                                <form-item label="排序" prop="sort">
                                    <i-input v-model="form.sort"></i-input>
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