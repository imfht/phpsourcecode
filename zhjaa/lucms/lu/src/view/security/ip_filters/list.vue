<template>
<div id="privileges-users-list">
  <Row :gutter="24">
    <Col :xs="8" :lg="10">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
    </Col>
    <Col :xs="6" :lg="5" class="hidden-mobile">
    <Input icon="searchForm" placeholder="请输入ip..." v-model="searchForm.ip" />
    </Col>
    <Col :xs="4" :lg="5">
    <Select v-model="searchForm.type" placeholder="类型">
        <Option value="" key="">全部</Option>
        <Option v-for="(item,key) in tableStatus.type" :value="key" :key="key">{{ item }}</Option>
    </Select>
    </Col>
    <Col :xs="1" :lg="2">
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

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute' @on-add-modal-hide="addModalHide" :types='tableStatus.type'></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute' @on-edit-modal-hide="editModalHide" :types='tableStatus.type'> </edit-component>

</div>
</template>

<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getTableStatus
} from '@/api/common'

import {
  getTableData,
  destroy
} from '@/api/ip-filter'

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
      tableStatus: {
        type: []
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
          title: '类型',
          minWidth: 100,
          render: (h, params) => {
            var row = params.row;
            var color = 'green';
            var text = '白名单';
            if (row.type === 'black') {
              text = '黑名单';
              color = 'red';
            }

            return h('div', [
              h('Tag', {
                props: {
                  color: color
                }
              }, text)
            ]);
          }
        },
        {
          title: 'ip',
          key: 'ip',
          minWidth: 100,
        },
        {
          title: '创建时间',
          key: 'created_at',
          sortable: 'customer',
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
                  title: '您确定要删除「' + params.row.id + '」？',
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
  mounted() {
    this.getTableStatusExcute('ip_filters/type')
    this.getTableDataExcute()
  },
  methods: {
    getTableStatusExcute(params) {
      let t = this
      getTableStatus(params).then(res => {
        t.tableStatus.type = res.data
      })
    },
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
