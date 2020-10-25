<?php

/**
 * Class MessageController
 * 私信控制器
 */
class MessageController extends \BaseController
{
    public function __construct()
    {
        /**
         * 对私信相关post或put请求进行数据过滤
         */
        \Libraries\MarkDownPurifier::purify(['content']);
    }
    /**
     * 返回用户自己所发送的，以及希望被接收的信息.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * 由于私信分页时区分接受和发送，这里根据前端请求区分返回的数据格式
     * 所返回的JSON格式：
     * {
     *   "received":[
     *   {
     *     "id": "主键id",
     *     "title": "私信的主题",
     *     "content": "私信的内容",
     *     "created_at": "创建时间",
     *     "read": "是否阅读",
     *     "sender_id": "发送的用户id",
     *     "sender_username": "发送的用户名",
     *     "sender_email": "发送的用户电子邮件"，
     *     "sender_head_image": "发送者的头像"
     *   }
     *   ]
     * }
     * 或
     * {
     *   "sent":[
     *   {
     *     "id": "主键id",
     *     "title": "私信的主题",
     *     "receiver_id": "接受者的id",
     *     "receiver_username": "接受者的用户名",
     *     "receiver_email": "接受者的电子邮箱地址",
     *     "receiver_head_image": "接收者的头像"
     *     "content": "私信的内容",
     *     "created_at": "创建时间",
     *     "read": "是否阅读",
     *   }
     *   ]
     * }
     *
     */
    public function index()
    {
        $currUserId = Auth::user()['id'];
        $data['received'] = MessageInbox::getUserMessages($currUserId);
        $data['sent'] = Message::getUserSentMessages($currUserId);
        $pagination = Paginate::paginateArray($data[Input::get('option', 'received')]);

        return Response::json($pagination, 200, [], JSON_NUMERIC_CHECK);
    }


    /**
     * 新建私信.
     *
     * 希望接收到的JSON格式：
     * {
     *      receiver_id: "待接受的用户id或电子邮箱或用户名，也可以是前述数据组成的数组"
     *      title: "私信的标题",
     *      content: "私信的内容"
     * }
     *
     * 当出错时，返回：
     * {
     *      error: "有关的错误信息"
     * }
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store()
    {
        $postData = Input::all();
        $validator = $this->getStoreValidator($postData);
        $newMessage = null;
        if( $validator->passes() ){

            DB::transaction(function()use($postData, &$newMessage){
                $msgData = array_only($postData, ['title', 'content']);
                $msgData['sender_id'] = Auth::user()['id'];
                $addMsg = $newMessage = Message::create($msgData);

                if(is_array($postData['receiver_id']) ){
                    $receivers = $postData['receiver_id'];
                } else {
                    $receivers[] = $postData['receiver_id'];
                }

                $msgInboxData = [
                    'message_id'=>$addMsg['id'],
                ];
                $formatReceiver = null;
                array_map(function($receiver) use(&$formatReceiver) {

                    //将用户标识数据格式化为id形式
                    $formatReceiver[] = User::select('id')
                        ->where('id', $receiver)
                        ->orWhere('username', $receiver)
                        ->orWhere('email', $receiver)
                        ->get()
                        ->toArray()[0]['id'];
                }, $receivers);
                $receivers = $formatReceiver;

                foreach($receivers as $currReceiver ){
                    $msgInboxData['receiver_id'] = $currReceiver;
                    MessageInbox::create($msgInboxData);
                }

            });

            return Response::json([
                'id' => $newMessage['id']
            ]);

        } else {
            return Response::make(
                [
                    'error'=>$this->changeValidatorMessageToString($validator->messages())
                ],
                403
            );
        }
    }

    /**
     * 生成新建信息所使用的校验器
     * @param $postData
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($postData)
    {
        $rules = [
            'receiver_id' => 'required',
            'title' => 'required',
            'content' => 'required'
        ];

        return Validator::make($postData, $rules);
    }

    /**
     * 更改私信的已读状态为已读.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $targetMsg = MessageInbox::where('id', $id)
            ->where('receiver_id', Auth::user()['id'])
            ->firstOrFail();

        $targetMsg->update(['read'=>true]);

        return Response::make('', 200);
    }

    /**
     * 暂不启用
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        return Response::make('Forbidden', 403);
    }

}
