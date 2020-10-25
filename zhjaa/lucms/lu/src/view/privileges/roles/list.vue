<template>
<div>
  <Row :gutter="24">
    <Col :xs="8" :lg="16">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="12" :lg="4" class="hidden-mobile">
    <Input icon="search" placeholder="请输入角色名称..." v-model="searchForm.name" />
    </Col>
    <Col :xs="3" :lg="3" class="hidden-mobile">
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

  <Modal v-model="permissionModal.show" :closable='false' :mask-closable=false width="800">
    <h3 slot="header" style="color:#2D8CF0">分配权限</h3>
    <Transfer v-if="permissionModal.show" :data="permissionModal.allPermissions" :target-keys="permissionModal.hasPermissions" :render-format="renderFormat" :operations="['移除权限','添加权限']" :list-style="permissionModal.listStyle" filterable @on-change="handleTransferChange">
    </Transfer>
    <div slot="footer">
      <Button type="text" @click="cancelPermissionModal">取消</Button>
      <Button type="primary" @click="giveRolePermissionExcute">保存 </Button>
    </div>
  </Modal>

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute' @on-add-modal-hide="addModalHide"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute' @on-edit-modal-hide="editModalHide"> </edit-component>

</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getAllPermission,
  getTableData,
  getRolePermissions,
  giveRolePermission,
  destroy
} from '@/api/roles'

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
      permissionModal: {
        id: 0,
        allPermissions: [],
        hasPermissions: [],
        show: false,
        saveLoading: false,
        listStyle: {
          width: '250px',
          height: '300px'
        }
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
        },
        {
          title: '角色限名称',
          key: 'name',
          minWidth: 150,
        },
        {
          title: '角色看守器',
          key: 'guard_name',
          minWidth: 150,
        },
        {
          title: '角色描述',
          key: 'description',
          minWidth: 150,
        },
        {
          title: '创建时间',
          key: 'created_at',
          minWidth: 150,
        },
        {
          title: '更新时间',
          key: 'created_at',
          minWidth: 150,
        },
        {
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
              h('Button', {
                props: {
                  type: 'info',
                  size: 'small'
                },
                style: {
                  marginRight: '5px'
                },
                on: {
                  click: () => {
                    t.getRolePermissionsExcute(params.row.id)
                    t.permissionModal.show = true
                    t.permissionModal.id = params.row.id
                  }
                }

              }, '权限'),

              h('Poptip', {
                props: {
                  confirm: true,
                  title: '您确定要删除「' + params.row.name + '」角色？',
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
          },
        },
      ]
    }
  },
  created() {
    let t = this
    t.getTableDataExcute()
    t.getAllPermissionExcute()
  },
  methods: {
    renderFormat(item) {
      return item.label + '「' + item.description + '」'
    },
    cancelPermissionModal() {
      let t = this
      t.permissionModal.show = false
      t.permissionModal.saveLoading = false
    },
    getAllPermissionExcute() {
      let t = this
      getAllPermission().then(res => {
        t.permissionModal.allPermissions = res.data
      }, function(error) {})
    },
    getTableDataExcute() {
      let t = this
      t.tableLoading = true
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
    handleTransferChange(newTargetKeys) {
      this.permissionModal.hasPermissions = newTargetKeys
    },
    getRolePermissionsExcute(id) {
      let t = this
      getRolePermissions(id).then(res => {
        t.permissionModal.hasPermissions = res.data
      })
    },
    giveRolePermissionExcute() {
      let t = this
      giveRolePermission(t.permissionModal.id, t.permissionModal.hasPermissions).then(res => {
        t.$Notice.success({
          title: res.message
        })
        t.permissionModal.show = false
      })
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
