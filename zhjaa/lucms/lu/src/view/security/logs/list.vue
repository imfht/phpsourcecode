
<template>
<div>
  <Row  :gutter="24">
    <Col :xs="8" :lg="5" class="hidden-mobile">
    <Input icon="search" placeholder="请输入昵称..." v-model="searchForm.user_name" />
    </Col>
    <Col :xs="5" :lg="4">
    <Select v-model="searchForm.type" placeholder="日志类型">
        <Option value="" key="">全部类型</Option>
        <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="5" :lg="4">
    <Select v-model="searchForm.table_name" placeholder="表" filterable>
        <Option value="" key="">全部表</Option>
        <Option v-for="(item,key) in tableStatus.table_name" :value="key" :key="key">{{ item }} </Option>
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
  <show-info v-if='showInfoModal.show' :info='showInfoModal.info' @show-modal-close="showModalClose"></show-info>


</div>
</template>


<script>
import ShowInfo from './components/show-info'

import {
  getTableStatus
} from '@/api/common'

import {
  getTableData
} from '@/api/log'

export default {
  components: {
    ShowInfo
  },
  data() {
    return {
      searchForm: {
        order_by: 'id,desc'
      },
      tableLoading: false,
      tableStatus: {
        type: [],
        table_name: [],
      },
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      showInfoModal: {
        show: false,
        info: ''
      },
      columns: [{
          title: 'ID',
          key: 'id',
          sortable: 'customer',
          minWidth: 50,
        },
        {
          title: '操作人',
          key: 'id',
          minWidth: 100,
          render: (h, params) => {
            return h('div', {
                class: 'green-color',
              },
              params.row.user.name + '--' + params.row.user.email
            )
          }
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
          title: '表',
          minWidth: 100,
          render: (h, params) => {
            return h('div',
              this.tableStatus.table_name[params.row.table_name]
            )
          }
        },
        {
          title: 'IP',
          key: 'ip',
          minWidth: 150,
        },
        {
          title: '创建时间',
          sortable: 'customer',
          key: 'created_at',
          minWidth: 150,
        },
        {
          title: '操作',
          key: '',
          minWidth: 250,
          render: (h, params) => {
            let t = this
            return h('div', [
              h('Button', {
                props: {
                  type: 'primary',
                  size: 'small'
                },
                style: {
                  marginRight: '5px'
                },
                on: {
                  click: () => {
                    t.showInfoModal.show = true
                    t.showInfoModal.info = params.row
                  }
                }

              }, '详细'),
            ])
          }
        }
      ]
    }
  },
  mounted() {
    let t = this
    t.getTableStatusExcute('logs')
    t.getTableDataExcute(t.feeds.current_page)
  },
  methods: {
    handleOnPageChange: function(to_page) {
      this.getTableDataExcute(to_page)
    },
    onPageSizeChange: function(per_page) {
      this.feeds.per_page = per_page
      this.getTableDataExcute(this.feeds.current_page)
    },
    getTableStatusExcute(params) {
      let t = this
      getTableStatus(params).then(res => {
        const response_data = res.data
        t.tableStatus.type = response_data.type
        t.tableStatus.table_name = response_data.table_name
      })
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
    showModalClose() {
      this.showInfoModal.show = false
    }
  }
}
</script>
