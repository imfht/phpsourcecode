<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Client;
use app\model\HttpChannel as AppHttpChannel;
use Channel\Client as ChannelClient;
use think\Request;
use think\facade\View;

class HttpChannel extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $model_channel = AppHttpChannel::order('id desc');

        $keywords = $this->request->param('keywords');

        if(!empty($keywords)){
            $model_channel->where('name|comment|local_target_ip|local_target_port|domain|id','like',"%$keywords%");
        }
        
        $channel_list = $model_channel->paginate(10);
        
        View::assign('list',$channel_list);

        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
        $client_id = $this->request->param('client_id');
        if(empty($client_id)){
            return $this->error('请选择客户端','create',200,['setp'=>1]);
        }
        $model_client = Client::find($client_id);

        View::assign('client',$model_client);
        return view('create');

        
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //

        $post_data = $request->post();

        $model_channel = AppHttpChannel::create($post_data);


        ChannelClient::connect();

        ChannelClient::publish('new_http_channel',$model_channel);

        

        $this->success('添加成功','index');

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
        $model_channel = AppHttpChannel::find($id);

        View::assign('channel',$model_channel);

        return view();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //

        $post_data = $request->post();

        $model_channel = AppHttpChannel::find($id);

        $model_channel->save($post_data);

        ChannelClient::connect();

        ChannelClient::publish('new_http_channel',$model_channel);

        if($request->isAjax()){
            return json_message($model_channel->toArray());
        }

        return $this->success('更新成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
        AppHttpChannel::destroy($id);

        return json_message();
    }
}
