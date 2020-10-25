
<template>
<div>
  <Row :gutter="24">
    <Col :xs="5" :lg="10">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="3" :lg="3">
    <Select v-model="searchForm.enable" placeholder="是否启用">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="3" :lg="4">
    <Select v-model="searchForm.advertisement_position_ids" filterable placeholder="请选择广告位类型">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in advertisementPositionsIds" :value="item.id" :key="item.id">{{ item.name }} </Option>
    </Select>
    </Col>
    <Col :xs="8" :lg="4" class="hidden-mobile">
    <Input icon="search" placeholder="请输入广告标题..." v-model="searchForm.name" />
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
  <add-component v-if='platformIsPc && addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :advertisement-positions-ids='advertisementPositionsIds'></add-component>
  <add-mobile-component v-if='!platformIsPc && addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide" :advertisement-positions-ids='advertisementPositionsIds'></add-mobile-component>
  <edit-component v-if='platformIsPc && editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide" :advertisement-positions-ids='advertisementPositionsIds'> </edit-component>
  <edit-mobile-component v-if='!platformIsPc && editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide" :advertisement-positions-ids='advertisementPositionsIds'> </edit-mobile-component>

</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'
import AddMobileComponent from './components/add-mobile'
import EditMobileComponent from './components/edit-mobile'
import ShowInfo from './components/show-info'

import {
  getTableStatus,
  switchEnable
} from '@/api/common'

import {
  getTableData,
  getAdvertisementPositions,
  destroy
} from '@/api/advertisement'

export default {
  components: {
    AddComponent,
    EditComponent,
    AddMobileComponent,
    EditMobileComponent,
    ShowInfo
  },
  data() {
    return {
      searchForm: {
        order_by: 'id,desc'
      },
      tableLoading: false,
      tableStatus: {
        enable: []
      },
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      advertisementPositionsIds: {},
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
          minWidth: 100,
        },
        {
          title: '广告标题',
          key: 'name',
          minWidth: 150,
        },
        {
          title: '封面',
          minWidth: 200,
          render: (h, params) => {
            let t = this
            return h('div', [
              h('img', {
                attrs: {
                  src: params.row.cover_image.url,
                },
                style: {
                  width: '40px',
                  height: '40px'
                },
                on: {
                  click: (value) => {}
                }
              }),
            ])
          }
        },
        {
          title: '广告位',
          minWidth: 150,
          render: (h, params) => {
            return h('div',
              params.row.advertisement_position.name
            )
          }
        },
        {
          title: '启用状态',
          key: 'enable',
          minWidth: 150,
          render: (h, params) => {
            return h('i-switch', {
              props: {
                slot: 'open',
                type: 'primary',
                value: params.row.enable === 'T', //控制开关的打开或关闭状态，官网文档属性是value
              },
              on: {
                'on-change': (value) => {
                  this.switchEnableExcute(params.index)
                }
              }
            })
          }
        },
        {
          title: '有效期',
          minWidth: 150,
          render: (h, params) => {

            const row = params.row
            var color = 'green'
            var text = row.start_at ? row.start_at : '永久有效'
            text += row.end_at ? '--' + row.end_at : ''
            if (row.overdue_time < 24 * 3600 && (row.overdue_time > 0)) {
              color = 'yellow'
            } else if (row.overdue_time < 0) {
              color = 'red'
            }

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
          minWidth: 150,
        },
        {
          title: '操作',
          minWidth: 200,
          render: (h, params) => {
            let t = this;
            return h('div', [
              h('Button', {
                style: {
                  margin: '0 5px'
                },
                props: {
                  type: 'primary',
                  size: 'small'
                },
                on: {
                  click: () => {
                    this.showInfoModal.show = true
                    this.showInfoModal.info = params.row
                  }
                }

              }, '详细'),
              h('Button', {
                props: {
                  type: 'success',
                  size: 'small'
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
                  title: '您确定要删除「' + params.row.name + '」广告？',
                  transfer: true
                },
                on: {
                  'on-ok': () => {
                    t.destroyExcute(params.row.id, params.index);
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
        },
      ]
    }
  },
  created() {
    let t = this
    t.getTableStatusExcute('advertisements/enable')
    t.getAdvertisementPositionsExcute()
    t.getTableDataExcute(t.feeds.current_page)
  },
  computed: {
    platformIsPc: function() {
      return this.globalPlatformType() == 'pc' ? true : false
    }
  },
  methods: {
    handleOnPageChange: function(to_page) {
      this.getTableDataExcute(to_page)
    },
    onPageSizeChange: function(per_page) {
      this.feeds.per_page = per_page
      this.getTableDataExcute(per_page)
    },
    getTableStatusExcute(params) {
      let t = this
      getTableStatus(params).then(res => {
        t.tableStatus.enable = res.data
      })
    },
    getAdvertisementPositionsExcute() {
      let t = this
      getAdvertisementPositions().then(res => {
        t.advertisementPositionsIds = res.data
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
    switchEnableExcute(index) {
      let t = this
      let new_status = 'T'
      if (t.feeds.data[index].enable === 'T') {
        new_status = 'F'
      }
      switchEnable(t.feeds.data[index].id, 'advertisements', new_status).then(res => {
        t.feeds.data[index].enable = new_status
        t.$Notice.success({
          title: res.message
        })
      })
    },
    destroyExcute(id, key) {
      let t = this
      destroy(id).then(res => {
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
