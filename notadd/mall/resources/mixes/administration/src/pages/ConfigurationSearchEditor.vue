<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            return {
                formValidate: {
                    search: '',
                    show: '',
                },
                loading: false,
                rules: {
                    search: [
                        {
                            message: '搜索默认词不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    show: [
                        {
                            message: '显示词不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                },
                form: {
                    search: '',
                    show: '',
                },
                validate: {
                    search: [
                        {
                            message: '搜索默认词不能为空',
                            required: true,
                            trigger: 'blur',
                            type: 'string',
                        },
                    ],
                    show: [
                        {
                            message: '显示词不能为空',
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
            submit() {
                const self = this;
                self.loading = true;
                self.$refs.form.validate(valid => {
                    if (valid) {
                        self.$Message.success('提交成功!');
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
        <div class="configuration-search-editor">
            <div class="edit-link-title">
                <i-button type="text" @click.native="goBack">
                    <icon type="chevron-left"></icon>
                </i-button>
                <span>热门搜索—编辑</span>
            </div>
            <card :bordered="false">
                <i-form :label-width="200" ref="form" :model="form" :rules="rules">
                    <row>
                        <i-col span="12">
                            <form-item label="搜索默认词" prop="search">
                                <i-input v-model="form.search" placeholder=""></i-input>
                                <p class="range">搜索词参与搜索，列：童装</p>
                            </form-item>
                        </i-col>
                    </row>
                    <row>
                        <i-col span="12">
                            <form-item label="显示词" prop="show">
                                <i-input v-model="form.show" placeholder=""></i-input>
                                <p class="range">显示词不参与搜索，只起显示作用，例：61儿童节，童装五折狂欢</p>
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