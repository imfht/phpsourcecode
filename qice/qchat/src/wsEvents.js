export default {
    update_user( $v, msg ){
        if ( !msg.status ) {
            return $v.$Message.error(msg.msg);
        }

        $v.$Message.success(msg.msg);

        $v.formLogin.user = '';
        $v.formLogin.password = '';

        if ( msg.data ) {
            if ( typeof(msg.data.del) == 1 ) {
                $v.memberManage.data = $v.memberManage.data.filter(u => u.name != msg.data.name);
                return;
            }

            for ( let i in $v.memberManage.data ) {
                if ( $v.memberManage.data[i].name == msg.data.name ) {
                    return;
                }
            }
            $v.memberManage.data.push( msg.data );
        }
    },

    user_list( $v, msg ) {
        if ( msg.data.length == 0 ) return;

        if ( typeof(msg.data[0].online) !== 'undefined' ) {
            $v.allUsersList = msg.data;
        } else {
            $v.memberManage.data = msg.data;

            if( typeof($v.user.group) != 'undefined' ) { // 可能是拉人入群操作
                let searchData = [];
                if ( $v.user.group ) {
                    // 群
                    let members = [];
                    for ( let i in $v.user.members ) {
                        members.push($v.user.members[i].name);
                    }
                    for ( let i in msg.data) {
                        if ( members.indexOf( msg.data[i].name ) > -1 ) continue;
                        searchData.push(msg.data[i]);
                    }
                } else {
                    for ( let i in msg.data ) {
                        if ( msg.data[i].name == $v.user.name || msg.data[i].name == $v.userInfo.name ) continue;

                        searchData.push(msg.data[i]);
                    }
                }

                $v.memberSearch.data = searchData;
            }
        }
    },

    // add_friend( $v, msg ) {
    //     //$v.usersList.unshit( {'name': msg.from} );
    // },

    join_group( $v, msg ) {
        if ( msg.status != 1 ) {
            return $v.$Message.error(msg.msg);
        }

        let group = {
            'name': msg.group_name,
            'img': '',
            'unread': 0,
            'members' : msg.users,
            'group': true,
            'content': []
        };

        if ( msg.create ) {
            $v.usersList.unshift( group );

            if ( msg.from == $v.userInfo.name ) {
                if ( $v.userIndex == 0 ) {
                    $v.userSelected(0);
                    return;
                }
                $v.userIndex = 0;
            }
        } else {
            for ( let i in $v.usersList ) {
                if ( $v.usersList[i].group && $v.usersList[i].name == msg.group_name ) {
                    $v.usersList[i].members = msg.users;
                    return;
                }
            }

            $v.usersList.unshift( group );
        }

        if(msg.from != $v.userInfo.name && typeof($v.user.name) != 'undefined' ) {
            $v.userIndex += 1;
        }
    },

    passwd( $v, msg ) {
        if ( !msg.status ) {
            return $v.$Message.error(msg.msg);
        }

        $v.$Message.success(msg.msg);
        $v.formPasswd.show = false;
        $v.formPasswd.passwd = '';
        $v.formPasswd.passwd2 = '';
    },

    warn( $v, msg ) {
        $v.$Message.error(msg.msg);

        switch ( msg.code ) {
            case 1:
            sessionStorage.removeItem('user');
            $v.userInfo = {};
            $v.ws.close();
            
            setTimeout(() => {
                window.location.reload();
            }, 3500);
            break;
        }
    }
}