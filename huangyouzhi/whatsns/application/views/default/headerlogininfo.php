    
          
              {eval $user=$this->user;}
              {eval $this->load->model("course_model");$isvip = $this->course_model->getvipbyuid ( $user['uid'] );}
                               {if $isvip}
                       <style>
            header nav .user-center > ul .user a.user-list img.svip{
            width:20px;height:20px;
            top: 10px;
            right: -5px;
            }
            </style>
                {/if}
                {if $user['uid']==0}
                     <a href="{url user/login}" class="header-item">
                    登录      
                </a>
                <a href="{url user/register}" class="header-item active-color">
                    免费注册      
                </a>
                {else}
                  <a href="{url user/default}" class="header-item active-color">
                  欢迎,$user['username']
                </a>
                {if $user['groupid']<4&&$user['groupid']>0}
                     <a href="{SITE_URL}index.php?admin_main/stat" class="header-item active-color">
                    后台管理
                </a>
                  {/if}
                     <a href="{url user/logout}" class="header-item active-color">
                  退出
                </a>
                {/if}