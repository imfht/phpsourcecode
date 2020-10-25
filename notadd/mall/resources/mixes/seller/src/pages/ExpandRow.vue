<template>
    <div>
        <i-table class="expand-row"
                 :columns="columns"
                 :context="self"
                 :data="row.subordinate"
                 ref="categoryList"
                 :show-header="false">
        </i-table>
        <!--<row class="expand-row" v-for="item in row.subordinate">
            <i-col span="6">
                <span class="expand-value">{{ item.name }}</span>
            </i-col>
            <i-col span="6">
                <span class="expand-value">{{ item.sort }}</span>
            </i-col>
            <i-col span="6">
                <span class="expand-value">
                    <i-switch size="large" v-model="item.status">
                        <span slot="open">开启</span>
                        <span slot="close">关闭</span>
                    </i-switch>
                </span>
            </i-col>
            <i-col span="6">
                <span class="expand-key"></span>
                <span class="expand-value">
                    <i-button @click.native="edit()" type="ghost">编辑</i-button>
                    <i-button @click.native="remove()" class="delete-ad"
                              type="ghost">删除</i-button>
                </span>
            </i-col>
        </row>-->
    </div>
</template>
<script>
    export default {
        data() {
            const self = this;
            return {
                self: this,
                columns: [
                    {
                        key: 'space',
                        title: '',
                        width: 110,
                    },
                    {
                        key: 'name',
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
            };
        },
        props: {
            row: Object,
        },
    };
</script>