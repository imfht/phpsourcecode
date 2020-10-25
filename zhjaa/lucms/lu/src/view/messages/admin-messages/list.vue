<template>
<div>
  <Row :gutter="24">
    <Col :xs="4" :lg="3">
    <Select v-model="searchForm.type" placeholder="消息类型">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="4" :lg="3">
    <Select v-model="searchForm.status" placeholder="状态">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.status" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="4" :lg="4">
    <Button type="primary" icon="ios-search" @click="getTableDataExcute(feeds.current_page)">Search</Button>
    </Col>
    <Col :xs="4" :lg="2" class="hidden-mobile">
    <Poptip confirm placement="bottom" title="确认要操作?" @on-ok="readMessagesExcute(selectIds,false)" ok-text="确认" cancel-text="点错了">
      <Button>读取选中消息</Button>
    </Poptip>
    </Col>
    <Col :xs="3" :lg="2" class="hidden-mobile">
    <Poptip confirm placement="bottom" title="确认要操作?" @on-ok="readMessagesExcute('',true)" ok-text="确认" cancel-text="点错了">
      <Button type="warning">一键已读</Button>
    </Poptip>
    </Col>
    <Col :xs="3" :lg="2" class="hidden-mobile">
    <Poptip confirm placement="bottom" title="确认要操作?" @on-ok="deleteManyAdminMessageExcute(selectIds)" ok-text="确认" cancel-text="点错了">
      <Button type="error">删除选中消息</Button>
    </Poptip>
    </Col>
  </Row>

  <br>
  <Row>
    <div class="demo-spin-container" v-if="tableLoading">
      <Spin fix>
        <Icon type="load-c" size=18 class="spin-icon-load"></Icon>
        <div>加载中...</div>
      </Spin>
    </div>
    <Table border :columns="columns" :data="feeds.data" @on-sort-change='onSortChange' @on-selection-change='onSelectionChange'></Table>
    <div style="margin: 10px;overflow: hidden">
      <div style="float: right;">
        <Page :total="feeds.total" :current="feeds.current_page" :page-size="feeds.per_page" class="paging" show-elevator show-total show-sizer @on-change="handleOnPageChange" @on-page-size-change='onPageSizeChange'></Page>
      </div>
    </div>
  </Row>
  <show-info v-if='showInfoModal.show' :info='showInfoModal.info' @show-modal-close="showModalClose"></show-info>

</div>
</template>

<script>
import {
  getTableStatus
} from '@/api/common'

import {
  getTableData,
  readMessages,
  deleteAdminMessage,
  deleteManyAdminMessage
} from '@/api/admin-message'

import ShowInfo from './components/show-info'

export default {
  components: {
    ShowInfo
  },
  data() {
    return {
      selectIds: '',
      searchForm: {
        order_by: 'id,desc'
      },
      showInfoModal: {
        show: false,
        info: ''
      },
      tableLoading: false,
      tableStatus: {
        status: '',
        type: ''
      },
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      addModal: {
        show: false
      },
      editModal: {
        show: false,
        id: 0
      },
      columns: [{
          type: 'selection',
          minWidth: 60,
          align: 'center',
        },
        {
          title: 'ID',
          key: 'id',
          sortable: 'customer',
          minWidth: 50,
        },
        {
          title: '标题',
          key: 'title',
          minWidth: 100,
        },
        {
          title: '类型',
          minWidth: 100,
          render: (h, params) => {
            return h('div',
              this.tableStatus.type[params.row.type]
            )
          }
        },
        {
          title: '状态',
          minWidth: 150,
          render: (h, params) => {
            const row = params.row
            const color = row.status === 'R' ? 'green' : 'red'
            const text = row.status === 'R' ? '已读' : '未读'

            return h('div', [
              h('Tag', {
                props: {
                  color: color
                }
              }, text)
            ])
          }
        },
        {
          title: '创建时间',
          key: 'created_at',
          sortable: 'customer',
          minWidth: 150,
        },
        {
          title: '操作',
          minWidth: 150,
          render: (h, params) => {
            let t = this;
            let readButton =
              h('Poptip', {
                props: {
                  confirm: true,
                  title: '您确定要操作？',
                  transfer: true
                },
                on: {
                  'on-ok': () => {
                    t.readMessagesExcute(params.row.id, false)
                  }
                }
              }, [
                h('Button', {
                  style: {
                    margin: '0 5px'
                  },
                  props: {
                    type: 'info',
                    size: 'small',
                    placement: 'top'
                  }
                }, '标为已读'),
              ])
            if (params.row.status === 'R') readButton = ''
            return h('div', [
              h('Button', {
                style: {
                  margin: '0 5px'
                },
                props: {
                  type: 'success',
                  size: 'small'
                },
                on: {
                  click: () => {
                    this.showInfoModal.show = true
                    this.showInfoModal.info = params.row
                  }
                }

              }, '详细'),
              readButton,
              h('Poptip', {
                props: {
                  confirm: true,
                  title: '您确定要删除「' + params.row.title + '」？',
                  transfer: true
                },
                on: {
                  'on-ok': () => {
                    t.deleteTagExcute(params.row.id, params.index);
                  }
                }
              }, [
                h('Button', {
                  style: {
                    margin: '0 5px'
                  },
                  props: {
                    type: 'error',
                    size: 'small',
                    placement: 'top'
                  }
                }, '删除'),
              ]),

            ])
          }
        },
      ]
    }
  },
  mounted() {
    let t = this
    t.getTableStatusExcute('admin_messages')
    t.getTableDataExcute(t.feeds.current_page)
  },
  methods: {
    getTableStatusExcute(params) {
      let t = this
      getTableStatus(params).then(res => {
        t.tableStatus.status = res.data.status
        t.tableStatus.type = res.data.type
      })
    },
    handleOnPageChange: function(to_page) {
      this.getTableDataExcute(to_page)
    },
    onPageSizeChange: function(per_page) {
      this.feeds.per_page = per_page
      this.getTableDataExcute(this.feeds.current_page)
    },
    getTableDataExcute(to_page) {
      let t = this
      t.tableLoading = true
      t.feeds.current_page = to_page
      getTableData(to_page, t.feeds.per_page, t.searchForm).then(res => {
        t.feeds.data = res.data
        t.feeds.total = res.meta.total
        t.tableLoading = false
      }, function(error) {
        t.tableLoading = false
      })
    },
    onSortChange: function(data) {
      const order = data.column.key + ',' + data.order
      this.searchForm.order_by = order
      this.getTableDataExcute(this.feeds.current_page)
    },
    readMessagesExcute(messageIds, isReadAll) {
      if (isReadAll === false && !messageIds) {
        this.$Notice.error({
          title: '出错了',
          desc: '请先选择要操作的项'
        })
        return false
      }
      let t = this
      readMessages(isReadAll, messageIds).then(res => {
        t.getTableDataExcute(t.feeds.current_page)
      })
    },
    deleteManyAdminMessageExcute(messageIds) {
      if (!messageIds) {
        this.$Notice.error({
          title: '出错了',
          desc: '请先选择要操作的项'
        })
        return false
      }
      let t = this
      deleteManyAdminMessage(messageIds).then(res => {
        t.getTableDataExcute(t.feeds.current_page)
      })
    },
    onSelectionChange: function(selection) {
      this.selectIds = ''
      for (let index in selection) {
        this.selectIds += ',' + selection[index].id
      }
    },
    deleteTagExcute(tag, key) {
      let t = this
      deleteTag(tag).then(res => {
        t.feeds.data.splice(key, 1)
        t.$Notice.success({
          title: res.message
        })
      })
    },
    addBtn() {
      this.addModal.show = true
    },
    addModalHide() {
      this.addModal.show = false
    },
    editModalHide() {
      this.editModal.show = false
    },
    showModalClose() {
      this.showInfoModal.show = false
    }
  }
}
</script>
