components.pageServerInfo = {
  template: `
<b-card
    :inverse="false"
    class="mb-2"
    header="Gearmand servers information"
    variant="default"
  >
  <div class="row">
    <b-input-group left="Add server">
      <b-form-input v-model="tmpSvr.name" placeholder="name(eg test)" autocompleted="on"></b-form-input>
      <b-form-input v-model="tmpSvr.address" placeholder="address(eg 127.0.0.1:4730)" autocompleted="on"></b-form-input>

      <!-- Attach Right button -->
      <b-input-group-button slot="right">
        <b-button size="" variant="outline-success" @click="addServer" title="add a server"> <i class="icon-plus"></i> </b-button>
      </b-input-group-button>

    </b-input-group>
  </div>

  <div class="row my-1">
    <ol>
      <li v-for="(svr,index) in svrAry">
        {{ svr.name }} <code>{{ svr.address }}</code> <a class="btn btn-sm badge text-danger badge-pill" @click="delServer(index)">X</a>
      </li>
    </ol>
  </div>
  <div class="justify-content-center my-1 row">
    <b-button size="" variant="success" @click="getServerInfo"> <i class="icon-search"></i> Fetch Data From Remote </b-button>
  </div>

  <h3><hr> Servers information <hr></h3>

  <div class="justify-content-center my-1 row">
    <b-form-fieldset horizontal label="Filter" class="col-6" :label-size="2">
      <b-form-input v-model="svrFilter" placeholder="Type to Search"></b-form-input>
    </b-form-fieldset>
  </div>

  <b-table striped bordered hover
           :items="servers"
           :fields="serversFields"
           :filter="svrFilter">
    <template slot="address" scope="item">
      <code>{{item.value}}</code>
    </template>
    <template slot="version" scope="item">
      <code>{{item.value}}</code>
    </template>
  </b-table>

  <h3><hr> Jobs and Workers information <hr> </h3>

  <b-tabs small pills lazy ref="tabs" v-model="tabIndex">
    <b-tab title="Jobs information(Status)">
      <div class="justify-content-center my-1 row">
        <b-form-fieldset horizontal label="Filter" class="col-6" :label-size="2">
          <b-form-input v-model="stsFilter" placeholder="Type to Search"></b-form-input>
        </b-form-fieldset>
      </div>

      <b-table small bordered hover show-empty
               head-variant="success"
               :items="statusInfo"
               :fields="statusFields"
               :current-page="stsCurPage"
               :per-page="stsPerPage"
               :filter="stsFilter"
      >
        <template slot="job_name" scope="item">
          <span class="badge badge-success">{{item.value}}</span>
        </template>
        <template slot="in_queue" scope="item">
          {{item.value}}
        </template>
        <template slot="in_running" scope="item">
          {{item.value}}
        </template>
        <template slot="actions" scope="item">
          <b-btn size="sm" @click="details(item)">Details</b-btn>
        </template>
      </b-table>

      <div class="justify-content-center row my-1">
        <b-form-fieldset horizontal label="Rows per page" class="col-4" :label-size="7">
          <b-form-select :options="[{text:15,value:15},{text:20,value:20}]" v-model="stsPerPage">
          </b-form-select>
        </b-form-fieldset>
        <b-form-fieldset horizontal label="Pagination" class="col-8" :label-size="2">
          <b-pagination size="sm" :total-rows="this.statusInfo.length" :per-page="stsPerPage" v-model="stsCurPage"/>
        </b-form-fieldset>
      </div>

    </b-tab>
    <!-- Workers -->
    <b-tab title="Workers information">
      <div class="justify-content-center my-1 row">
        <b-form-fieldset horizontal label="Filter" class="col-6" :label-size="2">
          <b-form-input v-model="wkrFilter" placeholder="Type to Search"></b-form-input>
        </b-form-fieldset>
      </div>

      <!-- Main table element -->
      <b-table small bordered hover show-empty
               head-variant="success"
               :items="workersInfo"
               :fields="workersFields"
               :current-page="wkrCurPage"
               :per-page="wkrPerPage"
               :filter="wkrFilter"
      >
        <template slot="ip" scope="item">
          <code>{{item.value}}</code>
        </template>
        <template slot="job_names" scope="item">
          <code v-for="name in item.value">{{ name }}</code>
        </template>
        <template slot="actions" scope="item">
          <b-btn size="sm" @click="details(item)">Details</b-btn>
        </template>
      </b-table>

      <div class="justify-content-center row my-1">
        <b-form-fieldset horizontal label="Rows per page" class="col-4" :label-size="7">
          <b-form-select :options="[{text:15,value:15},{text:20,value:20}]" v-model="wkrPerPage">
          </b-form-select>
        </b-form-fieldset>
        <b-form-fieldset horizontal label="Pagination" class="col-8" :label-size="2">
          <b-pagination size="sm" :total-rows="this.workersInfo.length" :per-page="wkrPerPage" v-model="wkrCurPage"/>
        </b-form-fieldset>
      </div>
    </b-tab>

  </b-tabs>

</b-card>
`,
  created () {
    console.log('server-info created')
  },
  mounted () {
    console.log('server-info mounted')
    // this.$nextTick(() => {
    //   this.fetch()
    // })
  },
  updated () {
    console.log('server-info updated')
    // this.loadPlugin()
  },
  data: function () {
    return {
      statusInfo: [],
      statusFields: {
        job_name: {label: "Job name", sortable: true },
        server: {label: "Server name", sortable: true },
        in_queue: {label: "in queue", sortable: true},
        in_running: {label: "in running", sortable: true},
        capable_workers: {label: "capable workers", sortable: true}
      },
      workersInfo: [],
      workersFields: {
        id: {label: "ID", sortable: true },
        ip: {label: "IP"},
        server: {label: "Server"},
        job_names: {label: "Job list of the worker"}
      },
      tmpSvr: {name: '', address: ''},
      svrAry: [{name: 'local', address: '127.0.0.1:4730'}],
      servers: [],
      serversFields: {
        index: {label: "Index", sortable: true },
        name: {label: "Name", sortable: true },
        address: {label: "Address" },
        version: {label: "Version" }
      },
      stsCurPage: 1,
      stsPerPage: 15,
      stsFilter: null,
      wkrCurPage: 1,
      wkrPerPage: 15,
      wkrFilter: null,
      tabIndex: null,
      svrFilter: null
    }
  },
  methods: {
    fetch (servers) {
      const self = this
      vm.alert(false)

      axios.get('/?r=server-info',{
        params: {
          servers: JSON.stringify(servers)
        }
      })
        .then(({data, status}) => {
          console.log(data)

          if (data.code !== 0) {
            vm.alert(data.msg ? data.msg : 'network error!')
            return
          }

          self.servers = data.data.servers
          self.statusInfo = data.data.statusInfo
          self.workersInfo = data.data.workersInfo
      })
        .catch(err => {
          console.error(err)
          vm.alert('network error!')
      })
    },
    addServer () {
      if (!this.tmpSvr.name || !this.tmpSvr.address) {
        vm.alert('Please input server name and address')
        return
      }
      this.svrAry.push(this.tmpSvr)
      this.tmpSvr = {name: '', address: ''}
    },
    delServer(index) {
      this.svrAry.splice(index, 1)
    },
    getServerInfo () {
      if (!this.svrAry.length) {
        vm.alert('Please less add a server info')
        return
      }

      this.fetch(this.svrAry)
    }
  }// end methods
}
