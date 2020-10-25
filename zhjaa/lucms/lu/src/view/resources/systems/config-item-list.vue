

<template>
<div>
  <Row :gutter="24">
    <Col :xs="8" :lg="3">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="6" :lg="4" class="hidden-mobile">
    <Input icon="searchForm" placeholder="请输入配置标识..." v-model="searchForm.flag" />
    </Col>
    <Col :xs="0" :lg="4" class="hidden-mobile">
    <Input icon="searchForm" placeholder="请输入配置标题..." v-model="searchForm.title" />
    </Col>
    <Col :xs="3" :lg="4">
    <Select v-model="searchForm.enable" placeholder="请选择状态">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.enable" :value="key" :key="key">{{ item }}</Option>
      </Select>
    </Col>
    <Col :xs="3" :lg="4">
    <Select v-model="searchForm.group" placeholder="请选择配置分组">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.system_config_group" :value="key" :key="key">{{ item.title }}</Option>
      </Select>
    </Col>
    <Col :xs="1" :lg="2" >
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

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute()' @on-add-modal-hide="addModalHide" :config-type="tableStatus.system_config_type" :config-group="tableStatus.system_config_group"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute()' @on-edit-modal-hide="editModalHide" :config-type="tableStatus.system_config_type" :config-group="tableStatus.system_config_group"> </edit-component>

</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getTableData,
  getGroup,
  destroy,
} from '@/api/systems'

import {
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
        enable: '',
        falg: '',
        title: '',
        group: '',
        order_by: 'id,desc'
      },
      tableLoading: false,
      dataList: [],
      tableStatus: {
        system_config_group: [],
        system_config_type: [],
        enable: []
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
          title: '标识',
          key: 'description',
          minWidth: 100,
        },
        {
            title: '配置值',
            key: 'value',
          minWidth: 100,
        },
        {
          title: '启用状态',
          key: 'enable',
          minWidth: 100,
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
          title: '描述',
          key: 'flag',
          minWidth: 100,
        },
        {
          title: '创建时间',
          key: 'created_at',
          sortable: 'customer',
          minWidth: 100,
        },
        {
          title: '更新时间',
          key: 'created_at',
          minWidth: 100,
        },
        {
          title: '操作',
          minWidth: 150,
          render: (h, params) => {
            let t = this;
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
                  title: '您确定要删除「' + params.row.title + '」？',
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
    t.getGroupExcute()
    t.getTableDataExcute()
  },
  methods: {
    getGroupExcute(to_page) {
      let t = this
      getGroup(t.searchForm).then(res => {
        const response_data = res.data
        t.tableStatus.system_config_type = response_data.type
        t.tableStatus.system_config_group = response_data.group
        t.tableStatus.enable = response_data.enable
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
    },
    switchEnableExcute(index) {
      let t = this
      let new_status = 'T'
      if (t.dataList[index].enable === 'T') {
        new_status = 'F'
      }
      switchEnable(t.dataList[index].id, 'system_configs', new_status).then(res => {
        t.dataList[index].enable = new_status
        t.$Notice.success({
          title: res.message
        })
      })
    },
  }
}
</script>
