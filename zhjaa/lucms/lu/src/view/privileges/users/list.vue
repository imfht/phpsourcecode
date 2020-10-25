
<template>
<div>
  <Row :gutter="24">
    <Col :xs="7" :lg="11">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="3" :lg="3" >
    <Select v-model="searchForm.enable" placeholder="请选择状态">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
      </Select>
    </Col>
    <Col :xs="3" :lg="3">
    <Select v-model="searchForm.is_admin" placeholder="管理员">
      <Option value="" key="">全部</Option>
      <Option v-for="(item,key) in tableStatus.is_admin" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="6" :lg="3" class="hidden-mobile">
    <Input icon="search" placeholder="请输入邮箱搜索..." v-model="searchForm.email" />
    </Col>
    <Col :xs="3" :lg="3" >
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

  <Modal v-model="modalHeadImage.show">
    <p slot="header">图片预览</p>
    <div class="text-center">
      <img :src="modalHeadImage.url" alt="" v-if="modalHeadImage.show" class="center-align" style="width:100%">
    </div>
    <div slot="footer">
    </div>
  </Modal>

  <Modal v-model="roleModal.show" :closable='false' :mask-closable=false width="800">
    <h3 slot="header" style="color:#2D8CF0">分配权限</h3>
    <Transfer v-if="roleModal.show" :data="roleModal.allRoles" :target-keys="roleModal.hasRoles" :render-format="renderFormat" :operations="['移除角色','添加角色']" :list-style="roleModal.listStyle" filterable @on-change="handleTransferChange">
    </Transfer>
    <div slot="footer">
      <Button type="text" @click="cancelRoleModal">取消</Button>
      <Button type="primary" @click="giveUserRoleExcute">保存 </Button>
    </div>
  </Modal>

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute(feeds.current_page)' @on-add-modal-hide="addModalHide"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute(feeds.current_page)' @on-edit-modal-hide="editModalHide"> </edit-component>

</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getTableData,
  getAllRole,
  getUserRoles,
  giveUserRole,
  destroy
} from '@/api/user'

import {
  getTableStatus,
  switchEnable
} from '@/api/common'

export default {
  components: {
    AddComponent,
    EditComponent
  },
  data() {
    return {
      searchForm: {
        order_by: 'created_at,desc'
      },
      tableLoading: false,
      tableStatus: {
        enable: [],
        is_admin: [],
      },
      feeds: {
        data: [],
        total: 0,
        current_page: 1,
        per_page: 10
      },
      modalHeadImage: {
        show: false,
        url: null
      },
      roleModal: {
        id: 0,
        allRoles: [],
        hasRoles: [],
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
          title: '昵称',
          key: 'name',
          minWidth: 100,
        },
        {
          title: '头像',
          key: '',
          minWidth: 150,
          render: (h, params) => {
            let t = this
            return h('div', [
              h('img', {
                attrs: {
                  src: params.row.head_image.url,
                  'data-fancybox': '12312'
                },
                class: 'head_image',
                style: {
                  width: '40px',
                  height: '40px'
                },
                on: {
                  click: (value) => {
                    t.modalHeadImage.show = true
                    t.modalHeadImage.url = params.row.head_image.url
                  }
                }
              }),
            ])
          }
        },
        {
          title: '邮箱',
          key: 'email',
          minWidth: 150,
        },
        {
          title: '后台权限',
          minWidth: 150,
          render: (h, params) => {

            const row = params.row
            const color = row.is_admin === 'T' ? 'green' : 'red'
            const text = row.is_admin === 'T' ? '可登录' : '不可登录'

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
          title: '创建时间',
          key: 'created_at',
          minWidth: 150,
        },
        {
          title: '最近登录时间',
          key: 'last_login_at',
          sortable: 'customer',
          minWidth: 150,
        },
        {
          title: '操作',
          key: '',
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
                    // let argu = {
                    //   user_id: params.row.id
                    // };
                    // this.$router.push({
                    //   name: 'edit-user',
                    //   params: argu
                    // });
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
                    t.getUserRolesExcute(params.row.id);
                    t.roleModal.show = true
                    t.roleModal.id = params.row.id
                  }
                }

              }, '角色'),

              h('Poptip', {
                props: {
                  confirm: true,
                  title: '您确定要删除「' + params.row.name + '」？',
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
    t.getTableStatusExcute('users')
    t.getAllRoleExcute()
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
        t.tableStatus.enable = res.data.enable
        t.tableStatus.is_admin = res.data.is_admin
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
      switchEnable(t.feeds.data[index].id, 'users', new_status).then(res => {
        t.feeds.data[index].enable = new_status
        t.$Notice.success({
          title: res.message
        })
      })
    },
    getAllRoleExcute() {
      let t = this
      getAllRole().then(res => {
        t.roleModal.allRoles = res.data
      })
    },
    handleTransferChange(newTargetKeys) {
      this.roleModal.hasRoles = newTargetKeys
    },
    getUserRolesExcute(id) {
      getUserRoles(id).then(res => {
        this.roleModal.hasRoles = res.data
      })
    },
    renderFormat(item) {
      return item.label + '「' + item.description + '」'
    },
    giveUserRoleExcute() {
      let t = this
      giveUserRole(t.roleModal.id, t.roleModal.hasRoles).then((res) => {
        t.$Notice.success({
          title: '操作成功',
          desc: res.message
        })
        t.roleModal.show = false
      })
    },
    cancelRoleModal() {
      let t = this
      t.roleModal.show = false
      t.roleModal.saveLoading = false
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
    }
  },
}
</script>
