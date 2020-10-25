<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\File;
use App\Http\Controllers\Controller;
use App\Services\XmlRpc;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MetaWeblogController extends Controller
{
    private $client_id;

    public function index(Request $request)
    {
        $methods = [
            'blogger.getUsersBlogs' => 'getUsersBlogs',
            'blogger.deletePost' => 'deletePost',
            'metaWeblog.newPost' => 'newPost',
            'metaWeblog.editPost' => 'editPost',
            'metaWeblog.getPost' => 'getPost',
            'metaWeblog.getCategories' => 'getCategories',
            'metaWeblog.newMediaObject' => 'newMediaObject',
            'metaWeblog.getRecentPosts' => 'getRecentPosts',
        ];

        $this->client_id = $request->getClientIp();
        $request = $request->getContent();
        $method = null;
        $response = xmlrpc_decode_request($request, $method);

        [$appkey, $username, $password] = $response;

        if (! $this->authenticate($username, $password)) {
            $response = [
                'faultCode' => '2',
                'faultString' => 'Name or password is wrong, if you forget, please contect Administrator',
            ];
            XmlRpc::response($response, 'error');
            exit();
        }

        /*
         * 正常执行
         */
        if (isset($methods[$method])) {
            call_user_func_array([$this, $methods[$method]], [$method, $response]);
        } else {
            $this->methodNotFound($method);
        }
    }

    /**
     * 认证
     * @param $email
     * @param $password
     * @return bool
     */
    public function authenticate($email, $password)
    {
        if (\Auth::check() || \Auth::attempt(['email' => $email, 'password' => $password])) {
            return true;
        }

        return false;
    }

    /**
     * 获取博客信息.
     * @param $method
     * @param $params
     */
    public function getUsersBlogs($method, $params)
    {
        $response[0] = [
            'url' => url('/'),
            'blogid' => ! empty($appkey) ? $appkey : '1',
            'blogName' => 'Gryen-GTD',
        ];
        XmlRpc::response($response);
    }

    /**
     * 获取文章.
     * @param $method
     * @param $params
     */
    public function getPost($method, $params)
    {
        [$post_id] = $params;

        $response = [];

        $article = Article::find($post_id);

        $response['description'] = $article->withContent()->first()->content;
        $response['link'] = action('ArticlesController@show', ['id' => $post_id]);
        $response['mt_keywords'] = $article->tags;
        $response['dateCreated'] = $article->created_at;

        unset($article);

        XmlRpc::response($response);
    }

    /**
     * 创建文章.
     * @param $method
     * @param $params
     */
    public function newPost($method, $params)
    {
        [$blogid, $username, $password, $struct, $publish] = $params;

        $request = self::handleDescContent($this->transform($struct));

        /* 创建文章 */
        $article = Article::create($request);

        /* 标签处理 */
        Tag::createArticleTagProcess($request['tags'], $article->id);

        /* 更新文章内容 */
        $article->withContent()->create([
            'content' => $request['content'],
        ]);

        $this->creatorSuccess($article);
    }

    /**
     * 编辑文章.
     * @param $method
     * @param $params
     */
    public function editPost($method, $params)
    {
        [$post_id, $username, $password, $struct, $publish] = $params;

        $request = self::handleDescContent($this->transform($struct));

        $article = Article::withTrashed()
            ->find($post_id);
        if ($article->trashed()) {
            $article->restore();
        }

        $article->update($request);

        /* 更新文章内容 */
        $article->withContent()->update([
            'content' => $request['content'],
        ]);

        $this->creatorSuccess($article);
    }

    private function handleDescContent($request)
    {
        $description = trimAll(strip_tags(cutString('<description>', '</description>', $request['content'])));

        if (strlen($description) < 1) {
            $description = mb_strcut($request['content'], 0, 85, 'utf-8');
        }

        $request['description'] = $description;
//        $request['content'] = preg_replace('/<description>(.|\n)*<\/description>/', '', $request['content']);

        return $request;
    }

    /**
     * 删除文章.
     * @param $method
     * @param $params
     */
    public function deletePost($method, $params)
    {
        [$appKey, $postid, $username, $password, $publish] = $params;
        $res = Article::destroy($postid);

        XmlRpc::response($res > 0);
    }

    /**
     * TODO  获取最近推送的文章.
     * @param $method
     * @param $params
     */
    public function getRecentPosts($method, $params)
    {
        [$blogid, $username, $password, $numberOfPosts] = $params;

        XmlRpc::response($params);
    }

    /**
     * TODO 获取目录.
     * @param $method
     * @param $params
     */
    public function getCategories($method, $params)
    {
        $category = [];
        XmlRpc::response($category);
    }

    /**
     * TODO 创建目录.
     * @param $method
     * @param $params
     */
    public function newCategory($method, $params)
    {
        [$blog_id, $username, $password, $category] = $params;

        $categorys['id'] = 1;

        XmlRpc::response(intval($categorys->id));
    }

    /**
     * 上传图片.
     * @param $method
     * @param $params
     */
    public function newMediaObject($method, $params)
    {
        [$blogid, $username, $password, $struct] = $params;

        preg_match('/^(data:\s*image\/(\w+);base64,)/', $struct['bits']->scalar, $result);

        $tmpFilePath = base_path().'/storage/app/tmp/'.$struct['name'];

        $tmpFileCreated = file_put_contents($tmpFilePath, $struct['bits']->scalar);

        if ($tmpFileCreated) {
            $uploadFile = File::uploadSrvFile($tmpFilePath, $struct['name']);

            if (isset($uploadFile['file_path']) && $uploadFile['file_path'] !== null) {
                XmlRpc::response(['url' => $uploadFile['file_path']]);
            } else {
                $this->creatorFail('file upload cloud storage error!');
            }
        } else {
            $this->creatorFail('file save error!');
        }
    }

    /**
     * @param $struct: post_type|categories|title|
     * @return array
     */
    private function transform($struct)
    {
        $request = [];
        $request['title'] = $struct['title'];
        $request['cover'] = '';
        $request['tags'] = $struct['mt_keywords'];
        $request['content'] = $struct['description'];
        $request['status'] = $struct['post_status'] === 'draft' ? 0 : 1;
        $request['created_at'] = Carbon::createFromTimeString($struct['dateCreated']->scalar);
//        $request['post_type'] = $struct['post_type'];
//        $request->category_id = $category->id;
//        $request->user_id = \Auth::id();

        return $request;
    }

    /**
     * Method Not Found.
     * @param $methodName
     */
    protected function methodNotFound($methodName)
    {
        $response = [
            'faultCode' => '2',
            'faultString' => "The method you requested, '$methodName', was not found.",
        ];
        XmlRpc::response($response, 'error');
    }

    /**
     * Get Request Show Error Message.
     */
    public function errorMessage()
    {
        return response('XML-RPC server accepts POST requests only.');
    }

    /**
     * Observer creator Fail.
     * @param $error
     */
    public function creatorFail($error)
    {
        $response = [
            'faultCode' => '2',
            'faultString' => $error,
        ];
        XmlRpc::response($response, 'error');
    }

    /**
     * creator Success.
     * @param $model
     */
    public function creatorSuccess($model)
    {
        XmlRpc::response($model->id);
    }
}
