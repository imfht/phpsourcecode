
<template>
<div>
  <Row  :gutter="24">
    <Col :xs="6" :lg="10">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="5" :lg="4">
      <Select v-model="searchForm.port" placeholder="请选择 app">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.port" :value="key" :key="key">{{ item }}</Option>
      </Select>
    </Col>

    <Col :xs="5" :lg="4">
    <Select v-model="searchForm.system" placeholder="请选择系统">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.system" :value="key" :key="key">{{ item }}</Option>
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

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :port ="tableStatus.port" :system="tableStatus.system"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide" :port ="tableStatus.port" :system="tableStatus.system"> </edit-component>
  <show-info v-if='showInfoModal.show' :info='showInfoModal.info' @show-modal-close="showModalClose"></show-info>
</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'
import ShowInfo from './components/show-info'

import {
  getTableData,
  destroy
} from '@/api/app-version'

import {
  getTableStatus
} from '@/api/common'

export default {
  components: {
    AddComponent,
    EditComponent,
    ShowInfo
  },
  data() {
    return {
      searchForm: {
        order_by: 'created_at,desc'
      },
      tableLoading: false,
      tableStatus: {
        port: [],
        system: []
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
          title: '端口',
          minWidth: 100,
          render:(h,params) => {
            return h('div',[
              this.tableStatus.port[params.row.port]
            ])
          }
        },
        {
          title: '系统',
          minWidth: 100,
          render:(h,params) => {
            return h('div',[
              this.tableStatus.system[params.row.system]
            ])
          }
        },
        {
          title: '版本号',
          key: 'version_sn',
          sortable: 'customer',
          minWidth: 100,
        },
        {
          title: '描述',
          key: 'version_intro',
          minWidth: 100,
        },
        {
          title: '创建时间',
          key: 'created_at',
          minWidth: 100,
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
              h('Button', {
                props: {
                  type: 'success',
                  size: 'small'
                },
                style: {
                  marginRight: '5px'
                },
                on: {
                  click: () => {
                    this.editModal.show = true
                    this.editModal.id = params.row.id
                  }
                }

              }, '修改'),
              h('Poptip', {
                props: {
                  confirm: true,
                  title: '您确定要删除「' + params.row.id + '」？',
                  transfer: true
                },
                on: {
                  'on-ok': () => {
                    t.destroyExcute(params.row.id, params.index)
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
              ])
            ])
          }
        }
      ],

    }
  },
  created() {
    let t = this
    t.getTableStatusExcute('api_versions')
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
        t.tableStatus.port = res.data.port
        t.tableStatus.system = res.data.system
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
    destroyExcute(id, key) {
      let t = this
      deleteVersion(id).then(res => {
        t.feeds.data.splice(key, 1)
        t.$Notice.success({
          title: res.message
        })
      })
    },
    onSortChange: function(data) {
      const order = data.column.key + ',' + data.order
      this.searchForm.order_by = order
      this.getTableDataExcute(this.feeds.current_page)
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
  },
}
</script>
