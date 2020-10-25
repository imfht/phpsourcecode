<script>
    import expandRow from './ExpandRow.vue';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        components: {
            expandRow,
        },
        data() {
            const self = this;
            return {
                addCategory: {
                    category: '',
                    enable: true,
                    name: '',
                    sort: '',
                },
                addCategoryList: [
                    {
                        label: '海外代购1',
                        value: '1',
                    },
                    {
                        label: '海外代购2',
                        value: '2',
                    },
                    {
                        label: '海外代购3',
                        value: '3',
                    },
                ],
                addModal: false,
                categoryColumns: [
                    {
                        align: 'center',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        type: 'expand',
                        width: 50,
                        render(h, params) {
                            return h(expandRow, {
                                props: {
                                    row: params.row,
                                },
                            });
                        },
                    },
                    {
                        key: 'categoryName',
                        render(h, data) {
                            return h('div', [
                                h('span', data.row.categoryName),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.addSubordinate(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '新增下级'),
                            ]);
                        },
                        title: '分类名称',
                    },
                    {
                        align: 'center',
                        key: 'sort',
                        title: '排序',
                    },
                    {
                        key: 'shelves',
                        render(h, data) {
                            return h('i-switch', {
                                props: {
                                    size: 'large',
                                    value: data.row.status,
                                },
                                scopedSlots: {
                                    close() {
                                        return h('span', '关闭');
                                    },
                                    open() {
                                        return h('span', '开启');
                                    },
                                },
                            });
                        },
                        title: '上架',
                    },
                    {
                        align: 'center',
                        key: 'action',
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.edit(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    class: {
                                        'delete-ad': true,
                                    },
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '删除'),
                            ]);
                        },
                        title: '操作',
                    },
                ],
                categoryData: [
                    {
                        categoryName: '海外代购',
                        job: '数据工程师',
                        interest: '羽毛球',
                        birthday: '1991-05-14',
                        sort: '45',
                        status: true,
                        subordinate: [
                            {
                                name: '鞋子',
                                sort: 3,
                                status: true,
                            },
                            {
                                name: '卫衣',
                                sort: 5,
                                status: true,
                            },
                        ],
                        title: '海外代购',
                    },
                    {
                        categoryName: '海外代购',
                        job: '数据工程师',
                        interest: '羽毛球',
                        birthday: '1991-05-14',
                        sort: '456',
                        status: true,
                    },
                ],
                checkAll: false,
                checkAllGroup: [],
                editCategory: {
                    enable: true,
                    name: '',
                    sort: '',
                },
                editModal: false,
                indeterminate: true,
                loading: false,
                newAdd: {
                    category: '海外代购',
                    enable: true,
                    name: '',
                    sort: '',
                },
                self: this,
                subordinate: false,
            };
        },
        methods: {
            addCategoryModal() {
                this.addModal = true;
            },
            addSubordinate() {
                this.subordinate = true;
            },
            edit() {
                this.editModal = true;
            },
            remove(index) {
                this.categoryData.splice(index, 1);
            },
            submitCategory() {
                const self = this;
                self.loading = true;
                self.$refs.addCategory.validate(valid => {
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
            submitEdit() {
                const self = this;
                self.loading = true;
                self.$refs.editCategory.validate(valid => {
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
            submitSubordinate() {
                const self = this;
                self.loading = true;
                self.$refs.newAdd.validate(valid => {
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
        },
    };
</script>
<template>
    <div class="seller-wrap">
        <div class="shop-category">
            <tabs value="name1">
                <tab-pane label="店铺分类" name="name1">
                    <card :bordered="false">
                        <div class="category-list">
                            <i-button class="first-btn" type="ghost" @click.native="addCategoryModal">+新增分类</i-button>
                            <i-button type="ghost">批量删除</i-button>
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                            <i-table :columns="categoryColumns"
                                     :context="self"
                                     :data="categoryData"
                                     ref="categoryList"
                                     highlight-row>
                            </i-table>
                        </div>
                        <div class="page">
                            <page :total="100" show-elevator></page>
                        </div>
                    </card>
                    <modal
                            v-model="subordinate"
                            title="新增下级" class="upload-picture-modal">
                        <div>
                            <i-form ref="newAdd" :model="newAdd" :rules="newAddValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="分类名称">
                                            <i-input v-model="newAdd.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="上级分类">
                                            {{ newAdd.category }}
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="10">
                                        <form-item label="排序">
                                            <i-input v-model="newAdd.sort"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="显示状态">
                                            <radio-group v-model="newAdd.enable">
                                                <radio label="是"></radio>
                                                <radio label="否"></radio>
                                            </Radio-group>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitSubordinate">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                    <modal
                            v-model="editModal"
                            title="编辑分类" class="upload-picture-modal">
                        <div>
                            <i-form ref="editCategory" :model="editCategory" :rules="editValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="分类名称">
                                            <i-input v-model="editCategory.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="10">
                                        <form-item label="排序">
                                            <i-input v-model="editCategory.sort"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="显示状态">
                                            <radio-group v-model="editCategory.enable">
                                                <radio label="是"></radio>
                                                <radio label="否"></radio>
                                            </Radio-group>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitEdit">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                    <modal
                            v-model="addModal"
                            title="新增分类" class="upload-picture-modal">
                        <div>
                            <i-form ref="addCategory" :model="addCategory" :rules="editValidate" :label-width="100">
                                <row>
                                    <i-col span="12">
                                        <form-item label="分类名称">
                                            <i-input v-model="addCategory.name"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="上级分类">
                                            <i-select v-model="addCategory.category">
                                                <i-option v-for="item in addCategoryList"
                                                          :value="item.value">{{ item.label }}</i-option>
                                            </i-select>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="10">
                                        <form-item label="排序">
                                            <i-input v-model="addCategory.sort"></i-input>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="12">
                                        <form-item label="显示状态">
                                            <radio-group v-model="addCategory.enable">
                                                <radio label="是"></radio>
                                                <radio label="否"></radio>
                                            </Radio-group>
                                        </form-item>
                                    </i-col>
                                </row>
                                <row>
                                    <i-col span="20">
                                        <form-item>
                                            <i-button :loading="loading" type="primary" @click.native="submitCategory">
                                                <span v-if="!loading">确认提交</span>
                                                <span v-else>正在提交…</span>
                                            </i-button>
                                        </form-item>
                                    </i-col>
                                </row>
                            </i-form>
                        </div>
                    </modal>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>