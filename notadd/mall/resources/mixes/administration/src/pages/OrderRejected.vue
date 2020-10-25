<script>
    import image1 from '../assets/images/img_logo.png';

    export default {
        beforeRouteEnter(to, from, next) {
            next(() => {
            });
        },
        data() {
            const self = this;
            return {
                allRecordsColumns: [
                    {
                        align: 'center',
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'memberID',
                        title: '退单编号',
                        width: 190,
                    },
                    {
                        align: 'center',
                        key: 'memberAccount',
                        title: '退款金额',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'applicationStatus',
                        title: '申请原因',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'goodsImg',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.goodsImg,
                                            },
                                        });
                                    },
                                    default() {
                                        return h('icon', {
                                            props: {
                                                type: 'image',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '申请图片',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'contactPhone',
                        title: '申请时间',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'contactEmail',
                        title: '涉及商品',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyName',
                        title: '退货数量',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyAddress',
                        title: '商品ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyNum',
                        title: '订单编号',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'handel',
                        title: '商家处理',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'handelMessage',
                        title: '商家处理备注',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'platform',
                        title: '平台处理',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'platformMessage',
                        title: '平台处理备注',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'buyerId',
                        title: '买家ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyId',
                        title: '商家ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h, data) {
                            if (data.row.handel === '同意') {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.recordLook();
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看');
                            } else if (data.row.handel === '不同意') {
                                return h('i-button', {
                                    on: {
                                        click() {
                                            self.recordRejectLook();
                                        },
                                    },
                                    props: {
                                        size: 'small',
                                        type: 'ghost',
                                    },
                                }, '查看');
                            }
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.handelAll();
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '处理');
                        },
                        title: '操作',
                        width: 140,
                    },
                ],
                allRecordsData: [
                    {
                        applicationStatus: '等待审核',
                        buyerId: 22,
                        companyId: 3434,
                        companyName: '本初网络',
                        companyNum: '4535',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        handel: '同意',
                        handelMessage: '',
                        memberAccount: '465465445',
                        memberID: '65454654546',
                        platform: '',
                        platformMessage: '',
                        goodsImg: image1,
                    },
                    {
                        applicationStatus: '等待审核',
                        buyerId: 22,
                        companyId: 3434,
                        companyName: '本初网络',
                        companyNum: '4535',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        handel: '待审核',
                        handelMessage: '',
                        memberAccount: '465465445',
                        memberID: '65454654546',
                        platform: '',
                        platformMessage: '',
                        goodsImg: image1,
                    },
                    {
                        applicationStatus: '等待审核',
                        buyerId: 22,
                        companyId: 3434,
                        companyName: '本初网络',
                        companyNum: '4535',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        handel: '同意',
                        handelMessage: '',
                        memberAccount: '465465445',
                        memberID: '65454654546',
                        platform: '',
                        platformMessage: '',
                        goodsImg: image1,
                    },
                    {
                        applicationStatus: '等待审核',
                        buyerId: 22,
                        companyId: 3434,
                        companyName: '本初网络',
                        companyNum: '4535',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        handel: '不同意',
                        handelMessage: '',
                        memberAccount: '465465445',
                        memberID: '65454654546',
                        platform: '',
                        platformMessage: '',
                        goodsImg: image1,
                    },
                ],
                pendingColumns: [
                    {
                        align: 'center',
                        fixed: 'left',
                        type: 'selection',
                        width: 60,
                    },
                    {
                        align: 'center',
                        key: 'memberID',
                        title: '退单编号',
                        width: 190,
                    },
                    {
                        align: 'center',
                        key: 'memberAccount',
                        title: '退款金额',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'applicationStatus',
                        title: '申请原因',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'goodsImg',
                        render(h, data) {
                            return h('tooltip', {
                                props: {
                                    placement: 'right-end',
                                },
                                scopedSlots: {
                                    content() {
                                        return h('img', {
                                            domProps: {
                                                src: data.row.goodsImg,
                                            },
                                        });
                                    },
                                    default() {
                                        return h('icon', {
                                            props: {
                                                type: 'image',
                                            },
                                        });
                                    },
                                },
                            });
                        },
                        title: '申请图片',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'contactPhone',
                        title: '申请时间',
                        width: 100,
                    },
                    {
                        align: 'center',
                        key: 'contactEmail',
                        title: '涉及商品',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyName',
                        title: '退货数量',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyAddress',
                        title: '商品ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyPhone',
                        title: '订单编号',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyPhone',
                        title: '商家处理时间',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyPhone',
                        title: '商家处理备注',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyPhone',
                        title: '买家ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        key: 'companyPhone',
                        title: '商家ID',
                        width: 150,
                    },
                    {
                        align: 'center',
                        fixed: 'right',
                        key: 'action',
                        render(h) {
                            return h('i-button', {
                                on: {
                                    click() {
                                        self.recordHandel();
                                    },
                                },
                                props: {
                                    size: 'small',
                                    type: 'ghost',
                                },
                            }, '处理');
                        },
                        title: '操作',
                        width: 140,
                    },
                ],
                pendingData: [
                    {
                        applicationStatus: '等待审核',
                        companyName: '本初网络',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        goodsImg: image1,
                        memberAccount: '465465445',
                        memberID: '65454654546',
                    },
                    {
                        applicationStatus: '等待审核',
                        companyName: '本初网络',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        goodsImg: image1,
                        memberAccount: '465465445',
                        memberID: '65454654546',
                    },
                    {
                        applicationStatus: '等待审核',
                        companyName: '本初网络',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        goodsImg: image1,
                        memberAccount: '465465445',
                        memberID: '65454654546',
                    },
                    {
                        applicationStatus: '等待审核',
                        companyName: '本初网络',
                        companyAddress: '陕西西安',
                        companyPhone: '029-5554544',
                        contactEmail: '105454354@qq.com',
                        contactName: '王',
                        contactPhone: '15434354534',
                        goodsImg: image1,
                        memberAccount: '465465445',
                        memberID: '65454654546',
                    },
                ],
                searchCategory: '',
                searchList: [
                    {
                        label: '退单编号',
                        value: '1',
                    },
                    {
                        label: '商品名称',
                        value: '2',
                    },
                    {
                        label: '买家账号',
                        value: '3',
                    },
                    {
                        label: '店铺名称',
                        value: '4',
                    },
                    {
                        label: '订单编号',
                        value: '5',
                    },
                ],
                searchWord: '',
            };
        },
        methods: {
            exportData() {
                this.$refs.pendingTable.exportCsv({
                    filename: '退货管理数据',
                });
            },
            handelAll() {
                const self = this;
                self.$router.push({
                    path: 'rejected/all/handel',
                });
            },
            recordLook() {
                const self = this;
                self.$router.push({
                    path: 'rejected/look',
                });
            },
            recordHandel() {
                const self = this;
                self.$router.push({
                    path: 'rejected/handel',
                });
            },
            recordRejectLook() {
                const self = this;
                self.$router.push({
                    path: 'rejected/look/reject',
                });
            },
            remove(index) {
                this.pendingData.splice(index, 1);
            },
        },
    };
</script>
<template>
  <div class="mall-wrap">
      <div class="order-rejected">
          <tabs value="name1">
              <tab-pane label="待处理" name="name1">
                  <card :bordered="false">
                     <div class="prompt-box">
                         <p>提示</p>
                         <p>卖家提交申请，商家同意并经平台确认后，退款金额以买家付款方式返回给买家</p>
                     </div>
                     <div class="store-body">
                         <div class="store-body-header">
                             <i-button class="export-btn" type="ghost" @click="exportData">导出数据</i-button>
                             <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                             <div class="store-body-header-right">
                                 <i-input v-model="searchWord">
                                     <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                         <i-option v-for="item in searchList"
                                                   :key="item" :value="item.value">
                                             {{ item.label }}</i-option>
                                     </i-select>
                                     <i-button slot="append" type="primary">搜索</i-button>
                                 </i-input>
                             </div>
                         </div>
                         <i-table class="shop-table"
                                  :columns="pendingColumns"
                                  :data="pendingData"
                                  :context="self"
                                  ref="pendingTable"></i-table>
                     </div>
                     <div class="page">
                         <page :total="100" show-elevator></page>
                     </div>
                  </card>
              </tab-pane>
              <tab-pane label="所有记录" name="name2">
                  <card :bordered="false">
                      <div class="store-body">
                          <div class="store-body-header">
                              <i-button class="export-btn" type="ghost"  @click="exportData" >导出数据</i-button>
                              <i-button type="text" icon="android-sync" class="refresh">刷新</i-button>
                              <div class="store-body-header-right">
                                  <i-input v-model="searchWord">
                                      <i-select v-model="searchCategory" slot="prepend" style="width: 100px">
                                          <i-option v-for="item in searchList" :value="item.value"
                                                    :key="item">{{ item.label }}</i-option>
                                      </i-select>
                                      <i-button slot="append" type="primary">搜索</i-button>
                                  </i-input>
                              </div>
                          </div>
                          <i-table  ref="pendingTable"  highlight-row class="shop-table" :columns="allRecordsColumns"
                                    :data="allRecordsData"></i-table>
                          <div class="page">
                              <page :total="100" show-elevator></page>
                          </div>
                      </div>
                  </card>
              </tab-pane>
          </tabs>
    </div>
  </div>
</template>