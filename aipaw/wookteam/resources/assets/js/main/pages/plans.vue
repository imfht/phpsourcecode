<template>
    <div class="page-plans">
        <v-title>{{$L('选择合适你的 WookTeam')}}</v-title>

        <div class="top-bg"></div>

        <div class="top-menu">
            <div class="header">
                <div class="z-row">
                    <div class="header-col-sub">
                        <h2 @click="goForward({path: '/'})">
                            <img v-if="systemConfig.logo" :src="systemConfig.logo">
                            <img v-else src="../../../statics/images/logo-white.png">
                            <span>{{$L('轻量级的团队在线协作')}}</span>
                        </h2>
                    </div>
                    <div class="z-1">
                        <dl>
                            <dd>
                                <a v-if="systemConfig.github=='show'" class="right-info" target="_blank" href="https://github.com/kuaifan/wookteam">
                                    <Icon class="right-icon" type="logo-github"/>
                                </a>
                                <Dropdown class="right-info" trigger="hover" @on-click="setLanguage" transfer>
                                    <div>
                                        <Icon class="right-icon" type="md-globe"/>
                                        <Icon type="md-arrow-dropdown"/>
                                    </div>
                                    <Dropdown-menu slot="list">
                                        <Dropdown-item name="zh" :selected="getLanguage() === 'zh'">中文</Dropdown-item>
                                        <Dropdown-item name="en" :selected="getLanguage() === 'en'">English</Dropdown-item>
                                    </Dropdown-menu>
                                </Dropdown>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="banner">
            <div class="banner-title">
                {{$L('选择适合您的团队、企业的版本')}}
            </div>
            <div class="banner-desc">
                {{$L('WookTeam 是新一代企业协作平台，您可以根据您企业的业务需求，选择合适的产品功能。')}} <br>
                {{$L('从现在开始，WookTeam 为世界各地的团队提供支持，探索适合您的选项。')}}
            </div>
            <div class="plans-table">
                <div class="plans-table-bd plans-table-info">
                    <div class="plans-table-item first">
                        <div class="plans-table-info-th"></div>
                        <div class="plans-table-info-price"><em>{{$L('价格')}}</em></div>
                        <div class="plans-table-info-desc"><em>{{$L('概述')}}</em></div>
                        <div class="plans-table-info-desc"><em>{{$L('人数')}}</em></div>
                        <div class="plans-table-info-btn"></div>
                    </div>
                    <div @mouseenter="active=1" class="plans-table-item" :class="{active:active==1}">
                        <div class="plans-table-info-th">{{$L('团队版')}}</div>
                        <div class="plans-table-info-price">
                            <img class="plans-version" src="../../../statics/images/plans/free.png">
                            <div class="currency"><em>0</em></div>
                        </div>
                        <div class="plans-table-info-desc">{{$L('适用于轻团队的任务协作')}}</div>
                        <div class="plans-table-info-desc">{{$L('无限制')}}</div>
                        <div class="plans-table-info-btn">
                            <div class="plans-info-btns">
                                <Tooltip :content="$L('账号：%、密码：%', 'admin', '123456')" transfer>
                                    <a href="https://demo.wookteam.com" class="btn" target="_blank">{{$L('体验DEMO')}}</a>
                                </Tooltip>
                                <a href="https://github.com/kuaifan/wookteam" class="github" target="_blank"><Icon type="logo-github"/></a>
                            </div>
                        </div>
                    </div>
                    <div @mouseenter="active=2" class="plans-table-item" :class="{active:active==2}">
                        <div class="plans-table-info-th">{{$L('企业版')}} <span>{{$L('推荐')}}</span></div>
                        <div class="plans-table-info-price">
                            <img class="plans-version" src="../../../statics/images/plans/pro.png">
                            <div class="currency"><em>18800</em></div>
                        </div>
                        <div class="plans-table-info-desc">{{$L('适用于群组共享和高级权限')}}</div>
                        <div class="plans-table-info-desc">{{$L('无限制')}}</div>
                        <div class="plans-table-info-btn">
                            <Tooltip :content="$L('账号：%、密码：%', 'admin', '123456')" transfer>
                                <a href="https://pro.wookteam.com" class="btn" target="_blank">{{$L('体验DEMO')}}</a>
                            </Tooltip>
                        </div>
                    </div>
                    <div @mouseenter="active=3" class="plans-table-item" :class="{active:active==3}">
                        <div class="plans-table-info-th">{{$L('定制版')}}</div>
                        <div class="plans-table-info-price">
                            <img class="plans-version" src="../../../statics/images/plans/Ultimate.png">
                            <div class="currency"><em class="custom">{{$L('自定义')}}</em></div>
                        </div>
                        <div class="plans-table-info-desc">{{$L('根据您的需求量身定制')}}</div>
                        <div class="plans-table-info-desc">{{$L('无限制')}}</div>
                        <div class="plans-table-info-btn">
                            <a href="javascript:void(0)" class="btn btn-contact" @click="contactShow=true">{{$L('联系我们')}}</a>
                        </div>
                    </div>
                </div>
                <div class="plans-accordion-head" :class="{'plans-accordion-close':!body1}" @click="body1=!body1">
                    <div class="first"><span>{{$L('应用支持')}}</span></div>
                    <div @mouseenter="active=1" class="plans-table-item" :class="{active:active==1}"></div>
                    <div @mouseenter="active=2" class="plans-table-item" :class="{active:active==2}"></div>
                    <div @mouseenter="active=3" class="plans-table-item" :class="{active:active==3}"></div>
                    <span><Icon type="ios-arrow-down" /></span>
                </div>
                <div v-if="body1" class="plans-accordion-body">
                    <div class="plans-table-bd plans-table-app">
                        <div class="plans-table-item first">
                            <div class="plans-table-td">{{$L('项目管理')}}</div>
                            <div class="plans-table-td">{{$L('文档/知识库')}}</div>
                            <div class="plans-table-td">{{$L('团队管理')}}</div>
                            <div class="plans-table-td">IM{{$L('聊天')}}</div>
                            <div class="plans-table-td">{{$L('子任务')}}</div>
                            <div class="plans-table-td">{{$L('国际化')}}</div>
                            <div class="plans-table-td">{{$L('甘特图')}}</div>
                            <div class="plans-table-td">{{$L('任务动态')}}</div>
                            <div class="plans-table-td">{{$L('导出任务')}}</div>
                            <div class="plans-table-td">{{$L('日程')}}</div>
                            <div class="plans-table-td">{{$L('周报/日报')}}</div>

                            <div class="plans-table-td">{{$L('IM群聊')}}</div>
                            <div class="plans-table-td">{{$L('项目群聊')}}</div>
                            <div class="plans-table-td">{{$L('项目权限')}}</div>
                            <div class="plans-table-td">{{$L('项目搜索')}}</div>
                            <div class="plans-table-td">{{$L('任务类型')}}</div>
                            <div class="plans-table-td">{{$L('知识库搜索')}}</div>
                            <div class="plans-table-td">{{$L('团队分组')}}</div>
                            <div class="plans-table-td">{{$L('分组权限')}}</div>
                            <div class="plans-table-td">{{$L('成员统计')}}</div>
                            <div class="plans-table-td">{{$L('签到功能')}}</div>
                        </div>
                        <div @mouseenter="active=1" class="plans-table-item" :class="{active:active==1}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                            <div class="plans-table-td"> - </div>
                        </div>
                        <div @mouseenter="active=2" class="plans-table-item" :class="{active:active==2}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                        </div>
                        <div @mouseenter="active=3" class="plans-table-item" :class="{active:active==3}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                        </div>
                    </div>
                </div>
                <div class="plans-accordion-head" :class="{'plans-accordion-close':!body2}" @click="body2=!body2">
                    <div class="first"><span>{{$L('服务支持')}}</span></div>
                    <div @mouseenter="active=1" class="plans-table-item" :class="{active:active==1}"></div>
                    <div @mouseenter="active=2" class="plans-table-item" :class="{active:active==2}"></div>
                    <div @mouseenter="active=3" class="plans-table-item" :class="{active:active==3}"></div>
                    <span><Icon type="ios-arrow-down" /></span>
                </div>
                <div v-if="body2" class="plans-accordion-body">
                    <div class="plans-table-bd plans-table-app plans-table-service">
                        <div class="plans-table-item first">
                            <div class="plans-table-td">{{$L('自助支持')}} <span>{{$L('（Issues/文档/社群）')}}</span></div>
                            <div class="plans-table-td">{{$L('支持私有化部署')}}</div>
                            <div class="plans-table-td">{{$L('绑定自有域名')}}</div>
                            <div class="plans-table-td">{{$L('二次开发')}}</div>
                            <div class="plans-table-td">{{$L('在线咨询支持')}}</div>
                            <div class="plans-table-td">{{$L('电话咨询支持')}}</div>
                            <div class="plans-table-td">{{$L('中英文邮件支持')}}</div>
                            <div class="plans-table-td">{{$L('一对一客户顾问')}}</div>
                            <div class="plans-table-td">{{$L('产品培训')}}</div>
                            <div class="plans-table-td">{{$L('上门支持')}}</div>
                            <div class="plans-table-td">{{$L('专属客户成功经理')}}</div>
                            <div class="plans-table-td">{{$L('免费提供一次企业内训')}}</div>
                            <div class="plans-table-td">{{$L('明星客户案例')}}</div>
                            <div class="plans-table-info-btn"></div>
                        </div>
                        <div @mouseenter="active=1" class="plans-table-item" :class="{active:active==1}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-info-btn">
                                <div class="plans-info-btns">
                                    <Tooltip :content="$L('账号：%、密码：%', 'admin', '123456')" transfer>
                                        <a href="https://demo.wookteam.com" class="btn" target="_blank">{{$L('体验DEMO')}}</a>
                                    </Tooltip>
                                    <a href="https://github.com/kuaifan/wookteam" class="github" target="_blank"><Icon type="logo-github"/></a>
                                </div>
                            </div>
                        </div>
                        <div @mouseenter="active=2" class="plans-table-item" :class="{active:active==2}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-td"><span> - </span></div>
                            <div class="plans-table-info-btn">
                                <Tooltip :content="$L('账号：%、密码：%', 'admin', '123456')" transfer>
                                    <a href="https://pro.wookteam.com" class="btn" target="_blank">{{$L('体验DEMO')}}</a>
                                </Tooltip>
                            </div>
                        </div>
                        <div @mouseenter="active=3" class="plans-table-item" :class="{active:active==3}">
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-td"><Icon type="md-checkmark" /></div>
                            <div class="plans-table-info-btn">
                                <a href="javascript:void(0)" class="btn btn-contact" @click="contactShow=true">{{$L('联系我们')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="fluid-info fluid-info-1">
                <div class="fluid-info-item">
                    <div class="info-title">
                        {{$L('多种部署方式随心选择')}}
                    </div>
                    <div class="info-function">
                        <div class="func-item">
                            <div class="image">
                                <img src="../../../statics/images/plans/1.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('公有云')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('无需本地环境准备，按需购买帐户，专业团队提供运维保障服务，两周一次的版本迭代')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image">
                                <img src="../../../statics/images/plans/2.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('私有云')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('企业隔离的云服务器环境，高可用性，网络及应用层完整隔离，数据高度自主可控')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image image-80">
                                <img src="../../../statics/images/plans/3.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('本地服务器')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('基于 Docker 的容器化部署，支持高可用集群，快速弹性扩展，数据高度自主可控')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fluid-info">
                <div class="fluid-info-item">
                    <div class="info-title">
                        {{$L('完善的服务支持体系')}}
                    </div>
                    <div class="info-function">
                        <div class="func-item">
                            <div class="image">
                                <img src="../../../statics/images/plans/4.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('1:1客户成功顾问')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('资深客户成功顾问对企业进行调研、沟通需求、制定个性化的解决方案，帮助企业落地')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image image-80">
                                <img src="../../../statics/images/plans/5.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('完善的培训体系')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('根据需求定制培训内容，为不同角色给出专属培训方案，线上线下培训渠道全覆盖')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image">
                                <img src="../../../statics/images/plans/6.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('全面的支持服务')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('多种支持服务让企业无后顾之忧，7*24 线上支持、在线工单、中英文邮件支持、上门支持')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fluid-info fluid-info-3">
                <div class="fluid-info-item">
                    <div class="info-title">
                        {{$L('多重安全策略保护数据')}}
                    </div>
                    <div class="info-function">
                        <div class="func-item">
                            <div class="image">
                                <img src="../../../statics/images/plans/7.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('高可用性保证')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('多重方式保证数据不丢失，高可用故障转移，异地容灾备份，99.99%可用性保证')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image image-80">
                                <img src="../../../statics/images/plans/8.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('数据加密')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('多重方式保证数据不泄漏，基于 TLS 的数据加密传输，DDOS 防御和入侵检测')}}
                                </div>
                            </div>
                        </div>
                        <div class="func-item">
                            <div class="image image-50">
                                <img src="../../../statics/images/plans/9.svg">
                            </div>
                            <div class="func-desc">
                                <div class="desc-title">
                                    {{$L('帐户安全')}}
                                </div>
                                <div class="desc-text">
                                    {{$L('多重方式保证帐户安全，远程会话控制，设备绑定，安全日志以及手势密码')}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-footer"><span>WookTeam &copy; 2018-2020</span></div>

        <Modal
            v-model="contactShow"
            :title="$L('联系我们')"
            class-name="simple-modal"
            width="430"
            footer-hide>
            <div class="contact-modal">
                <p>{{$L('如有任何问题，欢迎使用微信与我们联系。')}}</p>
                <p><img src="../../../statics/images/plans/wechat.png"></p>
            </div>
        </Modal>
    </div>
</template>

<style lang="scss" scoped>
.contact-modal {
    p {
        padding: 0;
        margin: 0;
        font-size: 16px;
        text-align: center;
        img {
            display: inline-block;
            width: 248px;
        }
    }
}
</style>
<style lang="scss" scoped>
.page-plans {
    .top-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 640px;
        padding-top: 192px;
        z-index: 0;
        background: url(../../../statics/images/plans/banner-bg.png) center top no-repeat;
        background-size: 100% 100%;
    }

    .top-menu {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 2;
        .header {
            height: 50px;
            padding-top: 12px;
            max-width: 1280px;
            margin: 0 auto;
            .z-row {
                color: #fff;
                height: 50px;
                position: relative;
                z-index: 2;
                max-width: 1680px;
                margin: 0 auto;
                .header-col-sub {
                    width: 500px;
                    h2 {
                        position: relative;
                        padding: 1rem 0 0 1rem;
                        display: flex;
                        align-items: flex-end;
                        cursor: pointer;
                        img {
                            width: 150px;
                            margin-right: 6px;
                        }
                        span {
                            font-size: 12px;
                            font-weight: normal;
                            color: #ffffff;
                            line-height: 14px;
                        }
                    }
                }
                .z-1 {
                    dl {
                        position: absolute;
                        right: 20px;
                        top: 0;
                        font-size: 14px;
                        dd {
                            line-height: 50px;
                            color: #fff;
                            cursor: pointer;
                            margin-right: 1px;
                            .right-info {
                                display: inline-block;
                                cursor: pointer;
                                margin-left: 12px;
                                color: #ffffff;
                                .right-icon {
                                    font-size: 26px;
                                    vertical-align: middle;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    .banner {
        position: relative;
        z-index: 1;
        padding-top: 192px;
        .banner-title {
            font-size: 50px;
            text-align: center;
            padding: 0 10px;
            color: #fff;
        }
        .banner-desc {
            font-size: 14px;
            color: #fff;
            text-align: center;
            padding: 0 25px;
            max-width: 940px;
            margin-left: auto;
            margin-right: auto;
            margin-top: 40px;
            line-height: 30px;
        }

        .plans-table {
            max-width: 1120px;
            margin: 110px auto 100px;
            box-shadow: 0 10px 30px rgba(172, 184, 207, 0.3);
            em {
                font-style: normal;
                font-size: 14px;
                color: #666666;
            }
            .plans-table-bd {
                background-color: #fff;
                display: flex;
                .plans-table-item {
                    flex: 1;
                    border-left: 1px solid #eee;
                    position: relative;
                    z-index: 1;
                    & > div {
                        transition: background 0.3s;
                        border-bottom: 1px solid #eee;
                        &:first-child,
                        &:last-child {
                            border-bottom: none;
                        }
                    }
                    &:first-child {
                        flex: none;
                        width: 27.7%;
                        border-left: none;
                    }
                    &::before {
                        content: "";
                        position: absolute;
                        width: 100%;
                        height: 100%;
                        left: 0;
                        top: 0;
                        background: transparent;
                        border-radius: 0;
                        z-index: -2;
                        transform: scaleY(1);
                        transition: all 0.3s;
                    }
                    &.active {
                        position: relative;
                        border-left-color: transparent;
                        & > div {
                            border-color: transparent !important;
                            background: transparent;
                        }
                        &::before {
                            z-index: -1;
                            border-radius: 2px;
                            background: #fff;
                            transform: scaleY(1.05);
                            box-shadow: 0 10px 30px rgba(172, 184, 207, 0.3);
                        }
                        & + .plans-table-item {
                            border-left-color: transparent;
                        }
                    }
                }
            }
            .plans-table-app {
                .plans-table-td {
                    height: 60px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    &:first-child {
                        border-bottom: 1px solid #eee !important;
                    }
                    > span {
                        font-family:-apple-system, Arial, sans-serif;
                    }
                }
                .plans-table-item {
                    .plans-table-td {
                        position: relative;
                        i {
                            color: #22d7bb;
                            font-size: 20px;
                        }
                        & > .info {
                            position: absolute;
                            font-size: 12px;
                            color: #888;
                            top: 50%;
                            left: 50%;
                            transform: translate(30%, -50%);
                        }
                    }
                    .plans-table-info-btn {
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        height: 100px;
                    }
                    &.first {
                        .plans-table-td {
                            font-size: 14px;
                            color: #666;
                            i {
                                width: 34px;
                                font-size: 20px;
                                text-align: center;
                                transform: translateX(-5px);
                            }
                            &:nth-child(1) {
                                i {
                                    color: #ff7747;
                                }
                            }
                            &:nth-child(2) {
                                i {
                                    color: #f669a7;
                                }
                            }
                            &:nth-child(3) {
                                i {
                                    color: #ffa415;
                                }
                            }
                            &:nth-child(4) {
                                i {
                                    color: #2dbcff;
                                }
                            }
                            &:nth-child(5) {
                                i {
                                    color: #66c060;
                                }
                            }
                            &:nth-child(6) {
                                i {
                                    color: #99d75a;
                                }
                            }
                            &:nth-child(7) {
                                i {
                                    color: #4e8af9;
                                }
                            }
                            &:nth-child(8) {
                                i {
                                    color: #ff5b57;
                                }
                            }
                            &.plans-table-app-okr {
                                position: relative;
                                &::after {
                                    content: "(OKR)";
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(90%, -50%);
                                }
                            }
                        }
                    }
                }
            }
            .plans-table-info-flex {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .plans-table-info {
                .plans-table-info-th {
                    height: 70px;
                    background-color: #eef2f8;
                    font-size: 16px;
                    color: #485778;
                    line-height: 70px;
                    text-align: center;
                    font-weight: 600;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    span {
                        height: 18px;
                        line-height: 18px;
                        font-size: 14px;
                        padding: 0 8px;
                        background-color: #fa3d3f;
                        border-radius: 2px;
                        color: #fff;
                        font-weight: normal;
                        margin-left: 7px;
                    }
                }
                .plans-table-info-price {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    height: 265px;
                    .plans-version {
                        margin-bottom: 30px;
                    }
                    .currency {
                        height: 35px;
                        position: relative;
                        margin-bottom: 18px;
                        &::before {
                            content: "￥";
                            color: #485778;
                            position: absolute;
                            font-size: 18px;
                            left: 0;
                            top: 0;
                            transform: translate(-110%, -20%);
                        }
                        > em {
                            font-size: 36px;
                            font-weight: 900;
                            display: inline-block;
                            margin-top: -10px;
                            height: 56px;
                            line-height: 56px;
                            &.custom {
                                font-size: 24px;
                                font-weight: 500;
                            }
                        }
                    }
                }
                .plans-table-info-desc {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    height: 70px;
                    font-size: 14px;
                    color: #aaaaaa;
                }
            }
            .plans-table-info-btn {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 115px;
                .plans-info-btns {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    .btn {
                        padding: 14px 36px;
                    }
                    .github {
                        margin-left: 10px;
                        & > i {
                            font-size: 32px;
                        }
                    }
                }
                .btn {
                    display: inline-block;
                    color: #fff;
                    background-color: #348FE4;
                    border-color: #348FE4;
                    padding: 14px 54px;
                    font-size: 14px;
                    line-height: 14px;
                    border-radius: 30px;
                    outline: none;
                    &.btn-contact {
                        background-color: #6BC853;
                        border-color: #6BC853;
                    }
                }
            }
            .plans-accordion-head {
                height: 60px;
                line-height: 60px;
                background-color: #eef2f8;
                position: relative;
                z-index: 2;
                display: flex;
                cursor: pointer;
                & > div {
                    width: 27.7%;
                    flex: 1;
                    &.first {
                        width: 27.7%;
                        flex: none;
                        & > span {
                            font-weight: 600;
                            color: #333333;
                            font-size: 14px;
                            padding-left: 30px;
                        }
                    }
                }
                & > span {
                    position: absolute;
                    top: 0;
                    right: 30px;
                    line-height: 60px;
                    height: 60px;
                    transition: transform 0.3s;
                    i {
                        font-size: 20px;
                        color: #aaa;
                    }
                }
                &.plans-accordion-close {
                    & > span {
                        transform: rotate(90deg);
                    }
                }
            }
        }
    }

    .container-fluid {
        margin-left: auto;
        margin-right: auto;
        .fluid-info {
            &.fluid-info-1 {
                border-bottom: 1px solid #dddddd;
            }
            &.fluid-info-3 {
                background: url(../../../statics/images/plans/bg_04.jpg);
                background-size: 100% 100%;
            }
            .fluid-info-item {
                max-width: 1120px;
                margin: 0 auto;
                height: 780px;
                padding: 130px 0;
                .info-title {
                    text-align: center;
                    font-size: 42px;
                    color: #333333;
                    margin-bottom: 110px;
                }
                .info-function {
                    .func-item {
                        float: left;
                        width: 33%;
                        text-align: center;
                        padding: 0 40px;
                        .image {
                            height: 215px;
                            margin: 0 auto 40px;
                            img {
                                width: 63%;
                            }
                            &.image-80 {
                                img {
                                    width: 78%;
                                }
                            }
                            &.image-50 {
                                img {
                                    width: 50%;
                                }
                            }
                        }
                        .func-desc {
                            .desc-title {
                                font-size: 16px;
                                color: #333333;
                                margin-bottom: 27px;
                                font-weight: 600;
                            }
                            .desc-text {
                                color: #888888;
                                line-height: 24px;
                            }
                        }
                    }
                }
            }
        }
    }

    .contact-footer {
        margin: 20px 0;
        text-align: center;
        color: #333;

        a, span {
            color: #333;
            margin-left: 10px;
        }
    }
}
</style>
<script>
export default {
    data() {
        return {
            active: 2,

            body1: true,
            body2: true,

            contactShow: false,
            systemConfig: $A.jsonParse($A.storage("systemSetting")),
        }
    },

    created() {
        this.addLanguageData({
            "en": {
                "选择合适你的 WookTeam": "You choose the right WookTeam",
                "选择适合您的团队、企业的版本": "Choose your team, the enterprise version",
                "WookTeam 是新一代企业协作平台，您可以根据您企业的业务需求，选择合适的产品功能。": "WookTeam is a new generation of enterprise collaboration platform, you can according to your business needs, choose the right product features.",
                "从现在开始，WookTeam 为世界各地的团队提供支持，探索适合您的选项。": "From now on, WookTeam to support teams around the world, to explore the option for you.",
                "价格": "Price",
                "概述": "Outline",
                "人数": "Number of people",
                "社区版": "Community Edition",
                "团队版": "Team Edition",
                "适用于轻团队的任务协作": "Suitable for light task team collaboration",
                "无限制": "Unlimited",
                "账号：%、密码：%": "Account password:%",
                "体验DEMO": "DEMO",
                "企业版": "Enterprise Edition",
                "推荐": "Recommend",
                "适用于群组共享和高级权限": "Suitable for group sharing and advanced permissions",
                "定制版": "Custom Edition",
                "自定义": "Customize",
                "根据您的需求量身定制": "Tailored to your needs",
                "联系我们": "Contact us",
                "应用支持": "Application Support",
                "文档/知识库": "Document / Knowledge",
                "团队管理": "Team Management",
                "聊天": "To chat with",
                "国际化": "Globalization",
                "任务动态": "Dynamic task",
                "日程": "Agenda",
                "IM群聊": "IM Group Chat",
                "项目群聊": "Project group chat",
                "项目权限": "Project Permissions",
                "项目搜索": "Project Search",
                "任务类型": "Task Type",
                "知识库搜索": "Knowledge Base Search",
                "团队分组": "Team group",
                "分组权限": "Rights group",
                "成员统计": "Member Statistics",
                "签到功能": "Check-ins",
                "服务支持": "Service support",
                "自助支持": "Self-Support",
                "（Issues/文档/社群）": "(Issues / Document / Community)",
                "支持私有化部署": "Support the deployment of privatization",
                "绑定自有域名": "Binding own domain name",
                "二次开发": "Secondary development",
                "在线咨询支持": "Online consulting support",
                "电话咨询支持": "Telephone support",
                "中英文邮件支持": "Mail support in English",
                "一对一客户顾问": "One on one customer service.",
                "产品培训": "Product Training",
                "上门支持": "On-site support",
                "专属客户成功经理": "Dedicated customer success manager",
                "免费提供一次企业内训": "Corporate Training offers a free",
                "明星客户案例": "Star Customer Case",
                "多种部署方式随心选择": "A variety of deployment options to chose freely",
                "公有云": "Public cloud",
                "无需本地环境准备，按需购买帐户，专业团队提供运维保障服务，两周一次的版本迭代": "No local environment preparation, purchase account demand, professional team to provide operation and maintenance support services, bi-weekly version of the iteration",
                "私有云": "Private Cloud",
                "企业隔离的云服务器环境，高可用性，网络及应用层完整隔离，数据高度自主可控": "Enterprise cloud isolated server environments, high availability, network isolation and complete application layer, data is highly self-control",
                "本地服务器": "Local Server",
                "基于 Docker 的容器化部署，支持高可用集群，快速弹性扩展，数据高度自主可控": "Docker container-based deployment, support for high-availability clustering, rapid elasticity expanded data is highly self-control",
                "完善的服务支持体系": "Perfect service support system",
                "1:1客户成功顾问": "1: 1 Customer Success Consultant",
                "资深客户成功顾问对企业进行调研、沟通需求、制定个性化的解决方案，帮助企业落地": "Senior adviser to the success of enterprise customers to conduct research, communicate needs, develop customized solutions that help companies landing",
                "完善的培训体系": "A comprehensive training system",
                "根据需求定制培训内容，为不同角色给出专属培训方案，线上线下培训渠道全覆盖": "According to customized training needs, given exclusive training programs for different roles, training full coverage online and offline channels",
                "全面的支持服务": "Comprehensive support services",
                "多种支持服务让企业无后顾之忧，7*24 线上支持、在线工单、中英文邮件支持、上门支持": "A variety of support services allow enterprises worry-free, 7 * 24 online support, online ticket, in English and e-mail support, on-site support",
                "多重安全策略保护数据": "Multiple security policies to protect data",
                "高可用性保证": "High availability guarantee",
                "多重方式保证数据不丢失，高可用故障转移，异地容灾备份，99.99%可用性保证": "Multiple ways to ensure that data is not lost, high availability failover, offsite disaster recovery, 99.99% availability guarantee",
                "数据加密": "Data encryption",
                "多重方式保证数据不泄漏，基于 TLS 的数据加密传输，DDOS 防御和入侵检测": "Multiple manner to ensure data does not leak, based on TLS encrypted transmission data, intrusion detection and prevention DDOS",
                "帐户安全": "Account Security",
                "多重方式保证帐户安全，远程会话控制，设备绑定，安全日志以及手势密码": "Multiple ways to ensure account security, remote control session, binding equipment, security logs and gesture password",
                "如有任何问题，欢迎使用微信与我们联系。": "If you have any questions, please contact us using the micro-channel.",
            }
        });
    },

    mounted() {
        this.getSetting();
    },

    methods: {
        getSetting() {
            $A.apiAjax({
                url: 'system/setting',
                error: () => {
                    $A.storage("systemSetting", {});
                },
                success: (res) => {
                    if (res.ret === 1) {
                        this.systemConfig = res.data;
                        $A.storage("systemSetting", this.systemConfig);
                    } else {
                        $A.storage("systemSetting", {});
                    }
                }
            });
        },
    }
}
</script>
