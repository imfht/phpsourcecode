
<template>
<div>
  <Row :gutter="24">
    <Col :xs="15">
    <Button type="success" icon="plus" @click="addBtn()">添加</Button>
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

  <add-component v-if='addModal.show' @on-add-modal-success='getTableDataExcute()' @on-add-modal-hide="addModalHide"></add-component>
  <edit-component v-if='editModal.show' :modal-id='editModal.id' @on-edit-modal-success='getTableDataExcute()' @on-edit-modal-hide="editModalHide"> </edit-component>

  <Modal v-model="modalCoverImage.show">
    <p slot="header">图片预览</p>
    <div class="text-center">
      <img :src="modalCoverImage.url" alt="" v-if="modalCoverImage.show" class="center-align" style="width:100%">
    </div>
    <div slot="footer">
    </div>
  </Modal>
</div>
</template>


<script>
import AddComponent from './components/add'
import EditComponent from './components/edit'

import {
  getTableData,
  destroy
} from '@/api/carousel'

export default {
  components: {
    AddComponent,
    EditComponent
  },
  data() {
    return {
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
      modalCoverImage: {
        show: false,
        url: null
      },
      columns: [{
          title: 'ID',
          key: 'id',
          sortable: true,
          minWidth: 50,
        },
        {
          title: '跳转链接',
          key: 'url',
          minWidth: 150,
        },
        {
          title: '描述',
          key: 'description',
          minWidth: 150,
        },
        {
          title: '封面',
          key: '',
          minWidth: 150,
          render: (h, params) => {
            let t = this;
            if (params.row.cover_image.url) {
              return h('div', [
                h('img', {
                  attrs: {
                    src: params.row.cover_image.url,
                    class: 'fancybox',
                    href: params.row.cover_image.url,
                    title:'图片'
                  },
                  style: {
                    width: '40px',
                    height: '40px'
                  },
                  // on: {
                  //   click: (value) => {
                  //     t.modalCoverImage.show = true;
                  //     t.modalCoverImage.url = params.row.cover_image.url;
                  //   }
                  // }
                }),
              ]);
            }
          }
        },
        {
          title: '排序',
          key: 'weight',
          minWidth: 150,
        },
        {
          title: '创建时间',
          key: 'created_at',
          sortable: true,
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
  created() {
    let t = this
    t.getTableDataExcute()
  },
  methods: {
    getTableDataExcute(to_page) {
      let t = this
      t.tableLoading = true
      getTableData(t.searchForm).then(res => {
        const response_data = res.data
        t.dataList = response_data
        t.tableLoading = false
        t.globalFancybox()
      }, function(error) {
        t.tableLoading = false
      })
    },
    onSortChange: function(data) {
      const order = data.column.key + ',' + data.order
      this.searchForm.order_by = order
      this.getTableDataExcute(1)
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
  }
}
</script>
