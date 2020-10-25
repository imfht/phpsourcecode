@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/admin.css'>
<link rel='stylesheet' href='/css/datatables.min.css'>
@endsection

@section('content')
<div ng-controller="AdminCtrl" class="ng-root">
    <div class='col-md-2'>
        <ul class='nav nav-pills nav-stacked admin-nav' role='tablist'>
            <li role='presentation' aria-controls="home" class='admin-nav-item active'><a href='#home'>后台首页</a></li>
            <li role='presentation' aria-controls="links" class='admin-nav-item'><a href='#links'>短链接</a></li>
            <li role='presentation' aria-controls="settings" class='admin-nav-item'><a href='#settings'>设 置</a></li>

            @if ($role == $admin_role)
            <li role='presentation' class='admin-nav-item'><a href='#admin'>管理员</a></li>
            @endif

            @if ($api_active == 1)
            <li role='presentation' class='admin-nav-item'><a href='#developer'>开发者</a></li>
            @endif
        </ul>
    </div>
    <div class='col-md-10'>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <h2>欢迎光临！</h2>
                <p>这里是后台首页，左边有导航，右上角是用户中心。</p>
            </div>

            <div role="tabpanel" class="tab-pane" id="links">
                @include('snippets.link_table', [
                    'table_id' => 'user_links_table'
                ])
            </div>

            <div role="tabpanel" class="tab-pane" id="settings">
                <h3>修改密码</h3>
                <form action='/admin/action/change_password' method='POST'>
                    老密码：<input class="form-control password-box" type='password' name='current_password' />
                    新密码：<input class="form-control password-box" type='password' name='new_password' />
                    <input type="hidden" name='_token' value='{{csrf_token()}}' />
                    <input type='submit' class='btn btn-success change-password-btn'/>
                </form>
            </div>

            @if ($role == $admin_role)
            <div role="tabpanel" class="tab-pane" id="admin">
                <h3>链接</h3>
                @include('snippets.link_table', [
                    'table_id' => 'admin_links_table'
                ])

                <h3 class="users-heading">用 户</h3>
                <a ng-click="state.showNewUserWell = !state.showNewUserWell" class="btn btn-primary btn-sm status-display">添加用户</a>

                <div ng-if="state.showNewUserWell" class="new-user-fields well">
                    <table class="table">
                        <tr>
                            <th>用户名</th>
                            <th>密 码</th>
                            <th>Email</th>
                            <th>权限组</th>
                            <th></th>
                        </tr>
                        <tr id="new-user-form">
                            <td><input type="text" class="form-control" ng-model="newUserParams.username"></td>
                            <td><input type="password" class="form-control" ng-model="newUserParams.userPassword"></td>
                            <td><input type="email" class="form-control" ng-model="newUserParams.userEmail"></td>
                            <td>
                                <select class="form-control new-user-role" ng-model="newUserParams.userRole">
                                    @foreach  ($user_roles as $role_text => $role_val)
                                        <option value="{{$role_val}}">{{$role_text}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <a ng-click="addNewUser($event)" class="btn btn-primary btn-sm status-display new-user-add">添加</a>
                            </td>
                        </tr>
                    </table>
                </div>

                @include('snippets.user_table', [
                    'table_id' => 'admin_users_table'
                ])

            </div>
            @endif

            @if ($api_active == 1)
            <div role="tabpanel" class="tab-pane" id="developer">
                <h3>开发者</h3>

                <p>API keys and documentation for developers.</p>
                <p>
                    Documentation:
                    <a href='http://docs.polr.me/en/latest/developer-guide/api/'>http://docs.polr.me/en/latest/developer-guide/api/</a>
                </p>

                <h4>API Key: </h4>
                <div class='row'>
                    <div class='col-md-8'>
                        <input class='form-control status-display' disabled type='text' value='{{$api_key}}'>
                    </div>
                    <div class='col-md-4'>
                        <a href='#' ng-click="generateNewAPIKey($event, '{{$user_id}}', true)" id='api-reset-key' class='btn btn-danger'>Reset</a>
                    </div>
                </div>


                <h4>API Quota: </h4>
                <h2 class='api-quota'>
                    @if ($api_quota == -1)
                        unlimited
                    @else
                        <code>{{$api_quota}}</code>
                    @endif
                </h2>
                <span> requests per minute</span>
            </div>
            @endif
        </div>
    </div>

    <div class="angular-modals">
        <edit-long-link-modal ng-repeat="modal in modals.editLongLink" link-ending="modal.linkEnding"
            old-long-link="modal.oldLongLink" clean-modals="cleanModals"></edit-long-link-modal>
        <edit-user-api-info-modal ng-repeat="modal in modals.editUserApiInfo" user-id="modal.userId"
            api-quota="modal.apiQuota" api-active="modal.apiActive" api-key="modal.apiKey"
            generate-new-api-key="generateNewAPIKey" clean-modals="cleanModals"></edit-user-api-info>
    </div>
</div>


@endsection

@section('js')
{{-- Include modal templates --}}
@include('snippets.modals')

{{-- Include extra JS --}}
<script src='/js/datatables.min.js'></script>
<script src='/js/api.js'></script>
<script src='/js/AdminCtrl.js'></script>
@endsection
