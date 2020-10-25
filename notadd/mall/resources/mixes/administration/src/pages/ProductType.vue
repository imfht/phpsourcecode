<script>
    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                managementSearch: '',
                searchList: [
                    {
                        label: '店铺名称',
                        value: '订单编号',
                    },
                    {
                        label: '商品名称',
                        value: '商品名称',
                    },
                    {
                        label: '商品分类',
                        value: '商品分类',
                    },
                ],
                columns: [
                    {
                        key: 'typeId',
                        title: '类型ID',
                    },
                    {
                        key: 'typeName',
                        title: '类型名称',
                    },
                    {
                        key: 'sort',
                        title: '类型排序',
                    },
                    {
                        key: 'positionId',
                        title: '快捷定位ID',
                    },
                    {
                        key: 'positionName',
                        title: '快捷定位名称',
                    },
                    {
                        key: 'action',
                        title: '操作',
                        width: 180,
                        render(h, data) {
                            return h('div', [
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.edit(data.index);
                                        },
                                    },
                                    props: {
                                        class: 'delete-ad',
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '编辑'),
                                h('i-button', {
                                    on: {
                                        click() {
                                            self.remove(data.index);
                                        },
                                    },
                                    props: {
                                        class: 'delete-ad',
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '删除'),
                            ]);
                        },
                    },
                ],
                list: [
                    {
                        positionId: '22',
                        positionName: '液晶电视',
                        sort: '6',
                        typeId: '0001',
                        typeName: '迪卡侬',
                    },
                    {
                        positionId: '22',
                        positionName: '液晶电视',
                        sort: '6',
                        typeId: '0001',
                        typeName: '迪卡侬',
                    },
                    {
                        positionId: '22',
                        positionName: '液晶电视',
                        sort: '6',
                        typeId: '0001',
                        typeName: '迪卡侬',
                    },
                ],
            };
        },
        methods: {
            edit() {
                const self = this;
                self.$router.push({
                    path: 'type/edit',
                });
            },
            newAddData() {
                const self = this;
                self.$router.push({
                    path: 'type/add',
                });
            },
            remove(index) {
                this.list.splice(index, 1);
            },
        },
    };
</script>
<template>
    <div class="mall-wrap">
        <div class="goods-type">
            <tabs value="name1">
                <tab-pane label="类型管理" name="name1">
                    <card :bordered="false">
                        <div class="prompt-box">
                            <p>提示</p>
                            <p>当管理员添加商品分类时需选择类型。前台分类下商品列表页通过类型生成商品检索，
                                方便用户搜索需要的商品</p>
                        </div>
                        <div class="advertisement-action">
                            <i-button class="add-data" type="ghost" @click.native="newAddData">+新增数据</i-button>
                            <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                            <div class="goods-body-header-right">
                                <i-input v-model="managementWord" placeholder="请输入关键词进行搜索">
                                    <i-select v-model="managementSearch" slot="prepend" style="width: 100px;">
                                        <i-option v-for="item in searchList"
                                                  :value="item.value">{{ item.label }}
                                        </i-option>
                                    </i-select>
                                    <i-button slot="append" type="primary">搜索</i-button>
                                </i-input>
                            </div>
                        </div>
                        <i-table :columns="columns" :data="list" highlight-row></i-table>
                    </card>
                </tab-pane>
            </tabs>
        </div>
    </div>
</template>