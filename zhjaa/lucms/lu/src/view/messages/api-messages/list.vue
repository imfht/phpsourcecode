
<template>
<div>
  <Row :gutter="24">
    <Col :xs="9" :lg="6">
    <Button type="success" icon="plus" @click="addBtn()">发消息</Button>
    </Col>
    <Col :xs="6" :lg="6" class="hidden-mobile">
    <Input icon="search" placeholder="请输入用户电话..." v-model="searchForm.phone" />
    </Col>
    <Col :xs="3" :lg="5">
    <Select v-model="searchForm.status" placeholder="状态">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.status" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="3" :lg="4">
    <Select v-model="searchForm.type" placeholder="消息类型">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="1" :lg="2">
    <Button type="primary" icon="ios-search" @click="getTableDataExcute(feeds.current_page)">Search</Button>
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
    <Table border :columns="columns" :data="feeds.data" @on-sort-change='onSortChange'></Table>
    <div style="margin: 10px;overflow: hidden">
      <div style="float: right;">
        <Page :total="feeds.total" :current="feeds.current_page" :page-size="feeds.per_page" class="paging" show-elevator show-total show-sizer @on-change="handleOnPageChange" @on-page-size-change='onPageSizeChange'></Page>
      </div>
    </div>
  </Row>

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :message-type='tableStatus.type'></add-component>

  <show-info v-if='showInfoModal.show' :info='showInfoModal.info' @show-modal-close="showModalClose"></show-info>
</div>
</template>

<script>
import AddComponent from './components/publish-message'

import {
  getTableData,
  publishApiMessage
} from '@/api/api-message'

import ShowInfo from './components/show-info'
import {
  getTableStatus
} from '@/api/common'

export default {
  components: {
    AddComponent,
    ShowInfo
  },
  data() {
    return {
      searchForm: {
        order_by: 'id,desc'
      },
      showInfoModal: {
        show: false,
        info: ''
      },
      tableStatus: {
        type: '',
        status: ''
      },
      tableLoading: false,
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
          title: '用户',
          minWidth: 100,
          render: (h, params) => {
            return h('div',
              params.row.user.name + ' (' + params.row.user.phone + ')'
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
            ])
          }
        },
      ]
    }
  },
  mounted() {
    let t = this
    t.getTableStatusExcute('api_messages')
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
    showModalClose() {
      this.showInfoModal.show = false
    }
  }
}
</script>
