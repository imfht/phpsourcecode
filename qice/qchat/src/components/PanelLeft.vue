<template>
<div id="panel-left">
    <div class="header">
        <Avatar style="color: #f8f8f9;background-color: #19be6b" shape="square" size="large">
            {{ typeof(userInfo.name) == 'string' ? userInfo.name.substr(0,1) : '' }}
        </Avatar>
        <div class="info">
            <span>{{ typeof(userInfo.name) ? userInfo.name : '请登录' }}</span>

            <Dropdown trigger="click" class="info-act" placement="bottom-end" @on-click="infoAct">
                <a href="javascript:void(0)">
                    <Icon type="md-menu" size="26" /></Icon>
                </a>
                <DropdownMenu slot="list">
                    <DropdownItem name="memberManage" v-if="userInfo.manager">人员管理</DropdownItem>
                    <DropdownItem divided name="passwd">修改密码</DropdownItem>
                    <DropdownItem name="logout">退出</DropdownItem>
                </DropdownMenu>
            </Dropdown>
        </div>

        <div class="search" style="display: none;">
            <Input type="text" placeholder="">
                <Icon type="ios-search" slot="prepend"></Icon>
            </Input>
        </div>
    </div>

    <Tabs class="userlist-tab" v-model:value="tabSelected" @on-click="userListSwitch">
        <TabPane label="会话列表" name="tab1" icon="ios-chatbubbles">
            <CellGroup>
                <div class="userlist-item" v-for="(user, index) in usersList" @click="selectUser(user, index)">
                <Cell :class="{selected : selected == index}">
                    <div class="avatar" style="display: inline;">
                        <Avatar v-if="!user.group" icon="ios-person" />
                        <Avatar v-if="user.group" icon="ios-people" />
                        <!--Avatar>U</Avatar-->
                    </div>
                    <div class="" style="display: inline;">
                        <span>{{ user.name }}</span>
                    </div>

                    <Badge v-bind:count="user.unread" slot="extra" />
                </Cell>
                </div>
            </CellGroup>
        </TabPane>
        <TabPane label="人员列表" name="tab2" icon="ios-contact">
            <CellGroup>
                <div v-if="userInfo.name != user.name" v-for="(user, index) in allUsersList" @click="selectAllUser(user, index)">
                <Cell class="">
                    <div class="avatar" style="display: inline;">
                        <Avatar icon="ios-person" />
                        <!--Avatar v-if="user.online" icon="ios-person" style="background-color: #87d068;" /-->
                        <!--Avatar>U</Avatar-->
                    </div>
                    <div class="" style="display: inline;">
                        <span>{{ user.name }}</span>
                    </div>

                    <Badge v-bind:count="user.unread" slot="extra" />
                </Cell>
                </div>
            </CellGroup>
        </TabPane>
    </Tabs>
</div>        
</template>

<script>
export default {
    name: 'PanelLeft',

    props: ['usersList', 'allUsersList', 'userInfo', 'userIndex'],

    data () {
        return {
            tabSelected: 'tab1',
            selected: -1,
        }
    },

    watch: {
        userIndex: function( val, val2 ) {
            this.selected = val;
            this.$emit('userSelected', this.selected);
        }
        
    },

    methods: {
        infoAct: function(name){
            switch ( name ) {
                case 'logout':
                    sessionStorage.removeItem('user');
                    window.location.reload();
                break;

                case 'memberManage':
                    this.$emit('actionLeft', name);
                break;

                case 'passwd':
                    this.$emit('actionLeft', name);
                break;
            }
        },

        selectAllUser: function ( user, index ) {
            this.tabSelected = 'tab1';

            // 在人员列表中选择聊天对象
            for ( let i in this.usersList ) {
                if ( this.usersList[i].name == user.name ) {
                    // 人员列表中选择的在会话列表中是否存在
                    this.selected = parseInt(i);
                    
                    this.$emit('userSelected', i);
                    return ;
                }
            }
            
            // 会话列表中不存在，添加至会话列表中
            this.$emit('allUserSelected', user);
            this.selected = 0;
        },

        selectUser: function (user, index) {
            this.selected = index;
            this.$emit('userSelected', index);
        },

        userListSwitch: function ( index ) {
            
        }
    }
}
</script>

<style>
#panel-left{
    height: 100%;
    background-color: #d9d9d9;
    border-right: 1px #dcdee2 solid;
}

#panel-left .header {
    padding: 17px 7px;
    font-size: 16px;
    margin: 0 10px;
    /*border-bottom: 1px solid #989898;*/
}
#panel-left .header .info {
    display: inline-block;
}

#panel-left .header .info-act{
    position: absolute;
    right: 10px;
    top: 0;
    margin-top: 25px;
}

.userlist-tab {
    height: calc(100% - 74px);
}

.userlist-tab .ivu-tabs-nav{ width: 100%; }
.userlist-tab .ivu-tabs-tab{ width: 50%; }
.userlist-tab .ivu-tabs-tab .ivu-icon { font-size: 22px; width: 22px; height: 22px; margin-right: 1px; }

.userlist-tab .ivu-tabs-bar{ margin-bottom: 0;}
.userlist-tab .ivu-tabs-content{ height: 100%;}
.userlist-tab .ivu-tabs-tabpane{ width: 100%; height: calc(100% - 36px); overflow: hidden; overflow-y: auto; }

.userlist-tab .ivu-tabs-bar { 
    border-color: #96969a;
    box-shadow: 0px -1px #cccdce;
}

.userlist-item{
    border-top: 1px solid #ccc;
}
.userlist-item .ivu-cell-title {
    font-size: 16px;
}

#panel-left .search{
    margin: 10px 10px;
}

#panel-left .ivu-cell.selected {
    background-color: #f8f8f9;
}
</style>
