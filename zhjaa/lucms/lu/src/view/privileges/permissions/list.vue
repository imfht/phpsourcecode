

<template>
<div id="privileges-users-list">
  <Row :gutter="24">
    <Col :xs="8" :lg="16">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="12" :lg="4" class="hidden-mobile">
    <Input icon="search" placeholder="请输入权限名称..." v-model="searchForm.name" />
    </Col>
    <Col :xs="3" :lg="2" class="hidden-mobile">
    <Button type="primary" icon="ios-search" @click="getTableDataExcute()">Search</Button>
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
    <Table border :columns="columns" :data="dataList" @on-sort-change='onSortChange'></Table>
  </Row>

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute' @on-add-modal-hide="addModalHide"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute' @on-edit-modal-hide="editModalHide"> </edit-component>

</div>
</template>

<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getTableData,
  destroy
} from '@/api/permissions'

export default {
  components: {
    AddComponent,
    EditComponent
  },
  data() {
    return {
      searchForm: {
        order_by: 'id,desc'
      },
      tableLoading: false,
      dataList: [],
      modalHeadImage: {
        show: false,
        url: null
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
        minWidth: 100,
      }, {
        title: '权限名称',
        key: 'name',
        minWidth: 150,
      }, {
        title: '看守器',
        key: 'guard_name',
        minWidth: 150,
      }, {
        title: '权限描述',
        key: 'description',
        minWidth: 150,
      }, {
        title: '创建时间',
        key: 'created_at',
        minWidth: 150,
      }, {
        title: '更新时间',
        key: 'created_at',
        minWidth: 150,
      }, {
        title: '操作',
        minWidth: 200,
        render: (h, params) => {
          let t = this
          return h('div', [
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
                title: '您确定要删除「' + params.row.name + '」权限？',
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
      }, ]
    }
  },
  mounted() {
    this.getTableDataExcute()
  },
  methods: {
    getTableDataExcute() {
      let t = this
      t.loading = true
      getTableData(t.searchForm).then(res => {
        const response_data = res.data
        t.dataList = response_data
        t.tableLoading = false
      }, function(error) {
        t.tableLoading = false
      })
    },
    onSortChange: function(data) {
      const order = data.column.key + ',' + data.order
      this.searchForm.order_by = order
      this.getTableDataExcute()
    },
    destroyExcute(id, key) {
      let t = this
      destroy(id).then(res => {
        t.dataList.splice(key, 1)
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
    }
  }
}
</script>
