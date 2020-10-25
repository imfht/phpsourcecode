<?php
namespace app\controller;

use app\BaseController;
use app\model\Client;
use think\facade\View;

class Index extends Common
{
    public function index()
    {
        $model_client = Client::order('id desc');

        $keywords = $this->request->param('keywords');

        if(!empty($keywords)){
            $model_client->where('name|comment|key','like',"%$keywords%");
        }

        $client_list = $model_client->paginate(10);

        View::assign('client_list',$client_list);
        return view();
    }

    public function save()
    {
        $post_data = $this->request->post();

        if(empty($post_data['name'])){
            return json_message('名称不能为空');
        }

        $post_data['key'] = uniqid();
        $post_data['status'] = 0;
        
        Client::create($post_data);

        return json_message();
    }

    public function resetKey($id)
    {
        $model_client = Client::find($id);

        $model_client->key = uniqid();

        $model_client->save();

        return json_message($model_client->toArray());
    }

    public function update($id)
    {
        $model_client = Client::find($id);

        $post_data = $this->request->post();

        $model_client->save($post_data);

        return json_message($model_client->toArray());
    }

    public function delete($id)
    {
        Client::destroy($id);

        return json_message();
    }

    public function select()
    {
        $model_client = Client::order('id desc')->where('status',0);

        $keywords = $this->request->param('keywords');

        if(!empty($keywords)){
            $model_client->where('name|comment|key','like',"%$keywords%");
        }

        $client_list = $model_client->paginate(10);

        View::assign('client_list',$client_list);
        return view();
    }
}
