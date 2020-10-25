<template>
    <div v-clickoutside="handleClose" @click="handleClose">
        <!--已选列表-->
        <div v-if="multipleLists.length > 0" class="user-id-multiple">
            <div v-for="(item, index) in multipleLists" :key="index" class="user-id-multiple-item">
                <Tag @on-close="multipleLists.splice(index,1)" :closable="!existMultipleDisabled(item.username)"><UserView :username="item.username" showimg/></Tag>
            </div>
        </div>

        <!--输入框区域-->
        <div class="user-id-input" ref="reference">
            <Input v-model="nickName"
                   :placeholder="placeholder"
                   :disabled="disabled"
                   icon="md-search"
                   @on-click="searchEnter"
                   @on-enter="searchEnter"
                   @on-blur="searchEnter(true)"
                   @on-change="inputChange">
                <div v-if="$slots.prepend !== undefined" slot="prepend"><slot name="prepend"></slot></div>
                <div v-if="$slots.append !== undefined" slot="append"><slot name="append"></slot></div>
            </Input>
            <div v-if="userName" class="user-id-subtitle">{{$L('用户名')}}: {{userName}}</div>
            <div v-if="spinShow" class="user-id-spin"><div><w-loading></w-loading></div></div>
        </div>

        <!--弹出选择表-->
        <transition name="fade">
            <div
                v-show="!disabled && visible"
                ref="popper"
                class="user-id-input-body"
                :data-transfer="transfer"
                v-transfer-dom>
                <div class="user-id-input-table">
                    <Table highlight-row
                           v-if="searchShow"
                           ref="myTable"
                           size="small"
                           class="tableSelection"
                           :style="tableStyle"
                           :columns="columns"
                           :data="userLists"
                           @on-row-click="userChange"
                           @on-selection-change="userSelect"
                           :no-data-text="noDataText"></Table>
                    <div v-if="isConfirm&&searchShow" class="user-id-input-bottom">
                        <Button type="default" size="small" @click="visible=false">{{$L('取消')}}</Button>
                        <Button type="primary" size="small" @click="onConfirm">{{$L('确定')}}</Button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<style lang="scss" scoped>
    .user-id-multiple {
        margin-bottom: 6px;
        overflow: auto;
        white-space: normal;
        .ivu-tag {
            padding-left: 7px;
        }
        .user-id-multiple-item {
            display: inline-block;
        }
        .user-view-inline {
            height: 20px;
            line-height: 20px;
            vertical-align: top;
        }
    }
    .user-id-input {
        display: inline-block;
        width: 100%;
        position: relative;
        vertical-align: middle;
        z-index: 5;

        .user-id-subtitle {
            position: absolute;
            top: 2px;
            right: 32px;
            height: 30px;
            line-height: 30px;
            color: #cccccc;
            z-index: 2;
        }

        .user-id-spin {
            width: 14px;
            height: 14px;
            position: absolute;
            top: 50%;
            right: 34px;
            transform: translate(0, -50%);
            display: flex;
            align-items: center;
            > div {
                width: 100%;
                height: 100%;
            }
        }
    }
</style>
<style lang="scss">
    .user-id-input-body {
        z-index: 99999
    }
    .user-id-input-table {
        display: flex;
        flex-direction: column;
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 0 4px 2px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;

        .ivu-table table {
            width: 100% !important;
        }

        .ivu-table:before, .ivu-table:after {
            display: none !important;
        }

        .ivu-table-body {
            max-height: 180px;
            overflow: auto;
        }

        .ivu-table-small td {
            cursor: pointer;
        }

        .user-id-input-bottom {
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            > button {
                font-size: 13px;
                margin-left: 10px;
            }
        }
    }
</style>
<script>

    import clickoutside from '../../_modules/directives/clickoutside';
    import TransferDom from '../../_modules/directives/transfer-dom';
    import Popper from '../../_modules/directives/popper-novalue';

    export default {
        name: 'UserInput',
        directives: {clickoutside, TransferDom},
        mixins: [Popper],
        props: {
            placement: {
                default: 'bottom'
            },
            value: {
                default: ''
            },
            identity: {
                default: ''
            },
            noidentity: {
                default: ''
            },
            nousername: {
                default: ''
            },
            noprojectid: {
                default: ''
            },
            projectid: {
                default: ''
            },
            nobookid: {
                default: ''
            },
            nogroupid: {
                default: ''
            },
            placeholder: {
                default: ''
            },
            disabled: {
                type: Boolean,
                default: false
            },
            transfer: {
                type: Boolean,
                default () {
                    return true;
                }
            },
            loadstatus: {
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            multipleDisabled: {
                default: ''
            }
        },
        data () {
            return {
                multipleLists: [],

                userName: '',
                nickName: '',
                nickName__: '',
                seleName: '',
                searchShow: false,
                spinShow: false,
                skipSearch: false,

                isConfirm: false,

                tempName: '',

                winStyle: {},

                columns: [],
                userLists: [],
                noDataText: '',
            }
        },
        created() {
            this.columns = [
                {
                    "title": this.$L("头像"),
                    "width": 60,
                    "align": 'center',
                    render: (h, params) => {
                        return h('UserImg', {
                            props: {
                                info: params.row,
                            },
                            style: {
                                width: "26px",
                                height: "26px",
                                fontSize: "13px",
                                lineHeight: "26px",
                                borderRadius: "50%",
                                verticalAlign: "middle"
                            },
                        });
                    }
                }, {
                    "title": this.$L("用户名"),
                    "key": "username",
                    "minWidth": 80,
                    "ellipsis": true,
                }, {
                    "title": this.$L("昵称"),
                    "key": "nickname",
                    "minWidth": 80,
                    "ellipsis": true,
                    render: (h, params) => {
                        return h('span', params.row.nickname || '-');
                    }
                }
            ];
            if (this.multiple) {
                this.columns.unshift({
                    type: 'selection',
                    width: 30,
                    align: 'center'
                });
            }
            this.noDataText = this.$L("数据加载中.....");
            this.isConfirm = this.$listeners['on-confirm'];
        },
        watch: {
            value (val) {
                if (this.multiple) {
                    this.multipleLists = this.formatMultipleLists(val);
                    return;
                }
                this.userName = $A.cloneData(val)
            },

            userName (val) {
                if (this.skipSearch === true) {
                    this.skipSearch = false;
                }else{
                    this.nickName = '';
                    if (val) {
                        let where = { usernameequal: val };
                        if (typeof this.identity === "string") {
                            where['identity'] = this.identity;
                        }
                        if (typeof this.noidentity === "string") {
                            where['noidentity'] = this.noidentity;
                        }
                        if (typeof this.nousername === "string") {
                            where['nousername'] = this.nousername;
                        }
                        if (this.noprojectid) {
                            where['noprojectid'] = this.noprojectid;
                        }
                        if (this.projectid) {
                            where['projectid'] = this.projectid;
                        }
                        if (this.nobookid) {
                            where['nobookid'] = this.nobookid;
                        }
                        if (this.nogroupid) {
                            where['nogroupid'] = this.nogroupid;
                        }
                        this.noDataText = this.$L("数据加载中.....");
                        $A.apiAjax({
                            url: 'users/searchinfo',
                            data: {
                                where: where,
                                take: 1
                            },
                            beforeSend: () => {
                                this.spinShow = true;
                            },
                            complete: () => {
                                this.spinShow = false;
                                this.noDataText = this.$L("没有相关的数据");
                            },
                            error: () => {
                                this.noDataText = this.$L("数据加载失败！");
                            },
                            success: (res) => {
                                if (res.ret === 1 && $A.count(res.data) > 0) {
                                    let tmpData = res.data[0];
                                    if (this.multiple) {
                                        this.addMultipleLists(tmpData);
                                    } else {
                                        this.userName = tmpData.username;
                                        this.seleName = tmpData.nickname || tmpData.username;
                                        this.nickName = tmpData.nickname || tmpData.username;
                                        this.nickName__ = tmpData.nickname || tmpData.username;
                                        this.$emit('input', this.userName);
                                        this.$emit('change', tmpData);
                                    }
                                }
                            }
                        });
                    }
                }
            },

            nickName(val) {
                if (val != this.seleName || val == '') {
                    this.userName = '';
                    if (!this.multiple) {
                        this.$emit('input', this.userName);
                        this.$emit('change', {});
                    }
                }
            },

            spinShow(val) {
                if (typeof this.loadstatus === 'number') {
                    this.$emit('update:loadstatus', val ? this.loadstatus + 1 : this.loadstatus - 1);
                }else if (typeof this.loadstatus === 'boolean') {
                    this.$emit('update:loadstatus', val);
                }
            },

            searchShow(val) {
                if (val) {
                    this.handleShowPopper();
                    this.updateMultipleLists();
                } else {
                    this.handleClosePopper();
                }
            },

            multipleLists: {
                handler() {
                    if (this.searchShow) {
                        this.updateMultipleLists();
                    }
                    this.emitMultipleLists();
                },
                deep: true
            }
        },
        computed: {
            tableStyle() {
                return this.winStyle;
            }
        },
        methods: {
            inputChange() {
                const val = this.nickName;
                this.spinShow = false;
                this.tempName = '';
                if (val != '') {
                    setTimeout(() => {
                        if (val == this.nickName && val != this.tempName) {
                            this.searchEnter(false, (res) => {
                                return val == this.nickName;
                            });
                        }
                    }, 500)
                } else {
                    this.searchShow = false;
                }
            },

            handleShowPopper() {
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.visible = true;
                }, this.delay);
            },

            handleClosePopper() {
                if (this.timeout) {
                    clearTimeout(this.timeout);
                    if (!this.controlled) {
                        this.timeout = setTimeout(() => {
                            this.visible = false;
                        }, 100);
                    }
                }
            },

            updateStyle() {
                this.winStyle = {
                    width: `${Math.max(this.$el.offsetWidth, 230)}px`,
                };
            },

            emptyAll() {
                this.userName = '';
                this.nickName = '';
                this.nickName__ = '';
                this.seleName = '';
                this.searchShow = false;
                this.spinShow = false;
            },

            searchEnter(verify, callback) {
                if (this.disabled === true) {
                    return;
                }
                if (this.spinShow === true) {
                    return;
                }
                if (verify === true) {
                    if (this.nickName === '') {
                        this.nickName__ = this.nickName;
                    }
                    if (this.nickName__ === this.nickName) {
                        return;
                    }
                }
                this.updateStyle();
                this.nickName__ = this.nickName;
                //
                let where = {username: this.nickName};
                if (typeof this.identity === "string") {
                    where['identity'] = this.identity;
                }
                if (typeof this.noidentity === "string") {
                    where['noidentity'] = this.noidentity;
                }
                if (typeof this.nousername === "string") {
                    where['nousername'] = this.nousername;
                }
                if (this.noprojectid) {
                    where['noprojectid'] = this.noprojectid;
                }
                if (this.projectid) {
                    where['projectid'] = this.projectid;
                }
                if (this.nobookid) {
                    where['nobookid'] = this.nobookid;
                }
                if (this.nogroupid) {
                    where['nogroupid'] = this.nogroupid;
                }
                this.tempName = where.username;
                this.noDataText = this.$L("数据加载中.....");
                $A.apiAjax({
                    url: 'users/searchinfo',
                    data: {
                        where: where,
                        take: 30
                    },
                    beforeSend: () => {
                        this.spinShow = true;
                    },
                    complete: () => {
                        this.spinShow = false;
                        this.noDataText = this.$L("没有相关的数据");
                    },
                    error: () => {
                        this.noDataText = this.$L("数据加载失败！");
                    },
                    success: (res) => {
                        if (typeof callback === "function" && callback(res) === false) {
                            return;
                        }
                        if (res.ret === 1) {
                            this.userLists = res.data;
                            this.userLists.forEach((item) => {
                                if (this.multiple) {
                                    if (this.existMultipleDisabled(item.username)) {
                                        item._disabled = true;
                                    }
                                } else {
                                    if (item.username == this.userName) {
                                        item._highlight = true;
                                    }
                                }
                            });
                            this.searchShow = true;
                        } else {
                            this.$Message.warning(res.msg);
                            this.emptyAll();
                        }
                    }
                });
            },

            userChange(item) {
                if (this.multiple) {
                    if (this.existMultipleDisabled(item.username)) {
                        return;
                    }
                    let tempLists = this.multipleLists.filter(({username}) => username == item.username);
                    if (tempLists.length > 0) {
                        this.multipleLists = this.multipleLists.filter(({username}) => username != item.username);
                    } else {
                        this.addMultipleLists(item);
                    }
                } else {
                    this.userName = item.username;
                    this.seleName = item.nickname || item.username;
                    this.nickName = item.nickname || item.username;
                    this.nickName__ = item.nickname || item.username;
                    this.skipSearch = true;
                    this.searchShow = false;
                    this.$emit('input', this.userName);
                    this.$emit('change', item);
                }
            },

            userSelect() {
                if (this.multiple) {
                    let lists = this.$refs.myTable.objData, item, inThe;
                    for (let index in lists) {
                        if (lists.hasOwnProperty(index)) {
                            item = lists[index];
                            inThe = this.multipleLists.find(({username}) => username == item.username);
                            if (item._isChecked) {
                                !inThe && this.multipleLists.push(item);
                            } else {
                                inThe && (this.multipleLists = this.multipleLists.filter(({username}) => username != item.username));
                            }
                        }
                    }
                }
            },

            handleClose(e) {
                if (this.multiple && $A(e.target).parents('.user-id-input-table').length > 0) {
                    return;
                }
                if (this.searchShow === true) {
                    this.searchShow = false;
                }
            },

            existMultipleDisabled(username) {
                return this.multipleDisabled && $A.strExists(`,${this.multipleDisabled},`, `,${username},`)
            },

            addMultipleLists(item) {
                let inThe = this.multipleLists.find(({username}) => username == item.username);
                if (!inThe) {
                    this.multipleLists.push(item);
                }
            },

            updateMultipleLists() {
                this.$nextTick(() => {
                    let lists = this.$refs.myTable.objData, item, inThe;
                    for (let index in lists) {
                        if (lists.hasOwnProperty(index)) {
                            item = lists[index];
                            inThe = this.multipleLists.find(({username}) => username == item.username);
                            this.$set(item, "_isChecked", !!inThe);
                        }
                    }
                });
            },

            emitMultipleLists() {
                let val = '';
                this.multipleLists.forEach((tmp) => {
                    if (val) {
                        val+= ",";
                    }
                    val+= tmp.username;
                });
                this.$emit('input', val);
            },

            formatMultipleLists(val) {
                let arr = (val + ",").split(",");
                let narr = [];
                arr.forEach((uname) => {
                    if (uname) {
                        let inn = false;
                        narr.some((tmp) => {
                            if (tmp.username == uname) {
                                return inn = true;
                            }
                        })
                        if (!inn) {
                            narr.push({
                                username: uname,
                            });
                        }
                    }
                });
                return narr;
            },

            onConfirm(e) {
                this.searchShow = false;
                this.$emit('on-confirm', e);
            }
        },
        mounted() {
            this.updatePopper();
            //
            if (this.multiple) {
                this.multipleLists = this.formatMultipleLists(this.value);
            } else if ($A.count(this.value) > 0) {
                this.userName = this.value;
            }
        }
    };
</script>
